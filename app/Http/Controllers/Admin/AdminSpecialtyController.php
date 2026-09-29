<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Procedure;
use App\Models\Specialty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminSpecialtyController extends Controller
{
    public function index(): View
    {
        $specialties = Specialty::with(['allProcedures', 'doctors.user'])
            ->withCount(['doctors', 'procedures'])
            ->orderBy('name')
            ->get();

        return view('admin.specialties', compact('specialties'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:specialties,name',
            'description' => 'nullable|string|max:500',
            'base_price' => 'required|numeric|min:0',
            'default_duration_minutes' => 'required|integer|min:10|max:240',
        ], [
            'name.unique' => 'La especialidad ya se encuentra registrada.',
        ]);

        $slug = Str::slug($validated['name']);
        $count = Specialty::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        Specialty::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'base_price' => $validated['base_price'],
            'default_duration_minutes' => $validated['default_duration_minutes'],
            'is_active' => true,
        ]);

        return back()->with('success', "Especialidad '{$validated['name']}' creada exitosamente.");
    }

    public function update(Request $request, Specialty $specialty): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('specialties', 'name')->ignore($specialty->id)],
            'description' => 'nullable|string|max:500',
            'base_price' => 'required|numeric|min:0',
            'default_duration_minutes' => 'required|integer|min:10|max:240',
        ]);

        $specialty->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'base_price' => $validated['base_price'],
            'default_duration_minutes' => $validated['default_duration_minutes'],
        ]);

        return back()->with('success', "Especialidad '{$specialty->name}' actualizada correctamente.");
    }

    public function toggleActive(Specialty $specialty): RedirectResponse
    {
        $newStatus = !$specialty->is_active;
        $specialty->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'activada' : 'desactivada';
        return back()->with('success', "Especialidad '{$specialty->name}' {$statusText} correctamente.");
    }

    public function destroy(Specialty $specialty): RedirectResponse
    {
        if ($specialty->appointments()->exists() || $specialty->doctors()->exists()) {
            $specialty->update(['is_active' => false]);
            return back()->with('warning', "La especialidad '{$specialty->name}' tiene médicos o citas asociadas. Se ha desactivado en su lugar.");
        }

        return DB::transaction(function () use ($specialty) {
            $name = $specialty->name;
            $specialty->procedures()->delete();
            $specialty->delete();

            return back()->with('success', "Especialidad '{$name}' eliminada correctamente.");
        });
    }

    public function storeProcedure(Request $request, Specialty $specialty): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:10|max:240',
        ]);

        $slug = Str::slug($validated['name']);
        $count = Procedure::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        Procedure::create([
            'specialty_id' => $specialty->id,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'duration_minutes' => $validated['duration_minutes'],
            'is_active' => true,
        ]);

        return back()->with('success', "Procedimiento '{$validated['name']}' agregado a {$specialty->name}.");
    }

    public function updateProcedure(Request $request, Procedure $procedure): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:10|max:240',
        ]);

        $procedure->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'duration_minutes' => $validated['duration_minutes'],
        ]);

        return back()->with('success', "Procedimiento '{$procedure->name}' actualizado correctamente.");
    }

    public function toggleProcedureActive(Procedure $procedure): RedirectResponse
    {
        $newStatus = !$procedure->is_active;
        $procedure->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'activado' : 'desactivado';
        return back()->with('success', "Procedimiento '{$procedure->name}' {$statusText} correctamente.");
    }

    public function destroyProcedure(Procedure $procedure): RedirectResponse
    {
        if ($procedure->appointments()->exists()) {
            $procedure->update(['is_active' => false]);
            return back()->with('warning', "El procedimiento '{$procedure->name}' tiene citas asociadas. Se ha desactivado.");
        }

        $name = $procedure->name;
        $procedure->doctors()->detach();
        $procedure->delete();

        return back()->with('success', "Procedimiento '{$name}' eliminado correctamente.");
    }
}
