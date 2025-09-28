<?php

// === RUTAS DE AUTENTICACIÓN ===

// Mostrar formulario de login (solo para usuarios no autenticados)
Flight::route('GET /login', function () {
    AuthMiddleware::guest(); // Middleware para usuarios no autenticados
    $authController = Flight::authController();
    $authController->showLogin();
});

// Procesar login
Flight::route('POST /login', function () {
    $authController = Flight::authController();
    $authController->login();
});

// Cerrar sesión
Flight::route('/logout', function () {
    $authController = Flight::authController();
    $authController->logout();
    Flight::redirect('/');
});

// Dashboard (ruta protegida)
Flight::route('GET /dashboard', function () {
    AuthMiddleware::authenticate(); // Middleware de autenticación
    $authController = Flight::authController();
    $authController->dashboard();
});