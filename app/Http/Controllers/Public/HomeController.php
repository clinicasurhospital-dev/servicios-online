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
            ->get();

        return view('public.home', compact('specialties', 'doctors'));
    }

    public function booking(): View|\Illuminate\Http\RedirectResponse
    {
        $branding = app(\App\Domain\Branding\BrandingService::class);
        if (!$branding->canShowBookingButton()) {
            return redirect()->route('home')->with('info', 'El agendamiento en línea estará disponible próximamente para el público general. Si requieres agendar una cita, comunícate con recepción al ' . config('branding.contact.phone') . ' o vía WhatsApp al ' . config('branding.contact.whatsapp') . '.');
        }

        return view('public.booking');
    }
}
