# Asymptix PHP Framework

The Fast and Easy PHP Framework for Rapid Development.

[![Total Downloads](https://poser.pugx.org/asymptix/framework/downloads)](https://packagist.org/packages/asymptix/framework)
[![Latest Stable Version](https://poser.pugx.org/asymptix/framework/v/stable)](https://packagist.org/packages/asymptix/framework)
[![Latest Unstable Version](https://poser.pugx.org/asymptix/framework/v/unstable)](https://packagist.org/packages/asymptix/framework)
[![StyleCI](https://styleci.io/repos/31887470/shield)](https://styleci.io/repos/31887470)
[![Build Status](https://travis-ci.org/Asymptix/Framework.svg?branch=master)](https://travis-ci.org/Asymptix/Framework)
[![Build Status](https://scrutinizer-ci.com/g/Asymptix/Framework/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Asymptix/Framework/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Asymptix/Framework/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Asymptix/Framework/?branch=master)
[![Code Climate](https://codeclimate.com/github/Asymptix/Framework/badges/gpa.svg)](https://codeclimate.com/github/Asymptix/Framework)
[![License](https://poser.pugx.org/asymptix/framework/license)](https://packagist.org/packages/asymptix/framework)

REQUIREMENTS
---

The minimum requirement by Asymptix Framework is that your Web server supports PHP 5.4.

INSTALLATION
---

To install basic framework libs use [Composer](https://getcomposer.org/) `composer install` command with `composer.json` configuration file.

```js
{
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

IDENTITY
---

It's highly recommended to send header information with framework signature for better recognition with parsers and analyzers (like [Wappalyzer](https://github.com/AliasIO/Wappalyzer)).
You may see example in the __index.php__ file:

```php
header('X-Powered-By: Asymptix PHP Framework, PHP/' . phpversion());
```

Copyright (c) 2009-2017 Asymptix.
