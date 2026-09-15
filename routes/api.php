<?php

use App\Domain\Availability\Services\AvailabilityService;
use App\Domain\Payments\Contracts\PaymentGatewayInterface;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Nexaris Link
|--------------------------------------------------------------------------
*/

// Payment Gateway Webhook (Idempotent)
Route::post('/webhooks/payment', function (Request $request, PaymentGatewayInterface $gateway) {
    $result = $gateway->handleWebhook($request);

    return response()->json([
        'success' => $result->isValid,
        'status' => $result->paymentStatus->value,
        'transaction_id' => $result->gatewayTransactionId,
    ]);
})->name('api.webhooks.payment');

// Real-time Availability API endpoint
Route::get('/availability/slots', function (Request $request, AvailabilityService $availabilityService) {
    $request->validate([
        'doctor_id' => 'required|exists:doctors,id',
        'date' => 'required|date_format:Y-m-d',
        'duration' => 'nullable|integer',
        'specialty_id' => 'nullable|exists:specialties,id',
        'procedure_id' => 'nullable|exists:procedures,id',
    ]);

    $doctor = Doctor::findOrFail($request->input('doctor_id'));
    $date = Carbon::parse($request->input('date'));
    $duration = $request->input('duration') ? (int) $request->input('duration') : null;
    $specialtyId = $request->input('specialty_id') ? (int) $request->input('specialty_id') : null;
    $procedureId = $request->input('procedure_id') ? (int) $request->input('procedure_id') : null;

    $slots = $availabilityService->getAvailableSlotsForDate(
        doctor: $doctor,
        date: $date,
        durationMinutes: $duration,
        specialtyId: $specialtyId,
        procedureId: $procedureId
    );

    return response()->json([
        'date' => $date->toDateString(),
        'doctor_id' => $doctor->id,
        'doctor_name' => $doctor->name,
        'slots_count' => $slots->count(),
        'slots' => $slots->map->toArray(),
    ]);
})->name('api.availability.slots');
