<?php
declare(strict_types=1);

namespace TestApp;

use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\RouteBuilder;

class Application extends BaseApplication
{
    public function bootstrap(): void
    {
        parent::bootstrap();

        $this->addPlugin('Bake');
        $this->addPlugin('OpenApiTheme');
    }

    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        return $middlewareQueue;
    }

    public function routes(RouteBuilder $routes): void
    {
        $routes->scope('/', function (RouteBuilder $builder): void {
            $builder->fallbacks();
        });
        parent::routes($routes);
    }
} 