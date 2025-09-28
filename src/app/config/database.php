<?php

/**
 * Configuración de base de datos usando variables de entorno
 *
 * Este archivo utiliza las variables definidas en .env para configurar
 * las conexiones a múltiples bases de datos.
 */

// Cargar variables de entorno si no están cargadas
if (!function_exists('env') && file_exists(__DIR__ . '/../../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
    $dotenv->load();
}

/**
 * Helper para obtener variables de entorno con valores por defecto
 */
if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false) {
            return $default;
        }

        // Convertir strings especiales
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        return $value;
    }
}

return [
    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    */
    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    */
    'connections' => [

        /*
        |--------------------------------------------------------------------------
        | Conexión Principal MySQL
        |--------------------------------------------------------------------------
        */
        'mysql' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'db'),
            'port'      => env('DB_PORT', '3306'),
            'database'  => env('DB_DATABASE', 'docker_php_api'),
            'username'  => env('DB_USERNAME', 'root'),
            'password'  => env('DB_PASSWORD', 'root_password'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null,
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 30,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Conexión Secundaria MySQL
        |--------------------------------------------------------------------------
        */
        'secondary' => [
            'driver'    => env('DB_SECONDARY_CONNECTION', 'mysql'),
            'host'      => env('DB_SECONDARY_HOST', 'db_secondary'),
            'port'      => env('DB_SECONDARY_PORT', '3306'),
            'database'  => env('DB_SECONDARY_DATABASE', 'secondary_db'),
            'username'  => env('DB_SECONDARY_USERNAME', 'secondary_user'),
            'password'  => env('DB_SECONDARY_PASSWORD', 'secondary_password'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null,
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 30,
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Conexión Analytics MySQL
        |--------------------------------------------------------------------------
        */
        'analytics' => [
            'driver'    => env('DB_ANALYTICS_CONNECTION', 'mysql'),
            'host'      => env('DB_ANALYTICS_HOST', 'analytics_db'),
            'port'      => env('DB_ANALYTICS_PORT', '3306'),
            'database'  => env('DB_ANALYTICS_DATABASE', 'analytics'),
            'username'  => env('DB_ANALYTICS_USERNAME', 'analytics_user'),
            'password'  => env('DB_ANALYTICS_PASSWORD', 'analytics_password'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => 'analytics_',
            'strict'    => false, // Más flexible para analytics
            'engine'    => null,
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 60, // Timeout más largo para queries complejas
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false, // Para grandes datasets
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Conexión PostgreSQL
        |--------------------------------------------------------------------------
        */
        'pgsql' => [
            'driver'   => env('DB_POSTGRES_CONNECTION', 'pgsql'),
            'host'     => env('DB_POSTGRES_HOST', 'postgres_host'),
            'port'     => env('DB_POSTGRES_PORT', '5432'),
            'database' => env('DB_POSTGRES_DATABASE', 'postgres_db'),
            'username' => env('DB_POSTGRES_USERNAME', 'postgres_user'),
            'password' => env('DB_POSTGRES_PASSWORD', 'postgres_password'),
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
            'sslmode'  => 'prefer',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 30,
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Conexión SQLite
        |--------------------------------------------------------------------------
        */
        'sqlite' => [
            'driver'   => env('DB_SQLITE_CONNECTION', 'sqlite'),
            'database' => env('DB_SQLITE_DATABASE', __DIR__ . '/../../storage/database.sqlite'),
            'prefix'   => '',
            'foreign_key_constraints' => true,
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 30,
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Conexión de Testing (en memoria)
        |--------------------------------------------------------------------------
        */
        'testing' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    */
    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled' => true,
        'log_queries' => env('LOG_QUERIES', false),
        'log_slow_queries' => env('LOG_SLOW_QUERIES', true),
        'slow_query_threshold' => env('SLOW_QUERY_THRESHOLD', 2000), // ms
        'log_connections' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pool de conexiones - configuración para reutilización
    |--------------------------------------------------------------------------
    */
    'pool' => [
        'enabled' => true,
        'max_connections' => 10,
        'timeout' => 30, // segundos
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Configuration
    |--------------------------------------------------------------------------
    */
    'redis' => [
        'client' => 'predis',
        'options' => [
            'cluster' => false,
            'prefix' => env('CACHE_PREFIX', 'laravel_database_'),
        ],
        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DB', 0),
        ],
        'cache' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_CACHE_DB', 1),
        ],
    ],
];