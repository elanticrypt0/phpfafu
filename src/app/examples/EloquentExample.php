<?php

require_once __DIR__ . '/../config/eloquent.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/FormValidator.php';

/**
 * Ejemplos prácticos de uso de Eloquent ORM y Laravel Validator
 *
 * Esta clase contiene ejemplos de cómo usar las nuevas implementaciones
 * de Eloquent ORM y Laravel Validator en lugar de las anteriores.
 */
class EloquentExample
{
    /**
     * Ejemplo 1: Crear un usuario con validación
     */
    public static function createUserExample(): array
    {
        $userData = [
            'username' => 'nuevo_usuario',
            'email' => 'nuevo@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'first_name' => 'Nuevo',
            'last_name' => 'Usuario'
        ];

        // Validar datos usando Laravel Validator
        $validator = User::validateUserData($userData);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->getErrors(),
                'message' => 'Datos de usuario inválidos'
            ];
        }

        try {
            // Crear usuario usando Eloquent
            $user = User::createUser($userData);

            return [
                'success' => true,
                'user' => $user->toArray(),
                'message' => 'Usuario creado exitosamente'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error al crear usuario'
            ];
        }
    }

    /**
     * Ejemplo 2: Autenticar usuario
     */
    public static function authenticateUserExample(string $username, string $password): array
    {
        // Validar credenciales de login
        $validator = FormValidator::validateLogin([
            'username' => $username,
            'password' => $password
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->getErrors(),
                'message' => 'Credenciales inválidas'
            ];
        }

        // Autenticar usando el modelo User
        $user = User::authenticate($username, $password);

        if ($user) {
            return [
                'success' => true,
                'user' => $user->toArray(),
                'message' => 'Autenticación exitosa'
            ];
        }

        return [
            'success' => false,
            'message' => 'Credenciales incorrectas'
        ];
    }

    /**
     * Ejemplo 3: Buscar usuarios con filtros
     */
    public static function searchUsersExample(string $searchTerm = '', int $page = 1): array
    {
        try {
            $query = User::query();

            // Si hay término de búsqueda, aplicar filtros
            if (!empty($searchTerm)) {
                $query = User::search($searchTerm);
            }

            // Aplicar paginación
            $perPage = 10;
            $offset = ($page - 1) * $perPage;

            $users = $query->skip($offset)->take($perPage)->get();
            $total = $query->count();

            return [
                'success' => true,
                'data' => $users->toArray(),
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_pages' => ceil($total / $perPage)
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error al buscar usuarios'
            ];
        }
    }

    /**
     * Ejemplo 4: Usar múltiples conexiones de base de datos
     */
    public static function multipleConnectionsExample(): array
    {
        try {
            // Usar conexión principal
            $mainUsers = User::count();

            // Usar conexión secundaria
            $secondaryUsers = User::on('secondary')->count();

            // Usar conexión analytics
            $analyticsUsers = User::on('analytics')->count();

            // Probar conexiones
            $connections = [
                'main' => EloquentManager::testConnection('mysql'),
                'secondary' => EloquentManager::testConnection('secondary'),
                'analytics' => EloquentManager::testConnection('analytics')
            ];

            return [
                'success' => true,
                'user_counts' => [
                    'main' => $mainUsers,
                    'secondary' => $secondaryUsers,
                    'analytics' => $analyticsUsers
                ],
                'connections' => $connections,
                'message' => 'Prueba de múltiples conexiones completada'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error al probar múltiples conexiones'
            ];
        }
    }

    /**
     * Ejemplo 5: Transacciones con Eloquent
     */
    public static function transactionExample(): array
    {
        try {
            $result = EloquentManager::transaction(function () {
                // Crear usuario
                $user = User::create([
                    'username' => 'transaction_user_' . time(),
                    'email' => 'transaction_' . time() . '@example.com',
                    'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
                    'first_name' => 'Transaction',
                    'last_name' => 'User'
                ]);

                // Crear perfil
                EloquentManager::connection()->table('user_profiles')->insert([
                    'user_id' => $user->id,
                    'bio' => 'Usuario creado en transacción de ejemplo',
                    'profile_visibility' => 'public',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // Log de actividad
                EloquentManager::connection()->table('activity_logs')->insert([
                    'user_id' => $user->id,
                    'action' => 'user_created',
                    'description' => 'Usuario creado via transacción de ejemplo',
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                return $user;
            });

            return [
                'success' => true,
                'user' => $result->toArray(),
                'message' => 'Transacción completada exitosamente'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error en la transacción'
            ];
        }
    }

    /**
     * Ejemplo 6: Validaciones avanzadas con Laravel Validator
     */
    public static function advancedValidationExample(): array
    {
        $testData = [
            'email' => 'invalid-email',
            'password' => '123',
            'age' => 'not-a-number',
            'website' => 'not-a-url',
            'gender' => 'invalid-option'
        ];

        // Validación con reglas complejas
        $validator = FormValidator::make($testData)->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'age' => 'required|integer|between:18,100',
            'website' => 'nullable|url',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'terms' => 'required|boolean'
        ], [
            'email.email' => 'El email debe ser válido',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.regex' => 'La contraseña debe contener mayúsculas, minúsculas y números',
            'age.between' => 'La edad debe estar entre 18 y 100 años',
            'terms.required' => 'Debe aceptar los términos y condiciones'
        ]);

        return [
            'success' => $validator->passes(),
            'errors' => $validator->getErrors(),
            'test_data' => $testData,
            'message' => $validator->passes() ? 'Validación exitosa' : 'Errores de validación encontrados'
        ];
    }

    /**
     * Ejemplo 7: Estadísticas del sistema
     */
    public static function systemStatsExample(): array
    {
        try {
            // Estadísticas de Eloquent
            $eloquentStats = EloquentManager::getStats();

            // Estadísticas de usuarios
            $userStats = User::getStats();

            // Información de conexiones
            $connections = EloquentManager::getConnectionsInfo();

            return [
                'success' => true,
                'eloquent_stats' => $eloquentStats,
                'user_stats' => $userStats,
                'connections' => $connections,
                'message' => 'Estadísticas del sistema obtenidas'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error al obtener estadísticas'
            ];
        }
    }

    /**
     * Ejecutar todos los ejemplos
     */
    public static function runAllExamples(): array
    {
        $results = [];

        $examples = [
            'create_user' => 'createUserExample',
            'authenticate' => function() { return self::authenticateUserExample('demo', 'demo'); },
            'search_users' => function() { return self::searchUsersExample('demo'); },
            'multiple_connections' => 'multipleConnectionsExample',
            'transaction' => 'transactionExample',
            'advanced_validation' => 'advancedValidationExample',
            'system_stats' => 'systemStatsExample'
        ];

        foreach ($examples as $name => $example) {
            try {
                if (is_callable($example)) {
                    $results[$name] = $example();
                } else {
                    $results[$name] = self::$example();
                }
            } catch (Exception $e) {
                $results[$name] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'message' => "Error ejecutando ejemplo: $name"
                ];
            }
        }

        return [
            'success' => true,
            'examples' => $results,
            'message' => 'Todos los ejemplos ejecutados'
        ];
    }
}