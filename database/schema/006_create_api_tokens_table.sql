-- ============================================================================
-- Tabla: api_tokens
-- Descripción: Tokens de API para autenticación
-- Versión: 1.0
-- Fecha: 2024-09-28
-- ============================================================================

CREATE TABLE IF NOT EXISTS api_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL COMMENT 'ID del usuario propietario',

    -- Información del token
    name VARCHAR(100) NOT NULL COMMENT 'Nombre descriptivo del token',
    token VARCHAR(64) UNIQUE NOT NULL COMMENT 'Token de API (hash)',
    type ENUM('personal', 'application', 'temporary') DEFAULT 'personal' COMMENT 'Tipo de token',

    -- Permisos y alcance
    abilities JSON NULL COMMENT 'Permisos específicos del token',
    scopes VARCHAR(500) NULL COMMENT 'Alcances permitidos separados por comas',

    -- Control de acceso
    allowed_ips TEXT NULL COMMENT 'IPs permitidas (separadas por comas)',
    rate_limit INT DEFAULT 1000 COMMENT 'Límite de requests por hora',

    -- Estados y control
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Token activo',
    expires_at TIMESTAMP NULL COMMENT 'Fecha de expiración',

    -- Estadísticas de uso
    last_used_at TIMESTAMP NULL COMMENT 'Última vez usado',
    usage_count INT DEFAULT 0 COMMENT 'Número de veces usado',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de actualización',

    -- Claves foráneas
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tokens de API';

-- ============================================================================
-- Tabla: api_requests
-- Descripción: Log de requests de API
-- ============================================================================

CREATE TABLE IF NOT EXISTS api_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    api_token_id BIGINT UNSIGNED NULL COMMENT 'ID del token usado',
    user_id INT UNSIGNED NULL COMMENT 'ID del usuario',

    -- Información de la request
    method VARCHAR(10) NOT NULL COMMENT 'Método HTTP',
    url VARCHAR(500) NOT NULL COMMENT 'URL solicitada',
    endpoint VARCHAR(200) NULL COMMENT 'Endpoint identificado',

    -- Request data
    headers JSON NULL COMMENT 'Headers de la request',
    query_params JSON NULL COMMENT 'Parámetros de query',
    body_size INT DEFAULT 0 COMMENT 'Tamaño del body en bytes',

    -- Response data
    status_code INT NOT NULL COMMENT 'Código de estado HTTP',
    response_size INT DEFAULT 0 COMMENT 'Tamaño de la respuesta en bytes',
    response_time DECIMAL(8,3) DEFAULT 0 COMMENT 'Tiempo de respuesta en ms',

    -- Información del cliente
    ip_address VARCHAR(45) NOT NULL COMMENT 'Dirección IP',
    user_agent TEXT NULL COMMENT 'User Agent',

    -- Geolocalización (opcional)
    country VARCHAR(2) NULL COMMENT 'Código de país',
    city VARCHAR(100) NULL COMMENT 'Ciudad',

    -- Errores
    error_message TEXT NULL COMMENT 'Mensaje de error si aplica',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de la request',

    -- Claves foráneas
    FOREIGN KEY (api_token_id) REFERENCES api_tokens(id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log de requests de API';

-- Índices para optimizar consultas
CREATE INDEX idx_api_tokens_user_id ON api_tokens(user_id);
CREATE INDEX idx_api_tokens_token ON api_tokens(token);
CREATE INDEX idx_api_tokens_type ON api_tokens(type);
CREATE INDEX idx_api_tokens_is_active ON api_tokens(is_active);
CREATE INDEX idx_api_tokens_expires_at ON api_tokens(expires_at);
CREATE INDEX idx_api_tokens_last_used_at ON api_tokens(last_used_at);

CREATE INDEX idx_api_requests_token_id ON api_requests(api_token_id);
CREATE INDEX idx_api_requests_user_id ON api_requests(user_id);
CREATE INDEX idx_api_requests_method ON api_requests(method);
CREATE INDEX idx_api_requests_status_code ON api_requests(status_code);
CREATE INDEX idx_api_requests_endpoint ON api_requests(endpoint);
CREATE INDEX idx_api_requests_created_at ON api_requests(created_at);
CREATE INDEX idx_api_requests_ip_address ON api_requests(ip_address);