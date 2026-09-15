<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPatientController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $patients = Patient::query()
            ->with(['user'])
            ->withCount('appointments')
            ->when($search, function ($query, $term) {
                $query->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('identification_number', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhereHas('user', fn ($q) => $q->where('email', 'like', "%{$term}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.patients', compact('patients', 'search'));
    }
}
