<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_MODERATOR = 'moderator';
    const ROLE_VIEWER = 'viewer';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
    public function isModerator(): bool
    {
        return $this->role === self::ROLE_MODERATOR;
    }

    public function isViewer(): bool
    {
        return $this->role === self::ROLE_VIEWER;
    }
    
    /**
     * Boot метод для регистрации событий модели
     */
    protected static function boot()
    {
        parent::boot();
        
        // Очистка кэша при создании пользователя
        static::created(function ($user) {
            Cache::forget('admin.stats');
            Cache::forget('admin.registrations');
        });
        
        // Очистка кэша при обновлении пользователя
        // Примечание: очистка top_authors при изменении количества рецептов
        // обрабатывается в RecipeService при создании/обновлении/удалении рецептов
        static::updated(function ($user) {
            Cache::forget('admin.stats');
            Cache::forget('admin.registrations');
        });
        
        // Очистка кэша при удалении пользователя
        static::deleted(function ($user) {
            Cache::forget('admin.stats');
            Cache::forget('admin.top_authors');
        });
    }
}
