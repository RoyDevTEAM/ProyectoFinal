<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodigoOtp extends Model
{
    protected $table = 'codigos_otp';
    protected $fillable = ['usuario_id', 'codigo', 'expira_en', 'utilizado'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}