<?php

namespace App\Http\Controllers;

use App\Models\Biodata;
use PDF;
use Illuminate\Http\Request;

class PDFController extends Controller
{
    public function generatePDF($nim)
    {
        $data = Biodata::where('nim', $nim)->first();
          
        $pdf = PDF::loadView('pdf', ['data' => $data])->setOptions(['defaultFont' => 'sans-serif']);
        return $pdf->stream();
    }
}
