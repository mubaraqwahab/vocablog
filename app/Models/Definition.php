<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Definition extends Model
{
    use HasFactory;

    protected $fillable = ["text", "comment", "examples"];

    protected function casts()
    {
        return ["examples" => "array"];
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
