<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAcceso extends Model
{
    protected $table = 'logs_acceso';
    protected $fillable = ['usuario_id', 'accion', 'ip', 'fecha'];
    public $timestamps = false;

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}