<?php

use App\Http\Controllers\Admin\AdminAgendaController;
use App\Http\Controllers\Admin\AdminAppointmentController;
use App\Http\Controllers\Admin\AdminAuditController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDoctorController;
use App\Http\Controllers\Admin\AdminPatientController;
use App\Http\Controllers\Admin\AdminSpecialtyController;
use App\Http\Controllers\Admin\AdminTransferController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Patient\PatientPortalController;
use App\Http\Controllers\Public\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Web Routes — SurHospital & Agendamiento
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/agendar', [HomeController::class, 'booking'])->name('booking');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Patient Portal Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('paciente')->name('patient.')->group(function () {
    Route::get('/', [PatientPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/citas', [PatientPortalController::class, 'appointments'])->name('appointments');
    Route::get('/citas/{appointment}', [PatientPortalController::class, 'showAppointment'])->name('appointment.show');
    Route::post('/citas/{appointment}/cancelar', [PatientPortalController::class, 'cancelAppointment'])->name('appointment.cancel');
    Route::post('/citas/{appointment}/transferencia', [PatientPortalController::class, 'submitTransfer'])->name('transfer.submit');
    Route::get('/pago-exitoso', [PatientPortalController::class, 'paymentSuccess'])->name('payment.success');
});

/*
|--------------------------------------------------------------------------
| Admin / Reception / Doctor Backoffice Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/agenda', [AdminAgendaController::class, 'index'])->name('agenda');
    Route::get('/citas', [AdminAppointmentController::class, 'index'])->name('appointments');
    Route::post('/citas/{appointment}/estado', [AdminAppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::get('/pacientes', [AdminPatientController::class, 'index'])->name('patients');
    Route::get('/medicos', [AdminDoctorController::class, 'index'])->name('doctors');
    Route::get('/especialidades', [AdminSpecialtyController::class, 'index'])->name('specialties');
    Route::get('/transferencias', [AdminTransferController::class, 'index'])->name('transfers');
    Route::post('/transferencias/{transfer}/validar', [AdminTransferController::class, 'validateTransfer'])->name('transfers.validate');
    Route::get('/transferencias/{transfer}/comprobante', [AdminTransferController::class, 'downloadReceipt'])->name('transfers.receipt');
    Route::get('/auditoria', [AdminAuditController::class, 'index'])->name('audit');
});
