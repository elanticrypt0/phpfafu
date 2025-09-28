<?php

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
});

Flight::route('POST /logout', function () {
    $authController = Flight::authController();
    $authController->logout();
});

// Dashboard (ruta protegida)
Flight::route('GET /dashboard', function () {
    AuthMiddleware::authenticate(); // Middleware de autenticación
    $authController = Flight::authController();
    $authController->dashboard();
});

// === RUTAS PROTEGIDAS DE EJEMPLO ===

// API para datos protegidos
Flight::route('GET /api/protected-data', function () {
    AuthMiddleware::authenticate();

    $data = [
        'message' => '🔒 Estos son datos confidenciales',
        'data' => [
            'ventas_mes' => '$25,340',
            'usuarios_activos' => 1,
            'última_actualización' => date('Y-m-d H:i:s')
        ]
    ];

    echo '<div class="success">
            <h4>' . $data['message'] . '</h4>
            <ul style="margin: 0; padding-left: 1rem;">
                <li><strong>Ventas del mes:</strong> ' . $data['data']['ventas_mes'] . '</li>
                <li><strong>Usuarios activos:</strong> ' . $data['data']['usuarios_activos'] . '</li>
                <li><strong>Última actualización:</strong> ' . $data['data']['última_actualización'] . '</li>
            </ul>
          </div>';
});

// API para perfil de usuario
Flight::route('GET /api/user-profile', function () {
    AuthMiddleware::authenticate();

    $authController = Flight::authController();
    $user = $authController->getCurrentUser();

    echo '<div class="info">
            <h4>👤 Perfil de Usuario</h4>
            <p><strong>Usuario:</strong> ' . $user . '</p>
            <p><strong>Rol:</strong> Administrador Demo</p>
            <p><strong>Permisos:</strong> Lectura, Escritura, Admin</p>
            <p><strong>Último acceso:</strong> ' . date('Y-m-d H:i:s') . '</p>
          </div>';
});

// API para renovar sesión
Flight::route('POST /api/refresh-session', function () {
    AuthMiddleware::authenticate();

    // Actualizar timestamp de la sesión
    $_SESSION['last_activity'] = time();

    echo '<div class="success">
            ✅ Sesión renovada correctamente. Timestamp: ' . date('Y-m-d H:i:s') . '
          </div>';
});

// === RUTAS PÚBLICAS ===

// Saludo público (ejemplo original)
Flight::route('POST /saludar', function () {
    $request = Flight::request();
    $nombre = $request->data['nombre'] ?? 'Pepe';

    $homeController = Flight::homeController();
    $homeController->saludar($nombre);
});