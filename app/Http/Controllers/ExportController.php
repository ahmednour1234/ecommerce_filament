<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ExportController extends Controller
{
    public function print(Request $request): View
    {
        $printData = session('print_data', [
            'title' => 'Report',
            'headers' => [],
            'rows' => [],
            'metadata' => [],
        ]);

        return view('exports.table-print', $printData);
    }
}

