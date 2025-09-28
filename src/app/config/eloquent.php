<?php

/**
 * Configuración y bootstrap de Eloquent ORM
 *
 * Este archivo configura Eloquent ORM para trabajar con múltiples conexiones
 * de base de datos fuera del framework Laravel completo.
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Dotenv\Dotenv;

/**
 * Cargar variables de entorno si existe el archivo .env
 */
function loadEnvironmentVariables() {
    $envFile = __DIR__ . '/../../.env';
    if (file_exists($envFile)) {
        $dotenv = Dotenv::createImmutable(dirname($envFile));
        $dotenv->load();
    }
}

/**
 * Helper para obtener variables de entorno
 */
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

/**
 * Configurar y inicializar Eloquent
 */
function initializeEloquent() {
    // Cargar variables de entorno
    loadEnvironmentVariables();

    // Cargar configuración de base de datos
    $config = require __DIR__ . '/database.php';

    // Crear una nueva instancia de Capsule
    $capsule = new Capsule;

    // Configurar las conexiones definidas en database.php
    foreach ($config['connections'] as $name => $connection) {
        $capsule->addConnection($connection, $name);
    }

    // Configurar la conexión por defecto
    $capsule->addConnection($config['connections'][$config['default']], 'default');

    // Configurar el event dispatcher (necesario para modelos)
    $capsule->setEventDispatcher(new Dispatcher(new Container));

    // Configurar logging de queries si está habilitado
    if ($config['logging']['log_queries'] ?? false) {
        $capsule->getConnection()->listen(function (QueryExecuted $query) use ($config) {
            logQuery(
                $query->sql,
                $query->bindings,
                $query->time,
                $query->connectionName,
                $config['logging']['slow_query_threshold'] ?? 2000
            );
        });
    }

    // Hacer que Eloquent esté disponible globalmente
    $capsule->setAsGlobal();

    // Inicializar Eloquent
    $capsule->bootEloquent();

    return $capsule;
}

/**
 * Logging de queries
 */
function logQuery($sql, $bindings, $time, $connectionName, $slowThreshold = 2000) {
    $timeMs = round($time, 2);

    $logMessage = sprintf(
        "[%s] Query en '%s' (%.2fms): %s",
        date('Y-m-d H:i:s'),
        $connectionName,
        $timeMs,
        $sql
    );

    if (!empty($bindings)) {
        $logMessage .= " | Params: " . json_encode($bindings);
    }

    if ($timeMs > $slowThreshold) {
        $logMessage = "[SLOW QUERY] " . $logMessage;
    }

    error_log($logMessage);
}

/**
 * Clase helper para gestionar conexiones Eloquent
 */
class EloquentManager
{
    private static $capsule = null;
    private static $initialized = false;

    /**
     * Obtener la instancia de Capsule
     */
    public static function getCapsule(): Capsule {
        if (self::$capsule === null) {
            self::$capsule = self::initialize();
        }

        return self::$capsule;
    }

    /**
     * Inicializar Eloquent
     */
    public static function initialize(): Capsule {
        if (self::$initialized) {
            return self::$capsule;
        }

        self::$capsule = initializeEloquent();
        self::$initialized = true;

        return self::$capsule;
    }

    /**
     * Obtener una conexión específica
     */
    public static function connection(string $name = null) {
        $capsule = self::getCapsule();
        return $capsule->getConnection($name);
    }

    /**
     * Obtener el schema builder para una conexión
     */
    public static function schema(string $connection = null) {
        return self::connection($connection)->getSchemaBuilder();
    }

    /**
     * Ejecutar una query en una conexión específica
     */
    public static function select(string $query, array $bindings = [], string $connection = null) {
        return self::connection($connection)->select($query, $bindings);
    }

    /**
     * Ejecutar una query de inserción
     */
    public static function insert(string $query, array $bindings = [], string $connection = null) {
        return self::connection($connection)->insert($query, $bindings);
    }

    /**
     * Ejecutar una query de actualización
     */
    public static function update(string $query, array $bindings = [], string $connection = null) {
        return self::connection($connection)->update($query, $bindings);
    }

    /**
     * Ejecutar una query de eliminación
     */
    public static function delete(string $query, array $bindings = [], string $connection = null) {
        return self::connection($connection)->delete($query, $bindings);
    }

    /**
     * Iniciar una transacción
     */
    public static function transaction(callable $callback, string $connection = null) {
        return self::connection($connection)->transaction($callback);
    }

    /**
     * Obtener información de conexiones activas
     */
    public static function getConnectionsInfo(): array {
        $capsule = self::getCapsule();
        $connections = [];

        // Cargar configuración para obtener todas las conexiones definidas
        $config = require __DIR__ . '/database.php';

        foreach ($config['connections'] as $name => $configData) {
            try {
                $connection = $capsule->getConnection($name);
                $pdo = $connection->getPdo();

                $connections[$name] = [
                    'name' => $name,
                    'driver' => $configData['driver'],
                    'host' => $configData['host'] ?? 'N/A',
                    'database' => $configData['database'],
                    'status' => 'connected',
                    'server_info' => $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION),
                ];
            } catch (\Exception $e) {
                $connections[$name] = [
                    'name' => $name,
                    'driver' => $configData['driver'],
                    'host' => $configData['host'] ?? 'N/A',
                    'database' => $configData['database'],
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $connections;
    }

    /**
     * Probar una conexión específica
     */
    public static function testConnection(string $name): array {
        try {
            $startTime = microtime(true);
            $connection = self::connection($name);
            $connection->select('SELECT 1');
            $endTime = microtime(true);

            return [
                'success' => true,
                'message' => 'Conexión exitosa',
                'time' => round(($endTime - $startTime) * 1000, 2) . 'ms',
                'connection' => $name
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'time' => null,
                'connection' => $name
            ];
        }
    }

    /**
     * Obtener estadísticas de la base de datos
     */
    public static function getStats(): array {
        $capsule = self::getCapsule();

        return [
            'eloquent_initialized' => self::$initialized,
            'active_connections' => count($capsule->getDatabaseManager()->getConnections()),
            'memory_usage' => formatBytes(memory_get_usage(true)),
            'peak_memory' => formatBytes(memory_get_peak_usage(true)),
        ];
    }
}

/**
 * Formatear bytes para estadísticas
 */
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Clase base para modelos Eloquent con conexiones múltiples
 */
abstract class BaseModel extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Cambiar la conexión dinámicamente
     */
    public function onConnection(string $connection) {
        $this->setConnection($connection);
        return $this;
    }

    /**
     * Obtener modelo en una conexión específica
     */
    public static function on(string $connection) {
        $instance = new static;
        $instance->setConnection($connection);
        return $instance->newQuery();
    }
}

// Inicializar Eloquent automáticamente cuando se incluya este archivo
if (!defined('ELOQUENT_INITIALIZED')) {
    EloquentManager::initialize();
    define('ELOQUENT_INITIALIZED', true);
}