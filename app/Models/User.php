<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
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
        'two_factor_secret',
        'two_factor_recovery_codes',
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
            'two_factor_enabled_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_recovery_codes' => 'array',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->uuid = Str::uuid();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function wishes()
    {
        return $this->hasMany(Wish::class);
    }

    public function wishesForTheme(Theme $theme)
    {
        return $this->wishes()->forTheme($theme->id)->ordered();
    }

    public function activityLogs()
    {
        return $this->hasMany(UserActivityLog::class);
    }

    public function hasTwoFactorEnabled()
    {
        return !is_null($this->two_factor_enabled_at);
    }

    public function logActivity(string $action, array $meta = [])
    {
        UserActivityLog::log($this->id, $action, $meta);
    }

    /**
     * Send the email verification notification with our custom template.
     *
     * @param  mixed  $token
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            \Illuminate\Support\Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $this->getKey(),
                'hash' => sha1($this->getEmailForVerification()),
            ]
        );

        \Illuminate\Support\Facades\Mail::to($this->email)
            ->send(new \App\Mail\VerifyEmail($this, $verificationUrl));
    }
}
