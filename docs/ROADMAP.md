# NEXARIS LINK — IMPLEMENTATION ROADMAP & PHASES

**Cliente:** SurHospital  
**Arquitectura:** Monolito Modular en Laravel 11/12 (Blade, Livewire, Alpine.js, Tailwind CSS, PostgreSQL)  

---

## Cronograma y Fases de Desarrollo

### FASE 1: Scaffolding, Base de Datos, Branding & Autenticación
- [x] Scaffolding del proyecto Laravel con configuración de entorno y PostgreSQL.
- [x] Configuración de Tailwind CSS, Vite, Alpine.js y Livewire.
- [x] Implementación de `BrandingService` y `config/branding.php` con tokens `#0700FF`.
- [x] Sistema de Autenticación con soporte para Roles (Spatie), Login, Registro de Pacientes y Recuperación.
- [x] Design System base (Blade components: Button, Input, Select, Modal, Drawer, Toast, Badge, Card, StatCard).

### FASE 2: Gestión de Entidades Clínicas (Users, Patients, Specialties, Procedures)
- [ ] Modelos, migraciones, factories y seeders para Especialidades y Procedimientos.
- [ ] CRUD de Especialidades con precios, duraciones e imágenes.
- [ ] CRUD de Procedimientos vinculados a Especialidades.
- [ ] Módulo de Pacientes con validación de identidad y perfil clínico básico.
- [ ] Asignación de Roles y Permisos (`SUPER_ADMIN`, `ADMIN`, `RECEPTION`, `DOCTOR`, `PATIENT`).

### FASE 3: Gestión de Médicos, Horarios & Availability Engine
- [ ] Modelos y relaciones de Médicos con Especialidades y Procedimientos.
- [ ] Gestión de Horarios de Trabajo (`DoctorSchedule`) por día de la semana.
- [ ] Gestión de Bloqueos de Agenda y Ausencias/Vacaciones (`DoctorAbsence`).
- [ ] Implementación del motor de disponibilidad central (`AvailabilityService`):
  - Cálculo dinámico de slots.
  - Exclusión automática de citas, bloqueos, feriados y ausencias.
  - Control estricto de intervalos según procedimiento o consulta.

### FASE 4: Ciclo de Vida de Citas, Agendamiento & Concurrencia
- [ ] Modelos de Citas (`Appointment`) y estados tipados (`AppointmentStatus` Enum).
- [ ] Protección contra doble reserva y concurrencia con transacciones PostgreSQL y locks pesimistas.
- [ ] Wizard interactivo de Agendamiento en Livewire (Especialidad → Procedimiento → Médico → Fecha → Slot → Paciente → Pago).
- [ ] Módulo de Creación Manual de Citas para Recepción.
- [ ] Módulo de Reprogramación con validación de política horaria (`minimum_reschedule_hours`) y liberación de slot.
- [ ] Módulo de Cancelación con registro de motivo y usuario.
- [ ] Historial inmutable de estados (`AppointmentStatusHistory`).

### FASE 5: Pasarela de Pagos & Transferencias Bancarias
- [ ] Interfaz abstracta `PaymentGatewayInterface` y DTOs de pago.
- [ ] Implementación de Pago Online simulado con webhooks idempotentes.
- [ ] Flujo de Transferencia Bancaria con carga de comprobante a Storage privado.
- [ ] Panel de Recepción para Validación / Rechazo de Transferencias.
- [ ] Disparo de Notificaciones por Email (Cita creada, Cita confirmada, Pago aprobado, Reprogramada, Cancelada).

### FASE 6: Backoffice Administrativo, Agenda & Métricas
- [ ] Layout Administrativo responsivo con Sidebar colapsable y Header con notificaciones.
- [ ] Vista de Agenda diaria y semanal para Recepción y Médicos.
- [ ] Dashboard Gerencial con KPIs reales (Citas hoy, Confirmadas, Pendientes, No-Show, Ocupación, Ingresos, Citas por médico/especialidad).
- [ ] Módulo de Auditoría del Sistema (`audit_logs`) con registro de acciones y payloads.

### FASE 7: Sitio Web Público SurHospital & Portal del Paciente
- [ ] Refactorización profesional del sitio web de SurHospital:
  - Inicio con Hero dinámico y CTA directo a agendar.
  - Sección Institucional "SurHospital".
  - Catálogo de Especialidades y Procedimientos.
  - Directorio de Médicos y Perfiles Profesionales.
  - Formulario de Contacto y Ubicación.
- [ ] Portal del Paciente simplificado:
  - Saludo personalizado y Próxima Cita en tarjeta destacada.
  - Historial de citas y comprobantes de pago.
  - Reprogramación y Cancelación en un clic según reglas.

### FASE 8: Pruebas Automatizadas, Seguridad & Calidad
- [ ] Suites de pruebas automatizadas:
  - `AuthenticationTest`
  - `AuthorizationTest`
  - `AvailabilityTest`
  - `AppointmentTest`
  - `ConcurrentBookingTest`
  - `ReschedulingTest`
  - `CancellationTest`
  - `PaymentTest`
  - `PatientAccessTest`
  - `DoctorAccessTest`
- [ ] Verificación de seguridad, análisis de código, formateo y validación de build de producción.
