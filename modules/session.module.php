<?php

require_once("core/db/beans/User.php");

$_USER = null;
if (isset($_SESSION['user'])) {
    $_USER = unserialize($_SESSION['user']);

    $userSelector = new DBSelector(new User());
    $_USER = $userSelector->selectDBObjectById($_USER->id);

    $user = clone($_USER);
    $user->password = null;
    //TODO: clear other secured data from session

    $_SESSION['user'] = serialize($user);

    unset($user);
}

?>