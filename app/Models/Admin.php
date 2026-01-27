<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'is_super_admin',
        'two_factor_enabled_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'two_factor_enabled_at' => 'datetime',
            'two_factor_recovery_codes' => 'array',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($admin) {
            $admin->uuid = Str::uuid();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function activityLogs()
    {
        return $this->hasMany(AdminActivityLog::class);
    }

    public function hasTwoFactorEnabled()
    {
        return !is_null($this->two_factor_enabled_at);
    }

    public function logActivity(string $action, array $meta = [])
    {
        AdminActivityLog::log($this->id, $action, $meta);
    }
}
