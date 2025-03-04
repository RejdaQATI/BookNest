<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $quote = Inspiring::quote();
    $this->comment($quote);
})->purpose('Display an inspiring quote');
