# OpenApiTheme plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:

```
composer require kaz29/cakephp-open-api-theme-plugin --dev
```

## Setup

- Copy `swagger-settings.php` to config directory.
- Describe the settings of your application in `swagger-settings.php`.
- Copy `swagger.php` to bin directory.
- Grant execution permissions to `swagger.php`.
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
$ bin/swagger.php
```
