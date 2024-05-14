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

    public function getRouteKeyName()
    {
        // Bind {lang} params in routes to the lang code not id
        return "code";
    }

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        dd("bind", $childType);
        if ($childType === "term") {
            return $this->terms()
                ->getQuery()
                ->whereBelongsTo(request()->user(), "owner")
                ->where("term", $value)
                ->firstOrFail();
        } else {
            return parent::resolveChildRouteBinding($childType, $value, $field);
        }
    }
}
