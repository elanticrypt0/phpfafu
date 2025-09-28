<?php

error_reporting(E_ALL);

// If you're using Composer, require the autoloader.
require '../vendor/autoload.php';
// if you're not using Composer, load the framework directly
// require 'flight/Flight.php';

use Latte\Engine;

Flight::register('view', Engine::class, [], function ($latte) {
   $latte->setTempDirectory(__DIR__ . '/../storage/cache/');
   $latte->setLoader(new \Latte\Loaders\FileLoader(__DIR__ . '/../app/views'));
});

// Inicializar Eloquent ORM
require_once __DIR__."/../app/config/eloquent.php";

require_once __DIR__."/../app/config/classes.php";
require_once __DIR__."/../app/config/routes/routes.php";

// Finally, start the framework.
Flight::start();