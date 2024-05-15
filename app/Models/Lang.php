<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lang extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $keyType = "string";
    public $incrementing = false;

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class);
    }

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        if ($childType === "term") {
            return $this->terms()
                ->getQuery()
                ->whereBelongsTo(request()->user(), "owner")
                ->where($field ?? "term", $value)
                ->firstOrFail();
        } else {
            return parent::resolveChildRouteBinding($childType, $value, $field);
        }
    }
}
