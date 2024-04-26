<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lang extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class);
    }
}
