<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Definition extends Model
{
    use HasFactory;

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function examples(): HasMany
    {
        return $this->hasMany(Example::class);
    }
}
