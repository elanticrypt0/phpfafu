<?php

/**
 * Registrar clases para utilizar en el framework
 */

require_once __DIR__."/../controllers/HomeController.php";

Flight::register('homeController',HomeController::class);