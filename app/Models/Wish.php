<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Wish extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'theme_id',
        'position',
        'content',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($wish) {
            $wish->uuid = Str::uuid();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForTheme($query, $themeId)
    {
        return $query->where('theme_id', $themeId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }
}
