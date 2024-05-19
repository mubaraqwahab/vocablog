<?php

use App\Models\Definition;
use App\Models\Term;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command("inspire", function () {
    $this->comment(Inspiring::quote());
})->purpose("Display an inspiring quote");
