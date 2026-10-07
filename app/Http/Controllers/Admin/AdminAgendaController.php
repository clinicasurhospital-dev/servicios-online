<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Specialty;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAgendaController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $viewMode = $request->input('view', 'day'); // 'day' or 'week'
        $selectedDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();
        $selectedDoctorId = $request->input('doctor_id');
        $selectedSpecialtyId = $request->input('specialty_id');

        // If user is a Doctor, constrain to their own doctor profile
        if ($user->isDoctor() && $user->doctor) {
            $selectedDoctorId = $user->doctor->id;
        }

        $doctors = Doctor::where('is_active', true)->with(['user', 'specialties'])->get();
        $specialties = Specialty::where('is_active', true)->orderBy('name', 'asc')->get();

        $query = Appointment::with(['patient', 'doctor.user', 'specialty', 'procedure', 'payments']);

        if ($selectedDoctorId) {
            $query->where('doctor_id', $selectedDoctorId);
        }

        if ($selectedSpecialtyId) {
            $query->where('specialty_id', $selectedSpecialtyId);
        }

        if ($viewMode === 'week') {
            $startOfWeek = $selectedDate->copy()->startOfWeek();
            $endOfWeek = $selectedDate->copy()->endOfWeek();
            $appointments = $query->whereBetween('start_at', [$startOfWeek, $endOfWeek])
                ->orderBy('start_at', 'asc')
                ->get();
        } else {
            $appointments = $query->whereDate('start_at', $selectedDate)
                ->orderBy('start_at', 'asc')
                ->get();
        }

        return view('admin.agenda', compact(
            'appointments',
            'doctors',
            'specialties',
            'selectedDate',
            'viewMode',
            'selectedDoctorId',
            'selectedSpecialtyId'
        ));
    }
}
