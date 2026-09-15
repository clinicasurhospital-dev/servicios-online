# NEXARIS LINK — RELATIONAL DATABASE & POSTGRESQL DESIGN

**Motor de Base de Datos:** PostgreSQL 15+ / 16  
**Convenciones:** `snake_case` en tablas y columnas, UUID / BigIncrements, Foreign Keys explícitas, Índices compuestos, Enums respaldados por tipos nativos o strings consistentes.

---

## 1. Esquema Entidad-Relación y Tablas

### 1.1. Seguridad y Usuarios (`users`, `roles`, `permissions`)
- `users`: `id`, `name`, `email` (unique), `phone`, `password`, `is_active`, `two_factor_secret`, `two_factor_confirmed_at`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`.
- `model_has_roles`, `role_has_permissions` (vía Spatie Laravel Permission).

### 1.2. Dominio Clínico (`patients`, `doctors`, `specialties`, `procedures`)
- `patients`: `id`, `user_id` (nullable, FK users), `identification_number` (cédula/DNI, unique), `identification_type`, `first_name`, `last_name`, `gender`, `birth_date`, `address`, `emergency_contact_name`, `emergency_contact_phone`, `blood_type`, `created_at`, `updated_at`.
- `specialties`: `id`, `name`, `slug` (unique), `description`, `icon`, `image_url`, `base_price` (decimal 10,2), `default_duration_minutes` (integer default 30), `is_active` (boolean default true), `created_at`, `updated_at`.
- `doctors`: `id`, `user_id` (FK users), `license_number` (código médico único), `bio`, `photo_url`, `is_active` (boolean default true), `created_at`, `updated_at`.
- `doctor_specialty`: `id`, `doctor_id` (FK doctors), `specialty_id` (FK specialties), `custom_price` (nullable decimal), `custom_duration_minutes` (nullable integer).
- `procedures`: `id`, `specialty_id` (FK specialties), `name`, `slug` (unique), `description`, `price` (decimal 10,2), `duration_minutes` (integer default 30), `is_active` (boolean default true), `created_at`, `updated_at`.
- `doctor_procedure`: `id`, `doctor_id` (FK doctors), `procedure_id` (FK procedures), `custom_price`, `custom_duration_minutes`.

### 1.3. Agendamiento y Disponibilidad (`schedules`, `blocks`, `absences`)
- `doctor_schedules`: `id`, `doctor_id` (FK doctors), `day_of_week` (0=Sunday to 6=Saturday), `start_time` (time), `end_time` (time), `slot_duration_minutes` (integer default 30), `is_active` (boolean default true), `created_at`, `updated_at`.
- `doctor_schedule_blocks`: `id`, `doctor_id` (FK doctors), `start_datetime` (timestamp), `end_datetime` (timestamp), `reason`, `created_by` (FK users), `created_at`, `updated_at`.
- `doctor_absences`: `id`, `doctor_id` (FK doctors), `type` (enum: `VACATION`, `MEDICAL_LEAVE`, `CONGRESS`, `OTHER`), `start_date` (date), `end_date` (date), `notes`, `is_approved` (boolean default true), `approved_by` (FK users), `created_at`, `updated_at`.
- `system_holidays`: `id`, `date` (date unique), `name`, `is_recurring` (boolean default false), `created_at`, `updated_at`.

### 1.4. Citas y Ciclo de Vida (`appointments`, `histories`)
- `appointments`:
  - `id` (bigint / UUID)
  - `code` (string unique e.g. `SUR-2026-XXXX`)
  - `patient_id` (FK patients)
  - `doctor_id` (FK doctors)
  - `specialty_id` (FK specialties)
  - `procedure_id` (nullable FK procedures)
  - `start_at` (timestamp with time zone)
  - `end_at` (timestamp with time zone)
  - `status` (enum: `PENDING`, `PENDING_PAYMENT`, `CONFIRMED`, `RESCHEDULED`, `CANCELLED`, `COMPLETED`, `NO_SHOW`)
  - `price` (decimal 10,2)
  - `patient_notes` (text nullable)
  - `clinical_notes` (text nullable)
  - `cancelled_reason` (text nullable)
  - `cancelled_by` (nullable FK users)
  - `cancelled_at` (timestamp nullable)
  - `created_by` (nullable FK users)
  - `created_at`, `updated_at`.
- `appointment_status_histories`:
  - `id`
  - `appointment_id` (FK appointments)
  - `previous_status` (string nullable)
  - `new_status` (string)
  - `changed_by_user_id` (nullable FK users)
  - `reason` (string nullable)
  - `metadata` (jsonb nullable)
  - `created_at`.

### 1.5. Pagos y Transferencias (`payments`, `bank_transfers`)
- `payments`:
  - `id`
  - `appointment_id` (FK appointments)
  - `payment_method` (enum: `ONLINE`, `BANK_TRANSFER`, `CASH`, `CARD_ONSITE`)
  - `gateway_name` (string nullable)
  - `gateway_transaction_id` (string nullable)
  - `amount` (decimal 10,2)
  - `currency` (string default 'USD')
  - `status` (enum: `PENDING`, `PAID`, `REJECTED`, `REFUNDED`)
  - `paid_at` (timestamp nullable)
  - `payload_response` (jsonb nullable)
  - `created_at`, `updated_at`.
- `bank_transfers`:
  - `id`
  - `payment_id` (FK payments)
  - `bank_name` (string)
  - `reference_number` (string)
  - `receipt_path` (string, stored in private storage)
  - `receipt_original_name` (string)
  - `amount` (decimal 10,2)
  - `transfer_date` (date)
  - `notes` (text nullable)
  - `validation_status` (enum: `PENDING_VALIDATION`, `APPROVED`, `REJECTED`)
  - `validated_by_user_id` (nullable FK users)
  - `validated_at` (timestamp nullable)
  - `rejection_reason` (text nullable)
  - `created_at`, `updated_at`.

### 1.6. Auditoría (`audit_logs`)
- `audit_logs`:
  - `id`
  - `user_id` (nullable FK users)
  - `action` (string: `AppointmentCreated`, `AppointmentRescheduled`, `AppointmentCancelled`, etc.)
  - `auditable_type` (string)
  - `auditable_id` (string/bigint)
  - `before_state` (jsonb nullable)
  - `after_state` (jsonb nullable)
  - `ip_address` (string nullable)
  - `user_agent` (text nullable)
  - `created_at`.

---

## 2. Restricciones e Índices de Rendimiento

1. **Anti-Collision Index:**
   ```sql
   CREATE INDEX idx_appointments_doctor_active_time ON appointments (doctor_id, start_at, end_at) 
   WHERE status NOT IN ('CANCELLED', 'RESCHEDULED');
   ```
2. **Patient Lookup Index:**
   ```sql
   CREATE INDEX idx_appointments_patient ON appointments (patient_id, start_at DESC);
   ```
3. **Availability Range Index:**
   ```sql
   CREATE INDEX idx_doctor_schedules_lookup ON doctor_schedules (doctor_id, day_of_week, is_active);
   CREATE INDEX idx_doctor_blocks_range ON doctor_schedule_blocks (doctor_id, start_datetime, end_datetime);
   CREATE INDEX idx_doctor_absences_range ON doctor_absences (doctor_id, start_date, end_date) WHERE is_approved = true;
   ```
