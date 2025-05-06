<?php
declare(strict_types=1);

namespace OpenApiTheme\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Put;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Tag;
use OpenApi\Attributes\Response;
use ReflectionClass;
use ReflectionMethod;

/**
 * OpenApiTheme\Command\OpenApiControllerCommand Test Case
 *
 * @uses \OpenApiTheme\Command\OpenApiControllerCommand
 */
class OpenApiControllerCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->setAppNamespace('TestApp');
        
        Configure::write('App.namespace', 'TestApp');
        Plugin::getCollection()->add(new \OpenApiTheme\Plugin());

        // TwigViewの設定
        if (!defined('Cake\TwigView\View\CACHE')) {
            define('Cake\TwigView\View\CACHE', TMP . 'twig' . DS);
        }

        // テスト用のコントローラファイルを削除
        $paths = [
            TEST_APP . 'Controller' . DS . 'ArticlesController.php',
            TEST_APP . 'Controller' . DS . 'Admin' . DS . 'ArticlesController.php',
        ];
        foreach ($paths as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
            // Adminディレクトリが存在する場合は、空のディレクトリも削除
            $adminDir = dirname($path);
            if (basename($adminDir) === 'Admin' && is_dir($adminDir) && count(glob($adminDir . '/*')) === 0) {
                rmdir($adminDir);
            }
        }
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Configure::delete('App.namespace');
        Plugin::getCollection()->remove('OpenApiTheme');
    }

    /**
     * Test basic controller baking
     *
     * @return void
     */
    public function testBasicBaking(): void
    {
        $this->exec('bake controller Articles --connection default --theme OpenApiTheme --no-test');
        $this->assertExitSuccess();
        $this->assertOutputContains('Baking controller class for Articles');
        $this->assertOutputContains('<success>Wrote</success>');
        $this->assertOutputContains(TEST_APP . 'Controller' . DS . 'ArticlesController.php');

        $controllerPath = TEST_APP . 'Controller' . DS . 'ArticlesController.php';
        $this->assertFileExists($controllerPath);
        
        require_once $controllerPath;
        $className = 'TestApp\\Controller\\ArticlesController';
        
        $reflClass = new ReflectionClass($className);
        $indexMethod = $reflClass->getMethod('index');
        $this->assertNotEmpty(
            $indexMethod->getAttributes(Get::class),
            'indexメソッドにGetアトリビュートが存在しません'
        );

        $viewMethod = $reflClass->getMethod('view');
        $this->assertNotEmpty(
            $viewMethod->getAttributes(Get::class),
            'viewメソッドにGetアトリビュートが存在しません'
        );

        $addMethod = $reflClass->getMethod('add');
        $this->assertNotEmpty(
            $addMethod->getAttributes(Post::class),
            'addメソッドにPostアトリビュートが存在しません'
        );

        $editMethod = $reflClass->getMethod('edit');
        $this->assertNotEmpty(
            $editMethod->getAttributes(Put::class),
            'editメソッドにPutアトリビュートが存在しません'
        );

        $deleteMethod = $reflClass->getMethod('delete');
        $this->assertNotEmpty(
            $deleteMethod->getAttributes(Delete::class),
            'deleteメソッドにDeleteアトリビュートが存在しません'
        );
    }

    /**
     * Test baking with prefix
     *
     * @return void
     */
    public function testBakeWithPrefix(): void
    {
        $this->exec('bake controller Articles --prefix Admin --connection default --theme OpenApiTheme --no-test');
        $this->assertExitSuccess();
        $this->assertOutputContains('Baking controller class for Articles');
        $this->assertOutputContains('<success>Wrote</success>');
        $this->assertOutputContains(TEST_APP . 'Controller' . DS . 'Admin' . DS . 'ArticlesController.php');
    }

    /**
     * Test baking with actions
     *
     * @return void
     */
    public function testBakeWithActions(): void
    {
        $this->exec('bake controller Articles --actions index,view,add --connection default --theme OpenApiTheme --no-test');
        $this->assertExitSuccess();
        $this->assertOutputContains('Baking controller class for Articles');
        $this->assertOutputContains('<success>Wrote</success>');
        $this->assertOutputContains(TEST_APP . 'Controller' . DS . 'ArticlesController.php');
    }

    /**
     * Test baking with no actions
     *
     * @return void
     */
    public function testBakeWithNoActions(): void
    {
        $this->exec('bake controller Articles --no-actions --connection default --theme OpenApiTheme --no-test');
        $this->assertExitSuccess();
        $this->assertOutputContains('Baking controller class for Articles');
        $this->assertOutputContains('<success>Wrote</success>');
        $this->assertOutputContains(TEST_APP . 'Controller' . DS . 'ArticlesController.php');
    }
}
