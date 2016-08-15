INSTALLATION & CONFIGURATION
---

To install framework use [Composer](https://getcomposer.org/) `composer install` command with `composer.json` configuration file.

```js
{
    "minimum-stability": "dev",
    "require": {
        "php": ">=5.4.0",
		"asymptix/framework": ">=2.0.0"
    }
}
```

If you don't want load all framework files, you can load only framework core libs with `composer.json` configuration file.

```js
{
    "repositories": [
        {
            "type": "package",
            "package": {
                "name": "asymptix/framework",
                "version": "dev-master",
                "source": {
                    "type": "svn",
                    "url": "https://github.com/Asymptix/Framework",
                    "reference": "trunk/framework"
                },
                "require": {
                    "php": ">=5.4.0"
                },
                "autoload": {
                    "psr-4": {
                        "Asymptix\\": ""
                    }
                }
            }
        }
    ],
    "minimum-stability": "dev",
    "require": {
        "php": ">=5.4.0",
        "asymptix/framework": ">=2.0.0"
    }
}
```

After [Composer](https://getcomposer.org/) installation you just need require autoload file with the next command:

```php
require_once("./vendor/autoload.php");
```
