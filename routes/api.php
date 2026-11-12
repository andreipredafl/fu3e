<?php

use App\Http\Controllers\CSVExport;
use Illuminate\Support\Facades\Route;

Route::patch('/csv-export', CSVExport::class);
