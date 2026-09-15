<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Procedure;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSpecialtyController extends Controller
{
    public function index(): View
    {
        $specialties = Specialty::with(['procedures', 'doctors.user'])
            ->withCount(['doctors', 'procedures'])
            ->orderBy('name')
            ->get();

        return view('admin.specialties', compact('specialties'));
    }
}
