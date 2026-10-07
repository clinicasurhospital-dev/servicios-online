<?php

use App\Http\Controllers\Admin\AdminAgendaController;
use App\Http\Controllers\Admin\AdminAppointmentController;
use App\Http\Controllers\Admin\AdminAuditController;
use App\Http\Controllers\Admin\AdminClinicalEncounterController;
use App\Http\Controllers\Admin\AdminClinicalRecordController;
use App\Http\Controllers\Admin\AdminClinicalTemplateController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDoctorController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminPatientController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminSpecialtyController;
use App\Http\Controllers\Admin\AdminTransferController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Patient\PatientPortalController;
use App\Http\Controllers\Public\CedulaLookupController;
use App\Http\Controllers\Public\DoctorReviewPublicController;
use App\Http\Controllers\Public\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Web Routes — SurHospital & Agendamiento
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/agendar', [HomeController::class, 'booking'])->name('booking');
Route::get('/csrf-token', fn () => response()->json(['token' => csrf_token()]))->name('csrf.token');
Route::get('/consultar-cedula/{cedula}', [CedulaLookupController::class, 'lookup'])->name('cedula.lookup');

// Valoración directa pública por Token (WhatsApp / Correo)
Route::get('/valorar/{token}', [DoctorReviewPublicController::class, 'show'])->name('reviews.public.show');
Route::post('/valorar/{token}', [DoctorReviewPublicController::class, 'store'])->name('reviews.public.store');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register'])->middleware('throttle:10,1');

    // Recuperación de Contraseña
    Route::get('/recuperar-contrasena', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/recuperar-contrasena', [AuthController::class, 'sendResetLinkEmail'])->middleware('throttle:6,1')->name('password.email');
    Route::get('/restablecer-contrasena/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/restablecer-contrasena', [AuthController::class, 'resetPassword'])->middleware('throttle:6,1')->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Patient Portal Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:PATIENT|SUPER_ADMIN|ADMIN'])->prefix('paciente')->name('patient.')->group(function () {
    Route::get('/', [PatientPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/citas', [PatientPortalController::class, 'appointments'])->name('appointments');
    Route::get('/citas/{appointment}', [PatientPortalController::class, 'showAppointment'])->name('appointment.show');
    Route::get('/citas/{appointment}/horarios-disponibles', [PatientPortalController::class, 'availableSlots'])->name('appointment.slots');
    Route::post('/citas/{appointment}/reprogramar', [PatientPortalController::class, 'rescheduleAppointment'])->name('appointment.reschedule');
    Route::post('/citas/{appointment}/cancelar', [PatientPortalController::class, 'cancelAppointment'])->name('appointment.cancel');
    Route::post('/citas/{appointment}/transferencia', [PatientPortalController::class, 'submitTransfer'])->name('transfer.submit');
    Route::post('/citas/{appointment}/pagar-tarjeta', [PatientPortalController::class, 'payWithCard'])->name('appointment.pay_card');
    Route::post('/citas/{appointment}/pagar-enlace', [PatientPortalController::class, 'payWithLink'])->name('appointment.pay_link');
    Route::get('/citas/{appointment}/historia-clinica/{type}', [PatientPortalController::class, 'downloadClinicalRecord'])->name('appointment.clinical_record');
    Route::post('/citas/{appointment}/valoracion', [PatientPortalController::class, 'storeReview'])->name('appointment.review');
    Route::get('/pago-exitoso', [PatientPortalController::class, 'paymentSuccess'])->name('payment.success');
});

/*
|--------------------------------------------------------------------------
| Admin / Reception / Doctor Backoffice Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:SUPER_ADMIN|ADMIN|RECEPTION|DOCTOR'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/agenda', [AdminAgendaController::class, 'index'])->name('agenda');
    Route::post('/notificaciones/marcar-todas', [AdminNotificationController::class, 'markAllAsRead'])->name('notifications.mark_all_read');
    Route::post('/notificaciones/{id}/marcar-leida', [AdminNotificationController::class, 'markAsRead'])->name('notifications.mark_read');
    
    // Citas
    Route::get('/citas', [AdminAppointmentController::class, 'index'])->name('appointments');
    Route::post('/citas/{appointment}/estado', [AdminAppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::post('/citas/{appointment}/historia-clinica', [AdminAppointmentController::class, 'uploadClinicalRecord'])->name('appointments.upload_history');
    Route::get('/citas/{appointment}/historia-clinica/{type}', [AdminAppointmentController::class, 'downloadClinicalRecord'])->name('appointments.download_history');
    Route::delete('/citas/{appointment}/historia-clinica/{type}', [AdminAppointmentController::class, 'deleteClinicalRecord'])->name('appointments.delete_history');
    Route::post('/citas/{appointment}/enviar-invitacion-valoracion', [AdminReviewController::class, 'sendInvitation'])->name('appointments.send_review_invitation');

    // Módulo de Sugerencias y Valoraciones Médicas
    Route::get('/valoraciones', [AdminReviewController::class, 'index'])->name('reviews');
    Route::post('/valoraciones/{review}/toggle-visibilidad', [AdminReviewController::class, 'toggleVisibility'])->name('reviews.toggle_visibility');

    // =========================================================================
    // HISTORIA CLÍNICA ELECTRÓNICA (EHR) — NEXARIS LINK / SURHOSPITAL
    // =========================================================================

    // Atención Médica Asistencial (Doctor)
    Route::get('/citas/{appointment}/iniciar-atencion', [AdminClinicalEncounterController::class, 'startFromAppointment'])->name('appointments.start_encounter');
    Route::get('/historia-clinica/atencion/{encounter}', [AdminClinicalEncounterController::class, 'show'])->name('clinical.encounter.show');
    Route::post('/historia-clinica/atencion/{encounter}/borrador', [AdminClinicalEncounterController::class, 'saveDraft'])->name('clinical.encounter.draft');
    Route::post('/historia-clinica/atencion/{encounter}/finalizar', [AdminClinicalEncounterController::class, 'finalize'])->name('clinical.encounter.finalize');
    Route::post('/historia-clinica/atencion/{encounter}/adenda', [AdminClinicalEncounterController::class, 'storeAddendum'])->name('clinical.encounter.addendum');

    // Historia Clínica Longitudinal del Paciente
    Route::get('/pacientes/{patient}/historia-clinica', [AdminClinicalRecordController::class, 'show'])->name('patients.clinical_record');
    Route::post('/pacientes/{patient}/historia-clinica/alergia', [AdminClinicalRecordController::class, 'storeAllergy'])->name('patients.clinical_record.allergy');
    Route::post('/pacientes/{patient}/historia-clinica/condicion', [AdminClinicalRecordController::class, 'storeCondition'])->name('patients.clinical_record.condition');
    Route::post('/pacientes/{patient}/historia-clinica/medicamento', [AdminClinicalRecordController::class, 'storeMedication'])->name('patients.clinical_record.medication');
    Route::post('/pacientes/{patient}/historia-clinica/antecedente', [AdminClinicalRecordController::class, 'storeBackground'])->name('patients.clinical_record.background');
    Route::post('/pacientes/{patient}/historia-clinica/documento', [AdminClinicalRecordController::class, 'uploadDocument'])->name('patients.clinical_record.document_upload');
    Route::get('/historia-clinica/documentos/{document}/descargar', [AdminClinicalRecordController::class, 'downloadDocument'])->name('clinical.documents.download');

    // Configurador de Plantillas (Admin / Super Admin)
    Route::middleware('role:SUPER_ADMIN|ADMIN')->prefix('configuracion/historia-clinica')->name('clinical.templates.')->group(function () {
        Route::get('/plantillas', [AdminClinicalTemplateController::class, 'index'])->name('index');
        Route::post('/plantillas', [AdminClinicalTemplateController::class, 'store'])->name('store');
        Route::get('/plantillas/{template}/builder', [AdminClinicalTemplateController::class, 'builder'])->name('builder');
        Route::post('/plantillas/{template}/duplicar', [AdminClinicalTemplateController::class, 'duplicate'])->name('duplicate');
        Route::post('/plantillas/{template}/nueva-version', [AdminClinicalTemplateController::class, 'newVersion'])->name('new_version');
        Route::post('/versiones/{version}/publicar', [AdminClinicalTemplateController::class, 'publishVersion'])->name('publish');
        
        // Builder AJAX endpoints
        Route::post('/versiones/{version}/secciones', [AdminClinicalTemplateController::class, 'storeSection'])->name('sections.store');
        Route::put('/secciones/{section}', [AdminClinicalTemplateController::class, 'updateSection'])->name('sections.update');
        Route::delete('/secciones/{section}', [AdminClinicalTemplateController::class, 'destroySection'])->name('sections.destroy');
        Route::post('/versiones/{version}/reordenar-secciones', [AdminClinicalTemplateController::class, 'reorderSections'])->name('sections.reorder');
        Route::post('/secciones/{section}/campos', [AdminClinicalTemplateController::class, 'storeField'])->name('fields.store');
        Route::put('/campos/{field}', [AdminClinicalTemplateController::class, 'updateField'])->name('fields.update');
        Route::delete('/campos/{field}', [AdminClinicalTemplateController::class, 'destroyField'])->name('fields.destroy');
        Route::post('/secciones/{section}/reordenar-campos', [AdminClinicalTemplateController::class, 'reorderFields'])->name('fields.reorder');
    });

    // Super Admin & Admin / Reception Only Management Routes
    Route::middleware('role:SUPER_ADMIN|ADMIN|RECEPTION')->group(function () {
        // Pacientes CRUD
        Route::get('/pacientes', [AdminPatientController::class, 'index'])->name('patients');
        Route::post('/pacientes', [AdminPatientController::class, 'store'])->name('patients.store');
        Route::put('/pacientes/{patient}', [AdminPatientController::class, 'update'])->name('patients.update');
        Route::delete('/pacientes/{patient}', [AdminPatientController::class, 'destroy'])->name('patients.destroy');

        // Médicos CRUD & Horarios
        Route::get('/medicos', [AdminDoctorController::class, 'index'])->name('doctors');
        Route::post('/medicos', [AdminDoctorController::class, 'store'])->name('doctors.store');
        Route::put('/medicos/{doctor}', [AdminDoctorController::class, 'update'])->name('doctors.update');
        Route::post('/medicos/{doctor}/toggle', [AdminDoctorController::class, 'toggleActive'])->name('doctors.toggle');
        Route::delete('/medicos/{doctor}', [AdminDoctorController::class, 'destroy'])->name('doctors.destroy');
        Route::post('/medicos/{doctor}/horarios', [AdminDoctorController::class, 'updateSchedules'])->name('doctors.schedules');

        // Especialidades CRUD
        Route::get('/especialidades', [AdminSpecialtyController::class, 'index'])->name('specialties');
        Route::post('/especialidades', [AdminSpecialtyController::class, 'store'])->name('specialties.store');
        Route::put('/especialidades/{specialty}', [AdminSpecialtyController::class, 'update'])->name('specialties.update');
        Route::post('/especialidades/{specialty}/toggle', [AdminSpecialtyController::class, 'toggleActive'])->name('specialties.toggle');
        Route::delete('/especialidades/{specialty}', [AdminSpecialtyController::class, 'destroy'])->name('specialties.destroy');

        // Procedimientos CRUD
        Route::post('/especialidades/{specialty}/procedimientos', [AdminSpecialtyController::class, 'storeProcedure'])->name('specialties.procedures.store');
        Route::put('/procedimientos/{procedure}', [AdminSpecialtyController::class, 'updateProcedure'])->name('procedures.update');
        Route::post('/procedimientos/{procedure}/toggle', [AdminSpecialtyController::class, 'toggleProcedureActive'])->name('procedures.toggle');
        Route::delete('/procedimientos/{procedure}', [AdminSpecialtyController::class, 'destroyProcedure'])->name('procedures.destroy');

        // Pagos / Transferencias
        Route::get('/transferencias', [AdminTransferController::class, 'index'])->name('transfers');
        Route::post('/transferencias/{transfer}/validar', [AdminTransferController::class, 'validateTransfer'])->name('transfers.validate');
        Route::get('/transferencias/{transfer}/comprobante', [AdminTransferController::class, 'downloadReceipt'])->name('transfers.receipt');
    });

    // Auditoría (Admin / Super Admin only)
    Route::middleware('role:SUPER_ADMIN|ADMIN')->group(function () {
        Route::get('/auditoria', [AdminAuditController::class, 'index'])->name('audit');
    });
});
