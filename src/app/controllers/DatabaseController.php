<?php

require_once __DIR__."/BaseController.php";
require_once __DIR__."/../helpers/FlashMessages.php";
require_once __DIR__."/../config/eloquent.php";

class DatabaseController extends BaseController
{
    public function showConnections(): void
    {
        FlashMessages::addToViews();

        $connections = EloquentManager::getConnectionsInfo();
        $stats = EloquentManager::getStats();

        Flight::view()->render('database/connections.latte', [
            'title' => 'Conexiones de Base de Datos',
            'connections' => $connections,
            'stats' => $stats,
            'currentUser' => Flight::get('currentUser'),
            'isAuthenticated' => Flight::get('isAuthenticated')
        ]);
    }

    public function testConnection(): void
    {
        $request = Flight::request();
        $connectionName = $request->data['connection'] ?? '';

        if (empty($connectionName)) {
            FlashMessages::error('Debe especificar una conexión para probar');
            Flight::redirect('/database/connections');
            return;
        }

        try {
            $result = EloquentManager::testConnection($connectionName);

            if ($result['success']) {
                FlashMessages::success("Conexión '{$connectionName}' probada exitosamente en {$result['time']}");
                FlashMessages::info("Versión del servidor: {$result['server_version']}");
            } else {
                FlashMessages::error("Error en conexión '{$connectionName}': {$result['message']}");
            }

        } catch (Exception $e) {
            FlashMessages::error("Error probando conexión: " . $e->getMessage());
        }

        Flight::redirect('/database/connections');
    }

    public function showQueryRunner(): void
    {
        FlashMessages::addToViews();

        $connections = EloquentManager::getConnectionsInfo();

        Flight::view()->render('database/query-runner.latte', [
            'title' => 'Ejecutor de Consultas',
            'connections' => $connections,
            'currentUser' => Flight::get('currentUser'),
            'isAuthenticated' => Flight::get('isAuthenticated')
        ]);
    }

    public function executeQuery(): void
    {
        $request = Flight::request();
        $sql = trim($request->data['sql'] ?? '');
        $connection = $request->data['connection'] ?? null;

        if (empty($sql)) {
            FlashMessages::error('Debe proporcionar una consulta SQL');
            Flight::redirect('/database/query-runner');
            return;
        }

        try {
            $startTime = microtime(true);
            $results = EloquentManager::select($sql, [], $connection);
            $endTime = microtime(true);

            $executionTime = round(($endTime - $startTime) * 1000, 2);
            $connectionName = $connection ?? 'default';

            // Determinar tipo de query
            $queryType = strtoupper(strtok(trim($sql), ' '));

            if (in_array($queryType, ['SELECT', 'SHOW', 'DESCRIBE', 'EXPLAIN'])) {
                $rowCount = count($results);

                FlashMessages::success("Query ejecutado en conexión '{$connectionName}' ({$executionTime}ms)");
                FlashMessages::info("Filas retornadas: {$rowCount}");

                // Mostrar resultados en session para la vista
                $_SESSION['query_results'] = [
                    'data' => $results,
                    'execution_time' => $executionTime,
                    'connection' => $connectionName,
                    'sql' => $sql,
                    'type' => $queryType
                ];

            } else {
                // Para queries que no son SELECT, usar métodos específicos
                if ($queryType === 'INSERT') {
                    $affectedRows = EloquentManager::insert($sql, [], $connection);
                } elseif ($queryType === 'UPDATE') {
                    $affectedRows = EloquentManager::update($sql, [], $connection);
                } elseif ($queryType === 'DELETE') {
                    $affectedRows = EloquentManager::delete($sql, [], $connection);
                } else {
                    // Para otros tipos de queries
                    $affectedRows = EloquentManager::connection($connection)->getPdo()->exec($sql);
                }

                FlashMessages::success("Query {$queryType} ejecutado en conexión '{$connectionName}' ({$executionTime}ms)");
                FlashMessages::info("Filas afectadas: {$affectedRows}");

                // Limpiar resultados previos
                unset($_SESSION['query_results']);
            }

        } catch (Exception $e) {
            FlashMessages::error("Error ejecutando query: " . $e->getMessage());
            unset($_SESSION['query_results']);
        }

        Flight::redirect('/database/query-runner');
    }

    public function showExamples(): void
    {
        FlashMessages::addToViews();

        Flight::view()->render('database/examples.latte', [
            'title' => 'Ejemplos de Uso de Base de Datos',
            'currentUser' => Flight::get('currentUser'),
            'isAuthenticated' => Flight::get('isAuthenticated')
        ]);
    }

    public function runExample(): void
    {
        $request = Flight::request();
        $exampleType = $request->data['example'] ?? '';

        try {
            switch ($exampleType) {
                case 'create_users_table':
                    $this->createUsersTable();
                    break;

                case 'insert_sample_data':
                    $this->insertSampleData();
                    break;

                case 'multi_connection_demo':
                    $this->multiConnectionDemo();
                    break;

                case 'transaction_demo':
                    $this->transactionDemo();
                    break;

                case 'performance_test':
                    $this->performanceTest();
                    break;

                default:
                    FlashMessages::error('Ejemplo no válido');
            }

        } catch (Exception $e) {
            FlashMessages::error("Error ejecutando ejemplo: " . $e->getMessage());
        }

        Flight::redirect('/database/examples');
    }

    private function createUsersTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";

        EloquentManager::connection()->getPdo()->exec($sql);
        FlashMessages::success('Tabla "users" creada exitosamente');
    }

    private function insertSampleData(): void
    {
        $users = [
            ['username' => 'admin', 'email' => 'admin@example.com', 'password_hash' => password_hash('admin123', PASSWORD_DEFAULT)],
            ['username' => 'user1', 'email' => 'user1@example.com', 'password_hash' => password_hash('user123', PASSWORD_DEFAULT)],
            ['username' => 'demo_user', 'email' => 'demo@example.com', 'password_hash' => password_hash('demo123', PASSWORD_DEFAULT)]
        ];

        $inserted = 0;
        foreach ($users as $user) {
            try {
                EloquentManager::connection()->table('users')->insert($user);
                $inserted++;
            } catch (Exception $e) {
                // Usuario podría ya existir, continuar
                continue;
            }
        }

        FlashMessages::success("Insertados {$inserted} usuarios de ejemplo");
        if ($inserted < count($users)) {
            FlashMessages::warning("Algunos usuarios ya existían y no fueron insertados");
        }
    }

    private function multiConnectionDemo(): void
    {
        // Usar conexión principal
        $mainUsers = EloquentManager::select("SELECT COUNT(*) as count FROM users");
        $mainCount = $mainUsers[0]['count'] ?? 0;

        // Intentar usar conexión secundaria (si está configurada y disponible)
        try {
            $secondaryTest = EloquentManager::testConnection('secondary');
            if ($secondaryTest['success']) {
                FlashMessages::success("Conexión principal: {$mainCount} usuarios");
                FlashMessages::info("Conexión secundaria disponible y funcionando");
            } else {
                FlashMessages::warning("Conexión secundaria no disponible: " . $secondaryTest['message']);
            }
        } catch (Exception $e) {
            FlashMessages::warning("Conexión secundaria no configurada correctamente");
        }

        // Probar conexión analytics
        try {
            $analyticsTest = EloquentManager::testConnection('analytics');
            if ($analyticsTest['success']) {
                FlashMessages::info("Conexión analytics disponible");
            } else {
                FlashMessages::warning("Conexión analytics no disponible: " . $analyticsTest['message']);
            }
        } catch (Exception $e) {
            FlashMessages::warning("Conexión analytics no configurada");
        }

        FlashMessages::success("Demo de múltiples conexiones completado");
    }

    private function transactionDemo(): void
    {
        EloquentManager::transaction(function() {
            // Insertar usuario en transacción
            $userData = [
                'username' => 'transaction_user_' . time(),
                'email' => 'transaction_' . time() . '@example.com',
                'password_hash' => password_hash('transaction123', PASSWORD_DEFAULT)
            ];

            $userId = EloquentManager::connection()->table('users')->insertGetId($userData);

            // Simular log de actividad (en la misma transacción)
            $logData = [
                'user_id' => $userId,
                'action' => 'user_created',
                'details' => 'Usuario creado via transacción demo',
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Si tenemos tabla de logs, insertamos ahí también
            try {
                EloquentManager::connection()->getPdo()->exec("
                    CREATE TABLE IF NOT EXISTS activity_logs (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        user_id INT,
                        action VARCHAR(100),
                        details TEXT,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ");

                EloquentManager::connection()->table('activity_logs')->insert($logData);
            } catch (Exception $e) {
                // Si falla algo, la transacción se revierte automáticamente
                throw new Exception("Error en log de actividad: " . $e->getMessage());
            }

            return $userId;
        });

        FlashMessages::success("Transacción completada exitosamente");
        FlashMessages::info("Usuario y log de actividad creados en una sola transacción");
    }

    private function performanceTest(): void
    {
        $startTime = microtime(true);

        // Test de múltiples queries
        for ($i = 0; $i < 10; $i++) {
            EloquentManager::select("SELECT COUNT(*) as count FROM users");
        }

        $endTime = microtime(true);
        $totalTime = round(($endTime - $startTime) * 1000, 2);

        FlashMessages::success("Test de rendimiento completado");
        FlashMessages::info("10 consultas ejecutadas en {$totalTime}ms");

        // Mostrar estadísticas del manager
        $stats = EloquentManager::getStats();
        FlashMessages::info("Conexiones activas: {$stats['active_connections']}");
        FlashMessages::info("Memoria usada: {$stats['memory_usage']}");
    }
}