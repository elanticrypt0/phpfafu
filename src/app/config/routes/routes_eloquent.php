<?php

/**
 * Rutas para ejemplos de Eloquent ORM y Laravel Validator
 */

require_once __DIR__."/../../controllers/ExamplesController.php";

// Rutas de ejemplos (requieren autenticación)
Flight::group('/examples', function() {
    // Página principal de ejemplos
    Flight::route('/', [ExamplesController::class, 'showIndex']);

    // Ejecutar ejemplo específico
    Flight::route('POST /run', [ExamplesController::class, 'runExample']);

    // Documentación
    Flight::route('/docs', [ExamplesController::class, 'documentation']);

}, [AuthController::class, 'requireAuth']);

// API de ejemplos (sin autenticación para facilitar testing)
Flight::group('/api/examples', function() {
    // Ejecutar ejemplo específico via API
    Flight::route('GET /', [ExamplesController::class, 'apiExample']);

    // Ejecutar todos los ejemplos
    Flight::route('GET /all', [ExamplesController::class, 'getAllExamples']);
});