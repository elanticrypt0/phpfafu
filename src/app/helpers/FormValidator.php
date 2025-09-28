<?php

use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Validation\Validator;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Container\Container;

/**
 * Helper para validación de formularios usando Laravel Validator
 *
 * Esta clase reemplaza Respect\Validation con Laravel Validator
 * para mayor consistencia y funcionalidad avanzada.
 */
class FormValidator
{
    private array $errors = [];
    private array $data = [];
    private ValidatorFactory $validatorFactory;
    private static $factoryInstance = null;

    public function __construct(array $data = [])
    {
        $this->data = $data;
        $this->validatorFactory = self::getValidatorFactory();
    }

    /**
     * Obtener o crear la instancia del validador factory
     */
    private static function getValidatorFactory(): ValidatorFactory
    {
        if (self::$factoryInstance === null) {
            // Configurar el traductor con mensajes en español
            $translator = new Translator(new ArrayLoader(), 'es');
            $translator->addLines([
                'validation.required' => 'El campo :attribute es requerido.',
                'validation.email' => 'El campo :attribute debe ser un email válido.',
                'validation.min.string' => 'El campo :attribute debe tener al menos :min caracteres.',
                'validation.max.string' => 'El campo :attribute no puede tener más de :max caracteres.',
                'validation.between.string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
                'validation.alpha' => 'El campo :attribute solo puede contener letras.',
                'validation.alpha_num' => 'El campo :attribute solo puede contener letras y números.',
                'validation.numeric' => 'El campo :attribute debe ser numérico.',
                'validation.integer' => 'El campo :attribute debe ser un entero.',
                'validation.string' => 'El campo :attribute debe ser una cadena de texto.',
                'validation.confirmed' => 'La confirmación de :attribute no coincide.',
                'validation.unique' => 'El :attribute ya está en uso.',
                'validation.exists' => 'El :attribute seleccionado no es válido.',
                'validation.regex' => 'El formato del campo :attribute es inválido.',
                'validation.same' => 'El campo :attribute y :other deben coincidir.',
                'validation.different' => 'El campo :attribute y :other deben ser diferentes.',
                'validation.in' => 'El campo :attribute seleccionado es inválido.',
                'validation.not_in' => 'El campo :attribute seleccionado es inválido.',
                'validation.size.string' => 'El campo :attribute debe tener exactamente :size caracteres.',
                'validation.boolean' => 'El campo :attribute debe ser verdadero o falso.',
                'validation.date' => 'El campo :attribute no es una fecha válida.',
                'validation.url' => 'El campo :attribute debe ser una URL válida.',
                'validation.json' => 'El campo :attribute debe ser una cadena JSON válida.',
            ], 'es');

            self::$factoryInstance = new ValidatorFactory($translator, new Container());
        }

        return self::$factoryInstance;
    }

    /**
     * Validar múltiples campos con reglas
     */
    public function validate(array $rules, array $customMessages = []): self
    {
        $validator = $this->validatorFactory->make($this->data, $rules, $customMessages);

        if ($validator->fails()) {
            $this->errors = array_merge($this->errors, $validator->errors()->toArray());
        }

        return $this;
    }

    /**
     * Validar un campo específico con reglas
     */
    public function validateField(string $field, $rules, string $customMessage = null): self
    {
        $fieldRules = [$field => $rules];
        $messages = $customMessage ? [$field => $customMessage] : [];

        return $this->validate($fieldRules, $messages);
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
        $fieldErrors = $this->errors[$field] ?? null;
        return is_array($fieldErrors) ? $fieldErrors[0] : $fieldErrors;
    }

    /**
     * Obtener el primer error
     */
    public function getFirstError(): ?string
    {
        if (empty($this->errors)) {
            return null;
        }

        $firstField = array_values($this->errors)[0];
        return is_array($firstField) ? $firstField[0] : $firstField;
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
     * Reglas de validación comunes usando Laravel Validator
     */
    public static function rules(): object
    {
        return new class {
            public function required(): string
            {
                return 'required';
            }

            public function email(): string
            {
                return 'email';
            }

            public function minLength(int $min): string
            {
                return "min:$min";
            }

            public function maxLength(int $max): string
            {
                return "max:$max";
            }

            public function length(int $min, int $max): string
            {
                return "between:$min,$max";
            }

            public function alpha(): string
            {
                return 'alpha';
            }

            public function alphaNum(): string
            {
                return 'alpha_num';
            }

            public function numeric(): string
            {
                return 'numeric';
            }

            public function integer(): string
            {
                return 'integer';
            }

            public function string(): string
            {
                return 'string';
            }

            public function username(): string
            {
                return 'alpha_num|between:3,20';
            }

            public function password(): string
            {
                return 'min:3'; // Mínimo 3 caracteres para demo
            }

            public function strongPassword(): string
            {
                return 'min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/';
            }

            public function confirmed(): string
            {
                return 'confirmed';
            }

            public function unique(string $table, string $column = null): string
            {
                $column = $column ?: 'email';
                return "unique:$table,$column";
            }

            public function exists(string $table, string $column = null): string
            {
                $column = $column ?: 'id';
                return "exists:$table,$column";
            }

            public function url(): string
            {
                return 'url';
            }

            public function date(): string
            {
                return 'date';
            }

            public function boolean(): string
            {
                return 'boolean';
            }

            public function json(): string
            {
                return 'json';
            }

            public function in(array $values): string
            {
                return 'in:' . implode(',', $values);
            }

            public function notIn(array $values): string
            {
                return 'not_in:' . implode(',', $values);
            }
        };
    }

    /**
     * Validación específica para login
     */
    public static function validateLogin(array $data): self
    {
        return self::make($data)->validate([
            'username' => self::rules()->required(),
            'password' => self::rules()->required()
        ], [
            'username.required' => 'El usuario es requerido',
            'password.required' => 'La contraseña es requerida'
        ]);
    }

    /**
     * Validación para registro de usuario
     */
    public static function validateRegistration(array $data): self
    {
        return self::make($data)->validate([
            'username' => self::rules()->username(),
            'email' => self::rules()->email(),
            'password' => self::rules()->password(),
            'password_confirmation' => 'required_with:password|same:password'
        ], [
            'username.alpha_num' => 'El usuario solo puede contener letras y números',
            'username.between' => 'El usuario debe tener entre 3 y 20 caracteres',
            'email.email' => 'Debe ser un email válido',
            'password.min' => 'La contraseña debe tener al menos 3 caracteres',
            'password_confirmation.same' => 'La confirmación de contraseña no coincide'
        ]);
    }

    /**
     * Validación para contraseña fuerte
     */
    public static function validateStrongPassword(array $data): self
    {
        return self::make($data)->validate([
            'password' => self::rules()->strongPassword(),
            'password_confirmation' => 'required_with:password|same:password'
        ], [
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula y un número',
            'password_confirmation.same' => 'La confirmación de contraseña no coincide'
        ]);
    }

    /**
     * Validación para email único
     */
    public static function validateUniqueEmail(array $data, string $table = 'users', int $ignoreId = null): self
    {
        $uniqueRule = $ignoreId
            ? "unique:$table,email,$ignoreId"
            : self::rules()->unique($table, 'email');

        return self::make($data)->validate([
            'email' => self::rules()->email() . "|$uniqueRule"
        ], [
            'email.email' => 'Debe ser un email válido',
            'email.unique' => 'Este email ya está registrado'
        ]);
    }

    /**
     * Validación para formulario de contacto
     */
    public static function validateContact(array $data): self
    {
        return self::make($data)->validate([
            'name' => 'required|string|min:2|max:100',
            'email' => self::rules()->email(),
            'subject' => 'required|string|min:5|max:200',
            'message' => 'required|string|min:10|max:1000'
        ], [
            'name.required' => 'El nombre es requerido',
            'name.min' => 'El nombre debe tener al menos 2 caracteres',
            'name.max' => 'El nombre no puede tener más de 100 caracteres',
            'email.email' => 'Debe ser un email válido',
            'subject.required' => 'El asunto es requerido',
            'subject.min' => 'El asunto debe tener al menos 5 caracteres',
            'message.required' => 'El mensaje es requerido',
            'message.min' => 'El mensaje debe tener al menos 10 caracteres'
        ]);
    }
}