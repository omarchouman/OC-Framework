<?php

use Core\App;
use Core\Container;
use Core\Database;

$container = new Container();

$container->bind('Core\Database', function () {
    $config = require base_path('config/database.php');

    return new Database($config);
});

App::setContainer($container);