# NEXARIS LINK — Seguridad, Privacidad y Cumplimiento Asistencial
**Cliente:** SurHospital  
**Documento:** CLINICAL_SECURITY.md  
**Especialidad:** Healthcare Security, Privacy, HIPAA/GDPR-aligned Architecture & Access Control  

---

## 1. Matriz de Autorización y Principio de Menor Privilegio

El módulo de Historia Clínica de SurHospital aplica una política estricta de **Principio de Menor Privilegio (Least Privilege)** validada exclusivamente en el servidor (Server-Side Authorization).

| Operación / Recurso | Administrador (`admin`) | Médico (`doctor`) | Recepción (`reception`) | Paciente (`patient`) |
| :--- | :---: | :---: | :---: | :---: |
| **Configurar Plantillas y Versiones** | ✅ Total | 👁️ Solo Lectura/Preview | ❌ Denegado | ❌ Denegado |
| **Publicar Versión de Plantilla** | ✅ Permitido | ❌ Denegado | ❌ Denegado | ❌ Denegado |
| **Iniciar Atención Médica (`Encounter`)**| ❌ (Solo asistencial)| ✅ Médico asignado | ❌ Denegado | ❌ Denegado |
| **Editar Borrador de Atención** | ❌ Denegado | ✅ Autor asignado | ❌ Denegado | ❌ Denegado |
| **Finalizar Atención Médica** | ❌ Denegado | ✅ Autor asignado | ❌ Denegado | ❌ Denegado |
| **Emitir Adenda a Atención Finalizada**| ❌ Denegado | ✅ Médico asistencial | ❌ Denegado | ❌ Denegado |
| **Ver Resumen Longitudinal Paciente** | ✅ Auditoría | ✅ En contexto asistencial| 👁️ Solo Alertas y Citas| 👁️ Solo Datos Propios |
| **Ver Diagnósticos y Exploración** | ✅ Si tiene permiso | ✅ Asistencial | ❌ **BLOQUEADO** | 👁️ Si `is_patient_visible`|
| **Cargar Documentos Clínicos** | ✅ Supervisado | ✅ Asistencial | 👁️ Solo administrativos | 👁️ Carga consentida |
| **Descargar Documentos Clínicos** | ✅ Auditado | ✅ Asistencial | ❌ No confidenciales | 👁️ Solo asignados |

---

## 2. Protección de Datos Clínicos Sensibles (`is_sensitive`)

1. **Aislamiento en Logs**:
   - Los logs técnicos y logs de Laravel (`laravel.log`, Sentry) nunca deben serializar objetos clínicos completos ni volcar `clinical_encounter_values` marcados con `is_sensitive = true`.
   - Los eventos de auditoría registran la acción realizada (e.g. `SensitiveFieldViewed`), el ID de la entidad y el ID del usuario, pero omiten el texto clínico confidencial.
2. **Enmascaramiento en Interfaz de Recepción**:
   - El personal de recepción ve el estado del paciente ("En Espera", "En Consulta", "Atendido") y las alertas generales operativas, pero los campos clínicos y diagnósticos se filtran a nivel de consulta Eloquent (Server-Side Scope).
3. **Filtrado en Portal del Paciente**:
   - Cuando el paciente consulta su historial o resumen de atención, la API / controlador aplica un filtro estricto: únicamente se transmiten los campos con `is_patient_visible = true`. Aunque el paciente inspeccione la respuesta JSON o DOM, la información médica interna confidencial nunca abandona el servidor.

---

## 3. Almacenamiento Seguro de Documentos (`medical_records`)

1. **Disco Privado no Expuesto Directamente**:
   - Los archivos clínicos (estudios de imagen, PDFs de laboratorio, consentimientos) se almacenan en una ruta fuera del `public/` web root (Disco privado `local` o `s3` privado).
2. **Descarga Mediante Stream Autorizado**:
   - No existen URLs directas o predecibles.
   - La descarga se realiza exclusivamente a través de una ruta protegida (`/clinical/documents/{id}/download`) que valida:
     - Autenticación del usuario.
     - Pertenencia del paciente o relación médico-paciente activa.
     - Registro inmediato en `clinical_audit_logs`.

---

## 4. Auditoría Asistencial (`ClinicalAuditLog`)

Cada evento clínico genera un registro inmutable con los siguientes atributos:
- **`user_id` / `user_name`**: Identidad del operador.
- **`role`**: Rol con el que actuó (`doctor`, `admin`, `reception`, `patient`).
- **`event`**: `EncounterStarted`, `DraftSaved`, `EncounterFinalized`, `AddendumCreated`, `TemplatePublished`, `DocumentViewed`, `DocumentDownloaded`.
- **`auditable_type` / `auditable_id`**: Referencia al modelo afectado.
- **`ip_address` & `user_agent`**: Trazabilidad forense de red.
- **`metadata`**: Datos de contexto estructurados (IDs, versionado, diferencias de estado).
