# Asymptix PHP Framework

The Fast and Easy PHP Framework for Rapid Development.

[![Total Downloads](https://poser.pugx.org/asymptix/framework/downloads)](https://packagist.org/packages/asymptix/framework)
[![Latest Stable Version](https://poser.pugx.org/asymptix/framework/v/stable)](https://packagist.org/packages/asymptix/framework)
[![Latest Unstable Version](https://poser.pugx.org/asymptix/framework/v/unstable)](https://packagist.org/packages/asymptix/framework)
[![License](https://poser.pugx.org/asymptix/framework/license)](https://packagist.org/packages/asymptix/framework)
[![StyleCI](https://styleci.io/repos/31887470/shield)](https://styleci.io/repos/31887470)
[![Build Status](https://travis-ci.org/Asymptix/Framework.svg?branch=master)](https://travis-ci.org/Asymptix/Framework)
[![Build Status](https://scrutinizer-ci.com/g/Asymptix/Framework/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Asymptix/Framework/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Asymptix/Framework/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Asymptix/Framework/?branch=master)
[![Code Climate](https://codeclimate.com/github/Asymptix/Framework/badges/gpa.svg)](https://codeclimate.com/github/Asymptix/Framework)

DIRECTORY STRUCTURE
---

```
classes/             example project classes
classes/db/          example DB beans classes
conf/                configuration files
controllers/         example controllers
framework/           core framework code
modules/             example modules
templates/           example templates
tests/               tests of the core framework code
```

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

LIST OF GLOBAL VARIABLES
---

### Basic static settings
* __~~$\_CONFIG~~__ - List of pairs key → value for global system configuration, like DB connections parameters, site URL, admin e-mail, etc. May be in old versions of the framework, now changed on __Config__ object and partly replaced with __$\_SETTINGS__ variable _(deprecated)_.
* __$\_SETTINGS__ -	List of pairs key → value for global system settings. Can be taken from the database or manually created.
* __$\_MENU__ -	Multi-level array with main menu structure. Can be as global variable or as static property of the __Menu__ class.
* __~~$\_PATH~~__ -	Old version to set absolute path to the project's folder _(deprecated)_.

### Session variables
* __$\_USER__ - Variable stores serialized object of the __User__ class or simple array with user data.

### Pages view and representation
* __$\_ROUTE__ - Global instance of the __Route__ class with public properties controller, action and id - represents current page URL or rules to display this page.
* __$\_TPL__	- String variable with a path to the needed template of the page.
* __$\_BREADCRUMBS__ - Array with page breadcrumbs data.
* __$\_JS__ - Controller local array with paths to needed for the current page JavaScript files.
* __$\_CSS__	- Controller local array with paths to needed for the current page CSS files.
* __$\_LANG__ - `Language` object stored in session and represents current selected language in localization functionality.

### Form submission data
* __$\_FIELDS__ - List with pairs key → value merged from __$\_REQUEST__ (__$\_POST__ and __$\_GET__) also used in form output if some data is invalid. Fields values may be changed on the way from data receiving before output in form fields.
* __$\_ARGS__ -	List with pairs key → value from __$\_GET__ string, so it's not a copy of __$\_GET__ but received from URL string data after ‘?’ sign.
* __$\_ERRORS__	List of the errors for invalid or notable fields after validation process or some notification process.
* __$\_MESSAGES__	List of messages shown after form submission if some errors or notifications. Not connected to fields but common for all forms.
* __$\_FILTER__	List of filters (data selection rules) for some forms.

### Email functionality
* __$\_EMAIL__ - Uses only in e-mail templates as e-mail inline parameters list (e.g. username, password or product name and price in e-mail templates).

IDENTITY
---

It's highly recommended to send header information with framework signature for better recognition with parsers and analyzers (like [Wappalyzer](https://github.com/AliasIO/Wappalyzer)).
You may see example in the __index.php__ file:

```php
header('X-Powered-By: Asymptix PHP Framework, PHP/' . phpversion());
```

WORK WITH DATABASE (ORM)
---

You can create new __DBObject__:

```php
$user = new User();
```

or you can get __DBObject__ with __DBSelector__:

```php
$userSelector = new DBSelector(new User());
$user = $userSelector->selectDBObjectById($userId);
```

You can manipulate with DataBase records this way:

```php
// Save (insert/update) record
$user->email = "dmytro@asymptix.com";
$user->save();
```

If ID of the __DBObject__ is empty - then __INSERT__ SQL instruction will be executed, if not empty - then __UPDATE__.

```php
// Delete record
$user->delete();
```

You can also use this syntax for the fast selection queries:

```php
// Init object
$site = new Site();
$sitesList = $site->select(array('status' => "active"))
                  ->order(array('title' => "ASC"))
                  ->limit(10)
                  ->go();
```

This query will be executed with using of Prepared Statement. Order of methods calls is free but go() - must be the last call in this order.

LOG WITH OutputStream
---

```php
require_once("./core/OutputStream.php");

OutputStream::start();

OutputStream::output("Simpel text \n");
OutputStream::line("Simpel text line");
OutputStream::line("Simpel text line");

OutputStream::line();

OutputStream::log("Simple default log");
OutputStream::log("Simple log with time format", "\(H:i:s\)");
OutputStream::log("Simple log with time {{time}} label");
OutputStream::log("Simple log with time {{time}} label and format", "\(H:i:s\)");
OutputStream::log("Log with few time {{time}} labels {{time}}");

OutputStream::line();

OutputStream::msg(OutputStream::MSG_INFO, "Info message");
OutputStream::msg(OutputStream::MSG_DEBUG, "Debug message with time format", "\(H:i:s\)");
OutputStream::msg(OutputStream::MSG_SUCCESS, "Success message with time {{time}} label");
OutputStream::msg(OutputStream::MSG_WARNING, "Warning message with time {{time}} label and format", "\(H:i:s\)");
OutputStream::msg(OutputStream::MSG_ERROR, "Default Error message");

OutputStream::close();
```

Copyright (c) 2016 Asymptix.
