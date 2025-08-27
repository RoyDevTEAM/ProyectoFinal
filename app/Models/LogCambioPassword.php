<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogCambioPassword extends Model
{
    protected $table = 'logs_cambios_password';
    protected $fillable = ['usuario_id', 'fecha_cambio'];
    public $timestamps = false;

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}