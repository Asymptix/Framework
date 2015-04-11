![Asymptix](https://media.licdn.com/media/AAEAAQAAAAAAAAK-AAAAJDVhMDMzNDIxLWMzOTktNDhhNS04YWFjLWZmMjQ0Mzc1NDE4Ng.png)

# Asymptix PHP Framework
The Fast and Easy PHP Framework for Rapid Development.

### List of global variables

##### Basic static settings
* __~~$\_CONFIG~~__ - List of pairs key → value for global system configuration, like DB connections parameters, site URL, admin e-mail, etc. May be in old versions of the framework, now changed on __Config__ object and partly replaced with __$\_SETTINGS__ variable _(deprecated)_.
* __$\_SETTINGS__ -	List of pairs key → value for global system settings. Can be taken from the database or manually created.
* __$\_MENU__ -	Multi-level array with main menu structure. Can be as global variable or as static property of the __Menu__ class.
* __~~$\_PATH~~__ -	Old version to set absolute path to the project's folder _(deprecated)_.

##### Session variables
* __$_USER__ - Variable stores serialized object of the __User__ class or simple array with user data.

##### Pages view and representation
* __$_ROUTE__ - Global instance of the __Route__ class with public properties controller, action and id - represents current page URL or rules to display this page.
* __$_TPL__	- String variable with a path to the needed template of the page.
* __$_JS__ - Controller local array with paths to needed for the current page JavaScript files.
* __$_CSS__	- Controller local array with paths to needed for the current page CSS files.

### Work with DataBase

You can get __DBObject__ with __DBSelector__:

```php
$userSelector = new DBSelector(new User());
$user = $userSelector->selectDBObjectById($userId);
```

You can manipulate with DataBase records this way:

```php
// Save (insert/update) record
$user->email = "dmytro@asymptix.com";
$user->save();

// Delete record
$user->delete();
```

Copyright (c) 2015 Asymptix.
