<?php

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'user_id',
    ];

    public $timestamps = false;

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope para obtener configuraciones de un usuario específico
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Configuraciones globales (sin usuario)
    public function scopeGlobal($query)
    {
        return $query->whereNull('user_id');
    }
}
