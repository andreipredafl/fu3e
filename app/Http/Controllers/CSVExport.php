<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CSVExport
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'received' => $request->all(),
            'message' => 'CSV export request received.',
        ]);
    }
}
