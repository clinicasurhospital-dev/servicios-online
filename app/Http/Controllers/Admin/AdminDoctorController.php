<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDoctorController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $specialtyId = $request->query('specialty_id');

        $doctors = Doctor::query()
            ->with(['user', 'specialties', 'schedules'])
            ->withCount('appointments')
            ->when($search, function ($query, $term) {
                $query->whereHas('user', function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                })->orWhere('license_number', 'like', "%{$term}%");
            })
            ->when($specialtyId, function ($query, $id) {
                $query->whereHas('specialties', fn ($q) => $q->where('specialties.id', $id));
            })
            ->orderBy('is_active', 'desc')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();

        return view('admin.doctors', compact('doctors', 'specialties', 'search', 'specialtyId'));
    }
}
