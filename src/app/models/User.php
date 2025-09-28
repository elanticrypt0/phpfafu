<?php

require_once __DIR__ . '/../config/eloquent.php';

/**
 * Modelo User usando Eloquent ORM
 *
 * Este modelo extiende BaseModel que incluye funcionalidades
 * para manejar múltiples conexiones de base de datos.
 */
class User extends BaseModel
{
    /**
     * La tabla asociada al modelo
     */
    protected $table = 'users';

    /**
     * La clave primaria de la tabla
     */
    protected $primaryKey = 'id';

    /**
     * Indica si el modelo debe usar timestamps
     */
    public $timestamps = true;

    /**
     * Los nombres de las columnas de timestamp
     */
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Los atributos que se pueden asignar masivamente
     */
    protected $fillable = [
        'username',
        'email',
        'password_hash'
    ];

    /**
     * Los atributos que deben ocultarse en las arrays
     */
    protected $hidden = [
        'password_hash'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope para usuarios activos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para buscar por email
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Scope para buscar por username
     */
    public function scopeByUsername($query, $username)
    {
        return $query->where('username', $username);
    }

    /**
     * Mutator para hashear la contraseña automáticamente
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verificar contraseña
     */
    public function verifyPassword($password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    /**
     * Obtener el nombre completo del usuario
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name) ?: $this->username;
    }

    /**
     * Verificar si el usuario es admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Obtener usuario por email
     */
    public static function findByEmail(string $email): ?self
    {
        return static::byEmail($email)->first();
    }

    /**
     * Obtener usuario por username
     */
    public static function findByUsername(string $username): ?self
    {
        return static::byUsername($username)->first();
    }

    /**
     * Crear un nuevo usuario
     */
    public static function createUser(array $data): self
    {
        return static::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'role' => $data['role'] ?? 'user',
        ]);
    }

    /**
     * Autenticar usuario
     */
    public static function authenticate(string $username, string $password): ?self
    {
        $user = static::byUsername($username)->orWhere('email', $username)->first();

        if ($user && $user->verifyPassword($password)) {
            return $user;
        }

        return null;
    }

    /**
     * Obtener usuarios usando conexión específica
     */
    public static function fromConnection(string $connection)
    {
        return (new static)->setConnection($connection);
    }

    /**
     * Obtener estadísticas de usuarios
     */
    public static function getStats(): array
    {
        return [
            'total_users' => static::count(),
            'active_users' => static::where('active', true)->count(),
            'admin_users' => static::where('role', 'admin')->count(),
            'recent_users' => static::where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    /**
     * Buscar usuarios
     */
    public static function search(string $term)
    {
        return static::where('username', 'LIKE', "%{$term}%")
                    ->orWhere('email', 'LIKE', "%{$term}%")
                    ->orWhere('first_name', 'LIKE', "%{$term}%")
                    ->orWhere('last_name', 'LIKE', "%{$term}%");
    }

    /**
     * Obtener usuarios paginados
     */
    public static function paginated(int $page = 1, int $perPage = 15)
    {
        $offset = ($page - 1) * $perPage;

        return [
            'data' => static::skip($offset)->take($perPage)->get(),
            'total' => static::count(),
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil(static::count() / $perPage),
        ];
    }

    /**
     * Validar datos de usuario
     */
    public static function validateUserData(array $data, bool $isUpdate = false): FormValidator
    {
        $rules = [
            'username' => 'required|alpha_num|between:3,20',
            'email' => 'required|email',
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'role' => 'nullable|in:user,admin,moderator',
        ];

        if (!$isUpdate) {
            $rules['password'] = 'required|min:6';
            $rules['password_confirmation'] = 'required_with:password|same:password';
        }

        return FormValidator::make($data)->validate($rules);
    }

    /**
     * Helper para obtener la conexión Carbon (si está disponible)
     */
    protected function freshTimestamp()
    {
        return date('Y-m-d H:i:s');
    }
}