<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenResetPassword extends Model
{
    protected $table = 'tokens_reset_password';
    protected $fillable = ['usuario_id', 'token', 'expires_at'];
    public $timestamps = false;

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}