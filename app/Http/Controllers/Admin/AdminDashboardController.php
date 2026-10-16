<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Payments\Enums\BankTransferStatus;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\BankTransfer;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Specialty;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $isDoctor = $user->isDoctor() && $user->doctor;

        // Base query depending on role
        $appointmentQuery = Appointment::query();
        if ($isDoctor) {
            $appointmentQuery->where('doctor_id', $user->doctor->id);
        }

        // Today's metrics
        $today = Carbon::today();
        $todayAppointments = (clone $appointmentQuery)->whereDate('start_at', $today)->count();
        $todayConfirmed = (clone $appointmentQuery)->whereDate('start_at', $today)->where('status', AppointmentStatus::CONFIRMED)->count();
        $todayPending = (clone $appointmentQuery)->whereDate('start_at', $today)->whereIn('status', [AppointmentStatus::PENDING, AppointmentStatus::PENDING_PAYMENT])->count();
        $todayCancelled = (clone $appointmentQuery)->whereDate('start_at', $today)->where('status', AppointmentStatus::CANCELLED)->count();
        $todayNoShow = (clone $appointmentQuery)->whereDate('start_at', $today)->where('status', AppointmentStatus::NO_SHOW)->count();

        // Doctor specific metrics
        $doctorPatientsCount = 0;
        $doctorMonthAppointments = 0;
        $doctorProfile = null;

        if ($isDoctor) {
            $doctorProfile = $user->doctor()->with(['specialties', 'schedules'])->first();
            $doctorPatientsCount = (clone $appointmentQuery)->distinct('patient_id')->count('patient_id');
            $doctorMonthAppointments = (clone $appointmentQuery)
                ->whereMonth('start_at', Carbon::now()->month)
                ->whereYear('start_at', Carbon::now()->year)
                ->count();
        }

        // Global metrics (Admin / Reception)
        $totalPatients = Patient::count();
        $pendingTransfersCount = BankTransfer::where('validation_status', BankTransferStatus::PENDING_VALIDATION)->count();
        $totalRevenue = Payment::where('status', PaymentStatus::PAID)->sum('amount');
        $monthRevenue = Payment::where('status', PaymentStatus::PAID)
            ->whereMonth('paid_at', Carbon::now()->month)
            ->whereYear('paid_at', Carbon::now()->year)
            ->sum('amount');

        // Distribution by Specialty
        $appointmentsBySpecialty = Specialty::withCount('appointments')
            ->orderBy('appointments_count', 'desc')
            ->take(5)
            ->get();

        // Distribution by Doctor
        $appointmentsByDoctor = Doctor::with(['user', 'specialties'])
            ->withCount('appointments')
            ->orderBy('appointments_count', 'desc')
            ->take(5)
            ->get();

        // Upcoming schedule for today
        $upcomingToday = (clone $appointmentQuery)
            ->whereDate('start_at', $today)
            ->with(['patient', 'doctor.user', 'specialty', 'procedure', 'payments'])
            ->orderBy('start_at', 'asc')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'todayAppointments',
            'todayConfirmed',
            'todayPending',
            'todayCancelled',
            'todayNoShow',
            'totalPatients',
            'pendingTransfersCount',
            'totalRevenue',
            'monthRevenue',
            'appointmentsBySpecialty',
            'appointmentsByDoctor',
            'upcomingToday',
            'isDoctor',
            'doctorPatientsCount',
            'doctorMonthAppointments',
            'doctorProfile'
        ));
    }
}
