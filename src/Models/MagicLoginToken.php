<?php

namespace Primalmaxor\MagicPass\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MagicLoginToken extends Model
{
    protected $fillable = ['user_id', 'code', 'expires_at'];

    protected $dates = ['expires_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
