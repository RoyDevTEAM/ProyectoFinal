<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Model
{
    protected $table = 'usuarios';
    protected $fillable = ['username', 'email', 'password_hash', 'rol_id', 'verificado'];

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class);
    }

    public function cursosCreados(): HasMany
    {
        return $this->hasMany(Curso::class, 'profesor_id');
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'estudiante_id');
    }

    public function tokensVerificacion(): HasMany
    {
        return $this->hasMany(TokenVerificacion::class);
    }

    public function tokensResetPassword(): HasMany
    {
        return $this->hasMany(TokenResetPassword::class);
    }

    public function logsAcceso(): HasMany
    {
        return $this->hasMany(LogAcceso::class);
    }

    public function logsCambioPassword(): HasMany
    {
        return $this->hasMany(LogCambioPassword::class);
    }
}