<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Procedure;
use App\Models\Specialty;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $specialties = Specialty::where('is_active', true)
            ->with(['procedures' => fn ($q) => $q->where('is_active', true)])
            ->withCount('doctors')
            ->get();

        $doctors = Doctor::where('is_active', true)
            ->with(['user', 'specialties'])
            ->take(6)
            ->get();

        return view('public.home', compact('specialties', 'doctors'));
    }

    public function booking(): View
    {
        return view('public.booking');
    }
}
