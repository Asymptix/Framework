![Asymptix](https://media.licdn.com/media/AAEAAQAAAAAAAAK-AAAAJDVhMDMzNDIxLWMzOTktNDhhNS04YWFjLWZmMjQ0Mzc1NDE4Ng.png)

# Asymptix PHP Framework
The Fast and Easy PHP Framework for Rapid Development.

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
