<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function appointmentsPerDay()
    {
        $data = Appointment::select(
                DB::raw('DATE(appointment_date) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('appointment_date', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(appointment_date)'))
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $data->pluck('date'),
            'data'   => $data->pluck('total'),
        ]);
    }

    public function appointmentsByType()
    {
        $data = Appointment::select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->get();

        return response()->json([
            'labels' => $data->pluck('type'),
            'data'   => $data->pluck('total'),
        ]);
    }

    public function revenuePerMonth()
    {
        $data = Payment::select(
                DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as month'),
                DB::raw('SUM(amount_paid) as total')
            )
            ->where('payment_date', '>=', now()->subMonths(12))
            ->groupBy(DB::raw('DATE_FORMAT(payment_date, "%Y-%m")'))
            ->orderBy('month')
            ->get();

        return response()->json([
            'labels' => $data->pluck('month'),
            'data'   => $data->pluck('total'),
        ]);
    }

    public function patientRegistrations()
    {
        $data = Patient::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
            ->orderBy('month')
            ->get();

        return response()->json([
            'labels' => $data->pluck('month'),
            'data'   => $data->pluck('total'),
        ]);
    }
}
