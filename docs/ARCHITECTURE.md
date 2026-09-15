# NEXARIS LINK — SOFTWARE ARCHITECTURE SPECIFICATION

**Plataforma:** Nexaris Link  
**Cliente Inicial:** SurHospital  
**Arquitectura:** Monolito Modular Limpio (Modular Monolith) en Laravel 11/12  
**Estilo:** Domain-Driven Design (DDD) Pragmatic Layers  

---

## 1. Principios de Diseño

1. **Monolito Modular:** Alta cohesión interna por dominio, bajo acoplamiento entre módulos.
2. **Separación de Responsabilidades:** Controllers delgados (Thin Controllers) dedicados únicamente a coordinar HTTP Request/Response; lógica de negocio encapsulada en **Domain Services**, **Actions** y **DTOs**.
3. **Control de Concurrencia Estricto:** Protección contra dobles reservas de citas médicas mediante transacciones ACID en PostgreSQL, bloqueos pesimistas (`lockForUpdate`), índices únicos condicionales y serialización de operaciones críticas.
4. **Desacoplamiento de Marca (White-Label):** Cero *hardcoding* de la marca del cliente (`SurHospital`, logos, paletas, teléfonos, textos). Toda la identidad se gestiona vía `BrandingService` y `config/branding.php`.
5. **Seguridad Integral:** Autenticación robusta, autorización mediante `Policies` de Laravel y Spatie Roles, Form Requests validados en servidor, almacenamiento seguro y aislado en disco privado para comprobantes bancarios, auditoría inmutable de eventos.

---

## 2. Mapa de Módulos y Dominios

```
app/
├── Domain/
│   ├── Authentication/         # Login, Registro, 2FA, Recuperación de Password
│   ├── Users/                  # Gestión de Usuarios, Roles (Spatie), Permisos
│   ├── Patients/               # Perfil Paciente, Historial Médico de Contacto
│   ├── Doctors/                # Perfil Médico, Especialidades, Configuración de Atención
│   ├── Specialties/            # Especialidades Médicas, Duraciones, Precios Base
│   ├── Procedures/             # Procedimientos/Servicios Clínicos específicos
│   ├── Scheduling/             # Horarios de Trabajo, Bloqueos, Ausencias, Feriados
│   ├── Availability/           # Motor de Cálculo de Disponibilidad (AvailabilityService)
│   ├── Appointments/           # Ciclo de Vida de Citas, Concurrencia, Reprogramación, Cancelación
│   ├── Payments/               # Gateway Abstraction, Transferencias Bancarias, Webhooks Idempotentes
│   ├── Notifications/          # Emails transaccionales, Canales extensibles (WhatsApp/SMS)
│   ├── Metrics/                # Agregaciones, Métricas Operativas y Financieras
│   ├── Audit/                  # Registro inmutable de eventos y cambios de estado
│   └── Branding/               # Inyección de identidad dinámica White-Label
```

---

## 3. Patrones y Componentes Arquitectónicos

### 3.1. Availability Engine (`AvailabilityService`)
Responsable de proyectar la disponibilidad exacta de citas sin pre-generar millones de slots vacíos en disco.
- **Entradas:** Médico, Especialidad/Procedimiento (determina duración), Rango de Fechas.
- **Reglas Evaluadas:**
  1. Horarios de atención activos del médico para el día de semana.
  2. Ventana de atención configurada (inicio, fin, pausas).
  3. Feriados institucionales / nacionales (desde configuración).
  4. Ausencias y vacaciones aprobadas del médico (`DoctorAbsence`).
  5. Bloqueos manuales de agenda (`DoctorScheduleBlock`).
  6. Citas activas ya reservadas (`PENDING`, `PENDING_PAYMENT`, `CONFIRMED`).
- **Salida:** Lista estructurada de slots disponibles en formato ISO 8601 con identificador de slot.

### 3.2. Concurrency & Anti-Double-Booking Protection
Para evitar que dos pacientes reserven el mismo médico y horario simultáneamente:
```php
DB::transaction(function () use ($dto) {
    // 1. Lock pesimista en slot/médico o registro de bloqueo temporal
    $doctor = Doctor::where('id', $dto->doctorId)->lockForUpdate()->first();
    
    // 2. Validación de disponibilidad en tiempo real bajo lock
    $isAvailable = $this->availabilityService->isSlotAvailable(
        $dto->doctorId,
        $dto->startAt,
        $dto->endAt
    );
    
    if (!$isAvailable) {
        throw new SlotAlreadyBookedException("El horario seleccionado ya no se encuentra disponible.");
    }
    
    // 3. Creación de cita dentro de la transacción
    $appointment = Appointment::create([...]);
    
    // 4. Registro de historial de estado y auditoría
    $this->recordStatusHistory($appointment, AppointmentStatus::PENDING);
});
```

### 3.3. Pasarela de Pagos (`PaymentGatewayInterface`)
Arquitectura orientada a interfaces:
```php
interface PaymentGatewayInterface {
    public function createPayment(PaymentCreationDTO $dto): PaymentResultDTO;
    public function getStatus(string $gatewayTransactionId): PaymentStatusResultDTO;
    public function handleWebhook(Request $request): WebhookResultDTO;
    public function refund(string $gatewayTransactionId, float $amount): RefundResultDTO;
}
```
Implementaciones iniciales:
- `BankTransferPaymentGateway`: Manejo de transferencias directas con subida de comprobante, validación manual por recepción y almacenamiento privado.
- `MockOnlinePaymentGateway`: Pasarela online simulada para pruebas locales y staging con webhook idempotente.

---

## 4. Matriz de Roles y Permisos

| Rol | Alcance de Acceso |
| :--- | :--- |
| `SUPER_ADMIN` | Acceso irrestricto, configuración del sistema, gestión de sedes y auditoría total. |
| `ADMIN` | Gestión operativa, médicos, especialidades, procedimientos, reportes y métricas. |
| `RECEPTION` | Gestión de pacientes, creación manual de citas, agenda diaria, validación de transferencias. |
| `DOCTOR` | Acceso estricto a su propia agenda, citas asignadas y configuración de ausencias. |
| `PATIENT` | Portal de paciente: sus citas, pagos, perfil y flujo de reserva. Aislamiento total de otros pacientes. |

---

## 5. Estrategia de Auditoría

Cada cambio de estado en entidades críticas dispara un evento que genera un registro en `audit_logs`:
- **Entidades auditadas:** `Appointment`, `Payment`, `DoctorSchedule`, `UserRole`, `DoctorAbsence`.
- **Datos almacenados:** `user_id`, `action`, `entity_type`, `entity_id`, `before_payload` (JSON), `after_payload` (JSON), `ip_address`, `user_agent`, `created_at`.
