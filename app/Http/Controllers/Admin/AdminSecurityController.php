<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AdminSecurityController extends Controller
{
    public function index(Request $request): View
    {
        $roles = Role::with('permissions')->get();

        // System Modules and their underlying permissions
        $modules = [
            'dashboard' => [
                'name' => 'Panel de Control & Métricas',
                'description' => 'Visualización de estadísticas, citas del día e indicadores generales.',
                'permissions' => [
                    'view_dashboard' => 'Ver Dashboard',
                    'view_metrics' => 'Ver Métricas Financieras y Estadísticas',
                ],
            ],
            'appointments' => [
                'name' => 'Agenda Médica & Citas',
                'description' => 'Consulta de calendario institucional y programación asistencial.',
                'permissions' => [
                    'view_all_appointments' => 'Ver Todas las Citas de la Clínica',
                    'create_appointment_manual' => 'Crear Citas Manualmente desde Recepción',
                    'manage_appointments' => 'Modificar Estados y Reprogramar Citas',
                    'view_doctor_own_agenda' => 'Ver Únicamente la Agenda Propia del Especialista',
                ],
            ],
            'patients' => [
                'name' => 'Directorio de Pacientes',
                'description' => 'Registro de pacientes, datos de filiación y contacto.',
                'permissions' => [
                    'view_patients' => 'Consultar Directorio de Pacientes',
                    'manage_patients' => 'Registrar, Modificar y Eliminar Pacientes',
                ],
            ],
            'doctors' => [
                'name' => 'Médicos, Especialistas & Horarios',
                'description' => 'Gestión del equipo médico y configuración de franjas de atención.',
                'permissions' => [
                    'manage_doctors' => 'Registrar y Editar Médicos / Especialistas',
                    'manage_schedules' => 'Configurar Horarios Semanales y Bloqueos',
                ],
            ],
            'specialties' => [
                'name' => 'Especialidades & Procedimientos',
                'description' => 'Catálogo de especialidades clínicas, procedimientos y aranceles.',
                'permissions' => [
                    'manage_specialties' => 'Administrar Especialidades Médicas',
                    'manage_procedures' => 'Administrar Procedimientos y Subprocedimientos',
                ],
            ],
            'payments' => [
                'name' => 'Validación de Pagos & Transferencias',
                'description' => 'Revisión y validación de transferencias bancarias y comprobantes.',
                'permissions' => [
                    'validate_bank_transfers' => 'Validar Comprobantes de Transferencia',
                    'manage_payments' => 'Gestionar Pagos y Reembolsos',
                ],
            ],
            'clinical' => [
                'name' => 'Historia Clínica Electrónica (EHR)',
                'description' => 'Expedientes longitudinales, atención de consultas y plantillas.',
                'permissions' => [
                    'view_clinical_records' => 'Acceder a Expedientes e Historias Clínicas',
                    'manage_clinical_encounters' => 'Atender Pacientes y Registrar Consultas Médicas',
                    'clinical_template_view' => 'Ver Diseñador de Plantillas Clínicas',
                    'clinical_template_manage' => 'Crear y Publicar Versiones de Plantillas',
                ],
            ],
            'reviews' => [
                'name' => 'Valoraciones & Sugerencias',
                'description' => 'Encuestas de satisfacción de pacientes y retroalimentación médica.',
                'permissions' => [
                    'view_doctor_reviews' => 'Ver Valoraciones y Reseñas de Pacientes',
                    'manage_doctor_reviews' => 'Modificar Visibilidad Pública y Enviar Invitaciones',
                ],
            ],
            'users' => [
                'name' => 'Módulo de Usuarios del Sistema',
                'description' => 'Almacén centralizado de todos los usuarios (médicos, pacientes, staff).',
                'permissions' => [
                    'view_users' => 'Ver Lista General de Usuarios',
                    'manage_users' => 'Crear, Editar, Activar y Eliminar Usuarios',
                ],
            ],
            'security' => [
                'name' => 'Módulo de Seguridad & Perfiles',
                'description' => 'Configuración de roles y asignación de permisos a módulos.',
                'permissions' => [
                    'view_roles' => 'Ver Matriz de Roles y Permisos',
                    'manage_roles' => 'Modificar Permisos de Módulos y Asignar Roles',
                ],
            ],
            'audit' => [
                'name' => 'Auditoría & Trazabilidad',
                'description' => 'Bitácora y logs de acciones de los usuarios.',
                'permissions' => [
                    'view_audit_logs' => 'Ver Registros de Auditoría y Trazabilidad',
                ],
            ],
            'patient_portal' => [
                'name' => 'Portal del Paciente (Web)',
                'description' => 'Acceso externo para que el paciente gestione sus citas y recetas.',
                'permissions' => [
                    'patient_portal_access' => 'Acceso al Portal Web de Paciente',
                ],
            ],
        ];

        // Search for Quick Role Assignment Tab
        $userSearch = trim($request->query('user_search', ''));
        $usersQuery = User::with(['roles', 'patient', 'doctor']);

        if ($userSearch) {
            $usersQuery->where(function ($q) use ($userSearch) {
                $q->where('name', 'like', "%{$userSearch}%")
                    ->orWhere('email', 'like', "%{$userSearch}%")
                    ->orWhere('phone', 'like', "%{$userSearch}%");
            });
        }

        $users = $usersQuery->paginate(12, ['*'], 'users_page')->withQueryString();

        return view('admin.security', compact('roles', 'modules', 'users', 'userSearch'));
    }

    public function updateRolePermissions(Request $request, Role $role): RedirectResponse
    {
        if ($role->name === 'SUPER_ADMIN') {
            return back()->with('info', 'El rol Super Administrador posee acceso irrestricto total por diseño del sistema.');
        }

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        // Refresh cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $roleLabel = match ($role->name) {
            'ADMIN' => 'Administrador',
            'RECEPTION' => 'Recepcionista',
            'DOCTOR' => 'Médico',
            'SPECIALIST' => 'Especialista',
            'PATIENT' => 'Paciente',
            default => $role->name,
        };

        return back()->with('success', "Permisos de acceso a módulos actualizados exitosamente para el rol '{$roleLabel}'.");
    }

    public function assignUserRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ], [
            'role.required' => 'Debes seleccionar un rol.',
            'role.exists' => 'El rol seleccionado no es válido.',
        ]);

        $newRole = $validated['role'];
        $currentRole = $user->roles->first()?->name;

        // Protection for the last super admin
        if ($currentRole === 'SUPER_ADMIN' && $newRole !== 'SUPER_ADMIN' && User::role('SUPER_ADMIN')->count() <= 1) {
            return back()->with('error', 'No puedes quitar el rol de Super Administrador al único titular del sistema.');
        }

        $user->syncRoles([$newRole]);

        $roleLabel = match ($newRole) {
            'SUPER_ADMIN' => 'Super Administrador',
            'ADMIN' => 'Administrador',
            'RECEPTION' => 'Recepcionista',
            'DOCTOR' => 'Médico',
            'SPECIALIST' => 'Especialista',
            'PATIENT' => 'Paciente',
            default => $newRole,
        };

        return back()->with('success', "Rol '{$roleLabel}' asignado exitosamente al usuario {$user->name}.");
    }
}
