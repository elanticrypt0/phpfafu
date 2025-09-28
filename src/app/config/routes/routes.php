<?php

require_once __DIR__."/routes_auth.php";
require_once __DIR__."/routes_example.php";
require_once __DIR__."/routes_database.php";
require_once __DIR__."/routes_eloquent.php";

// Inicializar sesiones para todas las rutas
Flight::before('start', function() {
    AuthMiddleware::startSession();
    AuthMiddleware::addUserToViews();
});

// Error: 404 Página no encontrada
Flight::map('notFound', function () {
    echo 'Error 404: <br/> Página no encontrada';
});

// Página principal
Flight::route('/', function () {
    Flight::view()->render('home/home.latte', [
        'title' => 'My App',
        'currentUser' => Flight::get('currentUser'),
        'isAuthenticated' => Flight::get('isAuthenticated')
    ]);
});