<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class UserActivityLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($log) {
            $log->created_at = now();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(int $userId, string $action, array $meta = [])
    {
        $defaultMeta = [
            'ip' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ];

        return static::create([
            'user_id' => $userId,
            'action' => $action,
            'meta' => array_merge($defaultMeta, $meta),
        ]);
    }
}
