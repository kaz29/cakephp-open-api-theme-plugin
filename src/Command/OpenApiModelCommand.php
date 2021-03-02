<?php
declare(strict_types=1);

namespace OpenApiTheme\Command;

use Bake\Command\ModelCommand as BaseModelCommand;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\ORM\Table;
use OpenApiTheme\Utility\TemplateRenderer;

class OpenApiModelCommand extends BaseModelCommand
{
    /**
     * Returns an entity property "schema".
     *
     * The schema is an associative array, using the property names
     * as keys, and information about the property as the value.
     *
     * The value part consists of at least two keys:
     *
     * - `kind`: The kind of property, either `column`, which indicates
     * that the property stems from a database column, or `association`,
     * which identifies a property that is generated for an associated
     * table.
     * - `type`: The type of the property value. For the `column` kind
     * this is the database type associated with the column, and for the
     * `association` type it's the FQN of the entity class for the
     * associated table.
     *
     * For `association` properties an additional key will be available
     *
     * - `association`: Holds an instance of the corresponding association
     * class.
     *
     * @param \Cake\ORM\Table $model The model to introspect.
     * @return array The property schema
     */
    public function getEntityPropertySchema(Table $model): array
    {
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

    /**
     * Bake an entity class.
     *
     * @param \Cake\ORM\Table $model Model name or object
     * @param array $data An array to use to generate the Table
     * @param \Cake\Console\Arguments $args CLI Arguments
     * @param \Cake\Console\ConsoleIo $io CLI io
     * @return void
     */
    public function bakeEntity(Table $model, array $data, Arguments $args, ConsoleIo $io): void
    {
        if ($args->getOption('no-entity')) {
            return;
        }
        $name = $this->_entityName($model->getAlias());

        $namespace = Configure::read('App.namespace');
        $pluginPath = '';
        if ($this->plugin) {
            $namespace = $this->_pluginNamespace($this->plugin);
            $pluginPath = $this->plugin . '.';
        }

        $data += [
            'name' => $name,
            'namespace' => $namespace,
            'plugin' => $this->plugin,
            'pluginPath' => $pluginPath,
            'primaryKey' => [],
        ];

        $renderer = new TemplateRenderer($this->theme);
        $renderer->set($data);
        $out = $renderer->generate('Bake.Model/entity');

        $path = $this->getPath($args);
        $filename = $path . 'Entity' . DS . $name . '.php';
        $io->out("\n" . sprintf('Baking entity class for %s...', $name), 1, ConsoleIo::QUIET);
        $io->createFile($filename, $out, $args->getOption('force'));

        $emptyFile = $path . 'Entity' . DS . '.gitkeep';
        $this->deleteEmptyFile($emptyFile, $io);
    }
}
