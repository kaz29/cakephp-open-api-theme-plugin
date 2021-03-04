# OpenApiTheme plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:

```
composer require kaz29/cakephp-open-api-theme-plugin --dev
```

## Dependency

- [Crud plugin](https://github.com/FriendsOfCake/crud)
- [swagger-php](https://github.com/zircote/swagger-php)

## Setup

- Copy `swagger-settings.php` to config directory.
- Describe the settings of your application in `swagger-settings.php`.
- Add `"build:swagger" : "build-swagger-json"` into your app's composer.json scripts section.

    ```
    "scripts": {
        "post-install-cmd": "App\\Console\\Installer::postInstall",
        "post-create-project-cmd": "App\\Console\\Installer::postInstall",
        "check": [
            "@test",
            "@cs-check"
        ],
        "cs-check": "phpcs --colors -p  src/ tests/",
        "cs-fix": "phpcbf --colors -p src/ tests/",
        "stan": "phpstan analyse",
        "test": "phpunit --colors=always",
        "build:swagger" : "build-swagger-json"  // <-- add this line
    },
    ```

- Add the following configuration to the end of config/bootstrap_cli.php.

```
Configure::write('Bake.theme', 'OpenApiTheme');
```

- Change Application.php as follows.

```
    protected function bootstrapCli(): void
    {
        try {
            $this->addPlugin('Bake');
        } catch (MissingPluginException $e) {
            // Do not halt if the plugin is missing
        }

        $this->addPlugin('Migrations');

        // Load more plugins here
        $this->addPlugin('OpenApiTheme');  // Add this line
    }
```

## Usage example

### bake controller

```
$ bin/cake bake open_api_controller Articles --prefix Api
```

### bake model

```
$ bin/cake bake open_api_model Articles
```

### create swagger.json

```
$ composer build:swagger
```

## Baked example

A sample of the bake of the [CakePHP CMS Tutorial](https://book.cakephp.org/4/en/tutorials-and-examples/cms/installation.html) can be found in the [example directory](example/).

- [Swagger UI example](https://petstore.swagger.io/?url=https://raw.githubusercontent.com/kaz29/cakephp-open-api-theme-plugin/master/example/swagger.json)

## Author

Kazuhiro Watanabe - cyo [at] mac.com - [https://twitter.com/kaz_29](https://twitter.com/kaz_29)

## License

OpenApiTheme plugin for CakePHP is licensed under the MIT License - see the LICENSE file for details
