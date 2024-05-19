<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Term extends Model
{
    use HasFactory;

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
        return $this->hasMany(Definition::class)->orderBy("id");
    }

    public function getRouteKeyName()
    {
        return "name";
    }
}
