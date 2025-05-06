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
     * @var array
     */
    private $loadedClasses = [];

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

        $this->cleanupTestFiles();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Configure::delete('App.namespace');
        Plugin::getCollection()->remove('OpenApiTheme');
        $this->cleanupTestFiles();
    }

    /**
     * テストファイルをクリーンアップ
     */
    private function cleanupTestFiles(): void
    {
        $testFiles = glob(TEST_APP . 'Controller' . DS . '*Controller.php');
        $testAdminFiles = glob(TEST_APP . 'Controller' . DS . 'Admin' . DS . '*Controller.php');
        
        foreach (array_merge($testFiles ?? [], $testAdminFiles ?? []) as $file) {
            // AppController.phpは削除しない
            if (basename($file) === 'AppController.php') {
                continue;
            }
            
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $adminDir = TEST_APP . 'Controller' . DS . 'Admin';
        if (is_dir($adminDir) && count(glob($adminDir . '/*')) === 0) {
            rmdir($adminDir);
        }
    }

    /**
     * クラスをアンロードする
     *
     * @param string $className
     * @return void
     */
    private function removeClass(string $className): void
    {
        $classExists = class_exists($className, false);
        if ($classExists) {
            $class = new ReflectionClass($className);
            $fileName = $class->getFileName();
            
            if ($fileName && is_file($fileName)) {
                opcache_invalidate($fileName, true);
                opcache_reset();
            }
        }
    }

    /**
     * クラスをロードする
     *
     * @param string $controllerPath
     * @param string $className
     * @return ReflectionClass
     */
    private function loadControllerClass(string $controllerPath, string $className): ReflectionClass
    {
        if (class_exists($className, false)) {
            $this->removeClass($className);
        }

        require $controllerPath;
        $this->loadedClasses[] = $className;
        
        return new ReflectionClass($className);
    }

    /**
     * Test basic controller baking
     *
     * @return void
     */
    public function testBasicBaking(): void
    {
        $this->exec('bake controller BasicArticles --connection default --theme OpenApiTheme --no-test');
        $this->assertExitSuccess();
        $this->assertOutputContains('Baking controller class for BasicArticles');
        $this->assertOutputContains('<success>Wrote</success>');

        $controllerPath = TEST_APP . 'Controller' . DS . 'BasicArticlesController.php';
        $this->assertFileExists($controllerPath);
        
        $className = 'TestApp\\Controller\\BasicArticlesController';
        $reflClass = $this->loadControllerClass($controllerPath, $className);
        
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
        $this->exec('bake controller PrefixArticles --prefix Admin --connection default --theme OpenApiTheme --no-test');
        $this->assertExitSuccess();
        $this->assertOutputContains('Baking controller class for PrefixArticles');
        $this->assertOutputContains('<success>Wrote</success>');

        $controllerPath = TEST_APP . 'Controller' . DS . 'Admin' . DS . 'PrefixArticlesController.php';
        $this->assertFileExists($controllerPath);
        
        $className = 'TestApp\\Controller\\Admin\\PrefixArticlesController';
        $reflClass = $this->loadControllerClass($controllerPath, $className);
        
        $indexMethod = $reflClass->getMethod('index');
        $this->assertNotEmpty(
            $indexMethod->getAttributes(Get::class),
            'Admin/indexメソッドにGetアトリビュートが存在しません'
        );

        $viewMethod = $reflClass->getMethod('view');
        $this->assertNotEmpty(
            $viewMethod->getAttributes(Get::class),
            'Admin/viewメソッドにGetアトリビュートが存在しません'
        );

        $addMethod = $reflClass->getMethod('add');
        $this->assertNotEmpty(
            $addMethod->getAttributes(Post::class),
            'Admin/addメソッドにPostアトリビュートが存在しません'
        );

        $editMethod = $reflClass->getMethod('edit');
        $this->assertNotEmpty(
            $editMethod->getAttributes(Put::class),
            'Admin/editメソッドにPutアトリビュートが存在しません'
        );

        $deleteMethod = $reflClass->getMethod('delete');
        $this->assertNotEmpty(
            $deleteMethod->getAttributes(Delete::class),
            'Admin/deleteメソッドにDeleteアトリビュートが存在しません'
        );
    }

    /**
     * Test baking with actions
     *
     * @return void
     */
    public function testBakeWithActions(): void
    {
        $this->exec('bake controller ActionArticles --actions index,view,add --connection default --theme OpenApiTheme --no-test');
        $this->assertExitSuccess();
        $this->assertOutputContains('Baking controller class for ActionArticles');
        $this->assertOutputContains('<success>Wrote</success>');

        $controllerPath = TEST_APP . 'Controller' . DS . 'ActionArticlesController.php';
        $this->assertFileExists($controllerPath);
        
        $className = 'TestApp\\Controller\\ActionArticlesController';
        $reflClass = $this->loadControllerClass($controllerPath, $className);
        
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

        $this->assertFalse(
            method_exists($className, 'edit'),
            'editメソッドが存在してはいけません'
        );
        $this->assertFalse(
            method_exists($className, 'delete'),
            'deleteメソッドが存在してはいけません'
        );
    }

    /**
     * Test baking with no actions
     *
     * @return void
     */
    public function testBakeWithNoActions(): void
    {
        $this->exec('bake controller NoActionArticles --no-actions --connection default --theme OpenApiTheme --no-test');
        $this->assertExitSuccess();
        $this->assertOutputContains('Baking controller class for NoActionArticles');
        $this->assertOutputContains('<success>Wrote</success>');

        $controllerPath = TEST_APP . 'Controller' . DS . 'NoActionArticlesController.php';
        $this->assertFileExists($controllerPath);
        
        $className = 'TestApp\\Controller\\NoActionArticlesController';
        $reflClass = $this->loadControllerClass($controllerPath, $className);
        
        $this->assertFalse(
            method_exists($className, 'index'),
            'indexメソッドが存在してはいけません'
        );
        $this->assertFalse(
            method_exists($className, 'view'),
            'viewメソッドが存在してはいけません'
        );
        $this->assertFalse(
            method_exists($className, 'add'),
            'addメソッドが存在してはいけません'
        );
        $this->assertFalse(
            method_exists($className, 'edit'),
            'editメソッドが存在してはいけません'
        );
        $this->assertFalse(
            method_exists($className, 'delete'),
            'deleteメソッドが存在してはいけません'
        );
    }
}
