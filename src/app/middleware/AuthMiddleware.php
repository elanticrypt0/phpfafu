<?php

class AuthMiddleware
{
    /**
     * Middleware para verificar autenticación
     * Se puede usar en rutas individuales o grupos de rutas
     */
    public static function authenticate(): void
    {
        AuthController::requireAuth();
    }

    /**
     * Middleware para verificar si el usuario ya está autenticado
     * Útil para rutas de login que no deberían ser accesibles si ya estás logueado
     */
    public static function guest(): void
    {
        require_once __DIR__.'/../controllers/AuthController.php';
        $auth = new AuthController();

        if ($auth->isAuthenticated()) {
            // Si es una petición HTMX, enviar respuesta apropiada
            if (Flight::request()->headers['HX-Request'] ?? false) {
                Flight::response()->header('HX-Redirect', '/dashboard');
                echo '<div class="success">Ya estás autenticado. Redirigiendo al dashboard...</div>';
            } else {
                Flight::redirect('/dashboard');
            }
            Flight::stop();
        }
    }

    /**
     * Middleware para inicializar sesiones
     * Debe ejecutarse antes que otros middlewares de auth
     */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Middleware para agregar información de usuario a las vistas
     * Se ejecuta después de verificar autenticación
     */
    public static function addUserToViews(): void
    {
        require_once __DIR__.'/../controllers/AuthController.php';
        $auth = new AuthController();

        if ($auth->isAuthenticated()) {
            $currentUser = $auth->getCurrentUser();

            // Agregar usuario global para las vistas usando Flight::set()
            Flight::set('currentUser', $currentUser);
            Flight::set('isAuthenticated', true);
        } else {
            Flight::set('currentUser', null);
            Flight::set('isAuthenticated', false);
        }
    }
}