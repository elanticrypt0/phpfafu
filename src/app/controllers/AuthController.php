<?php

require_once __DIR__."/BaseController.php";

class AuthController extends BaseController
{
    private const DEMO_USER = 'demo';
    private const DEMO_PASS = 'demo';

    public function showLogin(): void
    {
        if ($this->isAuthenticated()) {
            Flight::redirect('/dashboard');
            return;
        }

        Flight::view()->render('auth/login.latte', [
            'title' => 'Iniciar Sesión',
            'currentUser' => Flight::get('currentUser'),
            'isAuthenticated' => Flight::get('isAuthenticated')
        ]);
    }

    public function login(): void
    {
        $request = Flight::request();
        $username = $request->data['username'] ?? '';
        $password = $request->data['password'] ?? '';

        if ($this->validateCredentials($username, $password)) {
            $this->startSession($username);

            // Respuesta HTMX para redirección
            Flight::response()->header('HX-Redirect', '/dashboard');
            echo '<div class="success">Login exitoso. Redirigiendo...</div>';
        } else {
            // Respuesta HTMX con error
            echo '<div class="card" style="border-color: var(--color-error);">
                    <p style="color: var(--color-error); margin: 0;">
                        ❌ Credenciales incorrectas. Usa demo/demo
                    </p>
                  </div>';
        }
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Limpiar todas las variables de sesión
        $_SESSION = array();

        // Si se desea destruir la sesión completamente, eliminar también la cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finalmente, destruir la sesión
        session_destroy();

        // Respuesta HTMX para redirección
        Flight::response()->header('HX-Redirect', '/');
        Flight::response()->status(200);
        echo '<div class="success">✅ Sesión cerrada correctamente. Redirigiendo...</div>';
    }

    public function dashboard(): void
    {
        if (!$this->isAuthenticated()) {
            Flight::redirect('/login');
            return;
        }

        $user = $_SESSION['user'] ?? 'Usuario';

        Flight::view()->render('auth/dashboard.latte', [
            'title' => 'Dashboard',
            'user' => $user,
            'currentUser' => Flight::get('currentUser'),
            'isAuthenticated' => Flight::get('isAuthenticated')
        ]);
    }

    // Métodos auxiliares
    private function validateCredentials(string $username, string $password): bool
    {
        return $username === self::DEMO_USER && $password === self::DEMO_PASS;
    }

    private function startSession(string $username): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['authenticated'] = true;
        $_SESSION['user'] = $username;
        $_SESSION['login_time'] = time();
    }

    public function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
    }

    public function getCurrentUser(): ?string
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return $_SESSION['user'] ?? null;
    }

    // Método para middleware
    public static function requireAuth(): void
    {
        $auth = new self();

        if (!$auth->isAuthenticated()) {
            // Si es una petición HTMX, enviar respuesta apropiada
            if (Flight::request()->headers['HX-Request'] ?? false) {
                Flight::response()->header('HX-Redirect', '/login');
                echo '<div class="error">Sesión expirada. Redirigiendo al login...</div>';
            } else {
                Flight::redirect('/login');
            }
            Flight::stop();
        }
    }
}