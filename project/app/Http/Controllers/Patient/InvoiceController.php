<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $patient = auth()->user()->patient;
        abort_unless($patient, 403);

        $query = $patient->invoices()->with('doctor');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        return view('patient.invoices.index', compact('invoices'));
    }
}
