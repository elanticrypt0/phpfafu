<?php

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Helper para validación de formularios usando Respect\Validation
 */
class FormValidator
{
    private array $errors = [];
    private array $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Validar un campo con reglas específicas
     */
    public function validate(string $field, $rules, string $customMessage = null): self
    {
        try {
            $value = $this->data[$field] ?? null;
            $rules->assert($value);
        } catch (ValidationException $e) {
            $message = $customMessage ?? $this->translateMessage($e->getMessage(), $field);
            $this->errors[$field] = $message;
        }

        return $this;
    }

    /**
     * Validar múltiples campos a la vez
     */
    public function validateMultiple(array $validations): self
    {
        foreach ($validations as $field => $config) {
            $rules = $config['rules'];
            $message = $config['message'] ?? null;
            $this->validate($field, $rules, $message);
        }

        return $this;
    }

    /**
     * Verificar si la validación pasó
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Verificar si la validación falló
     */
    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Obtener todos los errores
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Obtener errores de un campo específico
     */
    public function getError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }

    /**
     * Obtener el primer error
     */
    public function getFirstError(): ?string
    {
        return !empty($this->errors) ? array_values($this->errors)[0] : null;
    }

    /**
     * Agregar error manualmente
     */
    public function addError(string $field, string $message): self
    {
        $this->errors[$field] = $message;
        return $this;
    }

    /**
     * Limpiar errores
     */
    public function clearErrors(): self
    {
        $this->errors = [];
        return $this;
    }

    /**
     * Obtener datos validados (solo campos sin errores)
     */
    public function getValidatedData(): array
    {
        $validated = [];
        foreach ($this->data as $field => $value) {
            if (!isset($this->errors[$field])) {
                $validated[$field] = $value;
            }
        }
        return $validated;
    }

    /**
     * Crear validador con datos de request
     */
    public static function make(array $data): self
    {
        return new self($data);
    }

    /**
     * Reglas de validación comunes
     */
    public static function rules(): object
    {
        return new class {
            public function required(): \Respect\Validation\Validator
            {
                return v::notEmpty();
            }

            public function email(): \Respect\Validation\Validator
            {
                return v::email();
            }

            public function minLength(int $min): \Respect\Validation\Validator
            {
                return v::length($min, null);
            }

            public function maxLength(int $max): \Respect\Validation\Validator
            {
                return v::length(null, $max);
            }

            public function length(int $min, int $max): \Respect\Validation\Validator
            {
                return v::length($min, $max);
            }

            public function alpha(): \Respect\Validation\Validator
            {
                return v::alpha();
            }

            public function alphaNum(): \Respect\Validation\Validator
            {
                return v::alnum();
            }

            public function numeric(): \Respect\Validation\Validator
            {
                return v::numeric();
            }

            public function username(): \Respect\Validation\Validator
            {
                return v::alnum()->length(3, 20);
            }

            public function password(): \Respect\Validation\Validator
            {
                return v::length(3, null); // Mínimo 3 caracteres para demo
            }

            public function strongPassword(): \Respect\Validation\Validator
            {
                return v::length(8, null)
                    ->regex('/[A-Z]/')  // Al menos una mayúscula
                    ->regex('/[a-z]/')  // Al menos una minúscula
                    ->regex('/[0-9]/'); // Al menos un número
            }
        };
    }

    /**
     * Traducir mensajes de error
     */
    private function translateMessage(string $message, string $field): string
    {
        $translations = [
            'must not be empty' => "El campo {$field} es requerido",
            'must be a valid email' => "El campo {$field} debe ser un email válido",
            'must have a length' => "El campo {$field} no tiene la longitud correcta",
            'must contain only letters' => "El campo {$field} solo puede contener letras",
            'must contain only letters and digits' => "El campo {$field} solo puede contener letras y números",
            'must be numeric' => "El campo {$field} debe ser numérico",
        ];

        foreach ($translations as $english => $spanish) {
            if (str_contains($message, $english)) {
                return $spanish;
            }
        }

        return "El campo {$field} es inválido";
    }

    /**
     * Validación específica para login
     */
    public static function validateLogin(array $data): self
    {
        return self::make($data)->validateMultiple([
            'username' => [
                'rules' => self::rules()->required(),
                'message' => 'El usuario es requerido'
            ],
            'password' => [
                'rules' => self::rules()->required(),
                'message' => 'La contraseña es requerida'
            ]
        ]);
    }

    /**
     * Validación para registro de usuario
     */
    public static function validateRegistration(array $data): self
    {
        return self::make($data)->validateMultiple([
            'username' => [
                'rules' => self::rules()->username(),
                'message' => 'El usuario debe tener entre 3 y 20 caracteres alfanuméricos'
            ],
            'email' => [
                'rules' => v::email(),
                'message' => 'Debe ser un email válido'
            ],
            'password' => [
                'rules' => self::rules()->password(),
                'message' => 'La contraseña debe tener al menos 3 caracteres'
            ]
        ]);
    }
}