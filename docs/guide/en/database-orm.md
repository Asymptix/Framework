Database (ORM)
---

You can create new __DBObject__:

```php
$user = new User();
```

or you can get __DBObject__ with __DBSelector__:

```php
$userSelector = new DBSelector(new User);
$user = $userSelector->selectDBObjectById($userId);
```

or you can get it with alternative way...

```php
$user = User::_select([User::ID_FIELD_NAME => $userId])->limit(1)->go();
```

or this short way...

```php
$user = User::_get($userId);
```

You can manipulate with DataBase records this way:

```php
// Save (insert/update) record
$user->email = "dmytro@asymptix.com";
$user->save();
```

If ID of the __DBObject__ is empty - then __INSERT__ SQL instruction will be executed, if not empty - then __UPDATE__.

If you don't want update all fields of the record, you can use

```php
$user->update(['email' => "dmytro@asymptix.com"])->go();
```

or in the static way:

```php
User::_update(['email' => "dmytro@asymptix.com"], [User::ID_FIELD_NAME => $userId])
      ->limit(1)
      ->go();
```

To delete user record...

```php
$user->delete();
```

or...

```php
User::_delete([User::ID_FIELD_NAME => $userId])->go();
```

or even...

```php
User::_delete($userId)->go();
```

You can also use this syntax for the fast selection queries:

```php
$sitesList = Site::_select(['status' => "active"])
                  ->order(['title' => "ASC"])
                  ->limit(10)
                  ->go();
```

All DB queries will be executed with using of Prepared Statements.

