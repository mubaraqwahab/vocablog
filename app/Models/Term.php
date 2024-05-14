<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Term extends Model
{
    use HasFactory;

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ["lang"];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lang(): BelongsTo
    {
        return $this->belongsTo(Lang::class);
    }

    public function definitions(): HasMany
    {
        return $this->hasMany(Definition::class);
    }

    public function getRouteKeyName()
    {
        return "term";
    }
}
