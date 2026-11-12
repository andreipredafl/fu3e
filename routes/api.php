<?php

use App\Http\Controllers\CSVExport;
use Illuminate\Support\Facades\Route;

Route::post('/csv-export', CSVExport::class);
