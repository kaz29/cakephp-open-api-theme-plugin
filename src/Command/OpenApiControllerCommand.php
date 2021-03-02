<?php
declare(strict_types=1);

namespace OpenApiTheme\Command;

use Bake\Command\ControllerCommand;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\ORM\Table;
use OpenApiTheme\Utility\TemplateRenderer;

/**
 * OpenApiController command.
 */
class OpenApiControllerCommand extends ControllerCommand
{
    /**
     * Generate the controller code
     *
     * @param string $controllerName The name of the controller.
     * @param array $data The data to turn into code.
     * @param \Cake\Console\Arguments $args The console args
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void
     */
    public function bakeController(string $controllerName, array $data, Arguments $args, ConsoleIo $io): void
    {
        $data += [
            'name' => null,
            'namespace' => null,
            'prefix' => null,
            'actions' => null,
            'helpers' => null,
            'components' => null,
            'plugin' => null,
            'pluginPath' => null,
            'propertySchema' => $this->getEntityPropertySchema($controllerName),
        ];

        $renderer = new TemplateRenderer($this->theme);
        $renderer->set($data);

        $contents = $renderer->generate('Bake.Controller/controller');

        $path = $this->getPath($args);
        $filename = $path . $controllerName . 'Controller.php';
        $io->createFile($filename, $contents, $args->getOption('force'));
    }

    public function getEntityPropertySchema($controllerName): array
    {
        $currentModelName = $controllerName;
        $plugin = $this->plugin;
        if ($plugin) {
            $plugin .= '.';
        }

        if ($this->getTableLocator()->exists($plugin . $currentModelName)) {
            $model = $this->getTableLocator()->get($plugin . $currentModelName);
        } else {
            $model = $this->getTableLocator()->get($plugin . $currentModelName, [
                'connectionName' => $this->connection,
            ]);
        }
        
        $properties = [];

        $schema = $model->getSchema();
        foreach ($schema->columns() as $column) {
            $columnSchema = $schema->getColumn($column);

            $properties[$column] = [
                'kind' => 'column',
                'type' => $columnSchema['type'],
                'null' => $columnSchema['null'],
                'comment' => $columnSchema['comment'],
            ];
        }

        foreach ($model->associations() as $association) {
            $entityClass = '\\' . ltrim($association->getTarget()->getEntityClass(), '\\');

            if ($entityClass === '\Cake\ORM\Entity') {
                $namespace = Configure::read('App.namespace');

                [$plugin, ] = pluginSplit($association->getTarget()->getRegistryAlias());
                if ($plugin !== null) {
                    $namespace = $plugin;
                }
                $namespace = str_replace('/', '\\', trim($namespace, '\\'));

                $entityClass = $this->_entityName($association->getTarget()->getAlias());
                $entityClass = '\\' . $namespace . '\Model\Entity\\' . $entityClass;
            }

            $properties[$association->getProperty()] = [
                'kind' => 'association',
                'association' => $association,
                'type' => $entityClass,
            ];
        }

        return $properties;
    }
}
