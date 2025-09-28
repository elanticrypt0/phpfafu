<?php

/**
 * Sistema de mensajes flash para mostrar notificaciones al usuario
 */
class FlashMessages
{
    private const SESSION_KEY = 'flash_messages';

    /**
     * Tipos de mensajes disponibles
     */
    public const SUCCESS = 'success';
    public const ERROR = 'error';
    public const WARNING = 'warning';
    public const INFO = 'info';

    /**
     * Agregar un mensaje flash
     */
    public static function add(string $type, string $message): void
    {
        self::initSession();

        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }

        $_SESSION[self::SESSION_KEY][] = [
            'type' => $type,
            'message' => $message,
            'timestamp' => time()
        ];
    }

    /**
     * Agregar mensaje de éxito
     */
    public static function success(string $message): void
    {
        self::add(self::SUCCESS, $message);
    }

    /**
     * Agregar mensaje de error
     */
    public static function error(string $message): void
    {
        self::add(self::ERROR, $message);
    }

    /**
     * Agregar mensaje de advertencia
     */
    public static function warning(string $message): void
    {
        self::add(self::WARNING, $message);
    }

    /**
     * Agregar mensaje informativo
     */
    public static function info(string $message): void
    {
        self::add(self::INFO, $message);
    }

    /**
     * Obtener todos los mensajes y limpiar la sesión
     */
    public static function getAndClear(): array
    {
        self::initSession();

        $messages = $_SESSION[self::SESSION_KEY] ?? [];
        $_SESSION[self::SESSION_KEY] = [];

        return $messages;
    }

    /**
     * Obtener mensajes sin limpiar
     */
    public static function get(): array
    {
        self::initSession();
        return $_SESSION[self::SESSION_KEY] ?? [];
    }

    /**
     * Verificar si hay mensajes
     */
    public static function has(): bool
    {
        return !empty(self::get());
    }

    /**
     * Verificar si hay mensajes de un tipo específico
     */
    public static function hasType(string $type): bool
    {
        $messages = self::get();
        foreach ($messages as $message) {
            if ($message['type'] === $type) {
                return true;
            }
        }
        return false;
    }

    /**
     * Limpiar todos los mensajes
     */
    public static function clear(): void
    {
        self::initSession();
        $_SESSION[self::SESSION_KEY] = [];
    }

    /**
     * Renderizar mensajes como HTML
     */
    public static function render(): string
    {
        $messages = self::getAndClear();
        if (empty($messages)) {
            return '';
        }

        $html = '<div id="flash-messages" class="flash-messages-container">';

        foreach ($messages as $message) {
            $type = htmlspecialchars($message['type']);
            $text = htmlspecialchars($message['message']);
            $icon = self::getIcon($type);

            $html .= "<div class=\"flash-message flash-{$type}\" data-flash-type=\"{$type}\">";
            $html .= "<span class=\"flash-icon\">{$icon}</span>";
            $html .= "<span class=\"flash-text\">{$text}</span>";
            $html .= "<button class=\"flash-close\" onclick=\"this.parentElement.remove()\">&times;</button>";
            $html .= "</div>";
        }

        $html .= '</div>';

        // Agregar JavaScript para auto-ocultar mensajes
        $html .= "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const flashMessages = document.querySelectorAll('.flash-message');
                flashMessages.forEach(function(message) {
                    // Auto-ocultar después de 5 segundos (excepto errores)
                    if (!message.classList.contains('flash-error')) {
                        setTimeout(function() {
                            if (message.parentElement) {
                                message.style.opacity = '0';
                                setTimeout(() => message.remove(), 300);
                            }
                        }, 5000);
                    }
                });
            });
        </script>";

        return $html;
    }

    /**
     * Obtener icono según el tipo de mensaje
     */
    private static function getIcon(string $type): string
    {
        return match($type) {
            self::SUCCESS => '✅',
            self::ERROR => '❌',
            self::WARNING => '⚠️',
            self::INFO => 'ℹ️',
            default => '📝'
        };
    }

    /**
     * Inicializar sesión si no está iniciada
     */
    private static function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Método estático para usar con Flight::set()
     */
    public static function addToViews(): void
    {
        Flight::set('flashMessages', self::render());
        Flight::set('hasFlashMessages', self::has());
    }
}