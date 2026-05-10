<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function appointments(Request $request): View
    {
        $query = Appointment::with(['patient', 'doctor', 'doctor.department']);

        if ($from = $request->input('from')) {
            $query->whereDate('appointment_date', '>=', $from);
        }

        if ($to = $request->input('to')) {
            $query->whereDate('appointment_date', '<=', $to);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')->paginate(50)->withQueryString();

        $summary = [
            'total'       => $query->toBase()->getCountForPagination(),
            'by_status'   => Appointment::selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status'),
        ];

        return view('admin.reports.appointments', compact('appointments', 'summary'));
    }

    public function exportAppointments(Request $request): Response
    {
        $query = Appointment::with(['patient', 'doctor']);

        if ($from = $request->input('from')) {
            $query->whereDate('appointment_date', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('appointment_date', '<=', $to);
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $filename = 'appointments-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['ID', 'Patient', 'Doctor', 'Date', 'Time', 'Type', 'Status', 'Notes']);

            $query->chunk(500, function ($appointments) use ($handle) {
                foreach ($appointments as $appt) {
                    fputcsv($handle, [
                        $appt->id,
                        $appt->patient->full_name ?? '',
                        $appt->doctor->full_name ?? '',
                        $appt->appointment_date?->format('Y-m-d'),
                        $appt->appointment_time,
                        $appt->type->value ?? '',
                        $appt->status->value ?? '',
                        $appt->notes,
                    ]);
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function revenue(Request $request): View
    {
        $year  = $request->input('year', now()->year);
        $month = $request->input('month');

        $query = Payment::selectRaw('YEAR(payment_date) as year, MONTH(payment_date) as month, SUM(amount_paid) as total')
            ->whereYear('payment_date', $year);

        if ($month) {
            $query->whereMonth('payment_date', $month);
        }

        $revenue = $query->groupByRaw('YEAR(payment_date), MONTH(payment_date)')
            ->orderByRaw('YEAR(payment_date), MONTH(payment_date)')
            ->get();

        $totalRevenue = Payment::whereYear('payment_date', $year)->sum('amount_paid');

        return view('admin.reports.revenue', compact('revenue', 'year', 'month', 'totalRevenue'));
    }

    public function exportRevenue(Request $request): Response
    {
        $year = $request->input('year', now()->year);

        $filename = 'revenue-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($year) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Payment ID', 'Invoice Number', 'Patient', 'Amount', 'Method', 'Date', 'Reference']);

            Payment::with(['invoice.patient'])
                ->whereYear('payment_date', $year)
                ->chunk(500, function ($payments) use ($handle) {
                    foreach ($payments as $payment) {
                        fputcsv($handle, [
                            $payment->id,
                            $payment->invoice->invoice_number ?? '',
                            $payment->invoice->patient->full_name ?? '',
                            $payment->amount_paid,
                            $payment->payment_method->value ?? '',
                            $payment->payment_date?->format('Y-m-d'),
                            $payment->reference_number,
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function patients(Request $request): View
    {
        $year = $request->input('year', now()->year);

        $registrations = Patient::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at), MONTH(created_at)')
            ->get();

        $totalPatients = Patient::count();
        $newThisMonth  = Patient::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        return view('admin.reports.patients', compact('registrations', 'year', 'totalPatients', 'newThisMonth'));
    }
}
