<?php

/**
 * Registrar clases para utilizar en el framework
 */

require_once __DIR__."/../controllers/HomeController.php";
require_once __DIR__."/../controllers/AuthController.php";
require_once __DIR__."/../middleware/AuthMiddleware.php";

Flight::register('homeController', HomeController::class);
Flight::register('authController', AuthController::class);