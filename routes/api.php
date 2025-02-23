<?php

use App\Http\Controllers\IngestController;
use Illuminate\Support\Facades\Route;

Route::post('/ingest/{monitor}', IngestController::class);
