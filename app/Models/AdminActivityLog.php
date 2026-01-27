<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'admin_id',
        'action',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public static function log(int $adminId, string $action, array $meta = [])
    {
        return self::create([
            'admin_id' => $adminId,
            'action' => $action,
            'meta' => array_merge($meta, [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]),
        ]);
    }
}
