# NEXARIS LINK — Modelo de Datos Clínico y Estrategia de Almacenamiento
**Cliente:** SurHospital  
**Documento:** CLINICAL_DATA_MODEL.md  
**Especialidad:** Database Architecture & High-Integrity Healthcare Data Modeling  

---

## 1. Análisis de Alternativas de Modelado de Datos

El diseño de un sistema de Historia Clínica Electrónica (EHR) configurable presenta tres alternativas clásicas de persistencia:

| Criterio | A) Modelo EAV Puro (Entity-Attribute-Value) | B) Modelo JSONB Monolítico | C) Modelo Híbrido Estructurado + Tipado (Adoptado) |
| :--- | :--- | :--- | :--- |
| **Integridad Referencial** | Muy baja. Todo es texto suelto. | Pobre. Claves y tipos dentro de JSON. | **Máxima**. Foreign keys a niveles críticos. |
| **Indexación y Consultas** | Extremadamente lenta (requiere múltiples JOINs por campo). | Depende de índices GIN/JSONB (Postgres) o JSON index (MySQL). | **Excelente**. Datos clave en columnas indexadas; valores dinámicos tipados con índices compuestos. |
| **Búsqueda de Alergias / Alertas** | Lenta y propensa a fallos de tipado. | Requiere parseo JSON profundo. | **Instantánea** (Tabla `patient_allergies` normalizada). |
| **Versionado e Inmutabilidad** | Complejo de congelar. | Congela el JSON completo pero duplica información. | **Nativo y Elegante**: `template_version_id` + `clinical_encounter_values` inmutables. |
| **Auditoría Legal** | Difícil saber qué cambió. | Difícil rastrear a nivel de campo sin diffs JSON. | **Total**: Detección granular por fila de valor y adenda histórica. |

### Decisión de Arquitectura: Modelo Híbrido Tipado
En SurHospital se adopta el **Modelo Híbrido Tipado**:
1. **Entidades Asistenciales Críticas Normalizadas**: Alergias, Antecedentes, Enfermedades Crónicas, Medicamentos habituales, Diagnósticos (CIE-10) y Documentos se almacenan en tablas relacionales con restricciones de clave foránea e índices B-Tree.
2. **Formularios Dinámicos de Especialidad Tipados**: Las respuestas a los campos configurables se guardan en `clinical_encounter_values` con columnas para los tipos primitivos fundamentales (`value_text`, `value_number`, `value_date`, `value_boolean`, `value_json`), permitiendo consultas numéricas directas (e.g. rangos de presión o glucosa) y exactitud de tipo sin coerción frágil.

---

## 2. Diccionario de Datos y Esquema Relacional

### 2.1 Módulo Longitudinal del Paciente

#### `clinical_records`
Historia clínica principal y única por paciente.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `patient_id`: BIGINT UNSIGNED NOT NULL UNIQUE (FK -> `patients.id` ON DELETE CASCADE)
- `record_number`: VARCHAR(50) NOT NULL UNIQUE (Código único asistencial e.g. `HC-2026-0001`)
- `blood_type`: VARCHAR(10) NULL (e.g. `O+`, `A-`)
- `organ_donor`: BOOLEAN DEFAULT FALSE
- `notes`: TEXT NULL
- `created_at`, `updated_at`: TIMESTAMP

#### `patient_allergies`
Alergias permanentes con capacidad de disparar alertas clínicas inmediatas.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `clinical_record_id`: BIGINT UNSIGNED NOT NULL (FK -> `clinical_records.id`)
- `substance`: VARCHAR(255) NOT NULL (e.g. `Penicilina`, `Látex`, `Mariscos`)
- `allergy_type`: ENUM('MEDICATION', 'FOOD', 'ENVIRONMENTAL', 'OTHER') NOT NULL
- `severity`: ENUM('MILD', 'MODERATE', 'SEVERE', 'LIFE_THREATENING') NOT NULL
- `reaction`: VARCHAR(255) NULL (e.g. `Shock anafiláctico`, `Urticaria`)
- `is_critical`: BOOLEAN NOT NULL DEFAULT FALSE (Genera `CLINICAL ALERT` prominente)
- `diagnosed_at`: DATE NULL
- `notes`: TEXT NULL
- `created_at`, `updated_at`, `deleted_at` (SoftDeletes)

#### `patient_conditions`
Condiciones patológicas crónicas o relevantes.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `clinical_record_id`: BIGINT UNSIGNED NOT NULL (FK -> `clinical_records.id`)
- `condition_name`: VARCHAR(255) NOT NULL (e.g. `Hipertensión Arterial`, `Diabetes Mellitus Tipo 2`)
- `icd10_code`: VARCHAR(20) NULL
- `status`: ENUM('ACTIVE', 'CONTROLLED', 'REMISSION', 'RESOLVED') NOT NULL DEFAULT 'ACTIVE'
- `diagnosed_date`: DATE NULL
- `notes`: TEXT NULL
- `is_clinical_alert`: BOOLEAN NOT NULL DEFAULT FALSE
- `created_at`, `updated_at`, `deleted_at` (SoftDeletes)

#### `patient_medications`
Medicación de uso habitual y continuo.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `clinical_record_id`: BIGINT UNSIGNED NOT NULL (FK -> `clinical_records.id`)
- `drug_name`: VARCHAR(255) NOT NULL (e.g. `Losartán`)
- `concentration`: VARCHAR(100) NULL (e.g. `50 mg`)
- `dose`: VARCHAR(100) NULL (e.g. `1 tableta`)
- `frequency`: VARCHAR(100) NULL (e.g. `Cada 12 horas`)
- `route`: VARCHAR(50) NULL (e.g. `Oral`, `Subcutánea`)
- `status`: ENUM('ACTIVE', 'SUSPENDED', 'DISCONTINUED') NOT NULL DEFAULT 'ACTIVE'
- `start_date`: DATE NULL
- `notes`: TEXT NULL
- `created_at`, `updated_at`, `deleted_at` (SoftDeletes)

#### `patient_backgrounds`
Antecedentes médicos estructurados por categoría.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `clinical_record_id`: BIGINT UNSIGNED NOT NULL (FK -> `clinical_records.id`)
- `category`: ENUM('PERSONAL', 'FAMILY', 'SURGICAL', 'HABIT', 'GYNECO_OBSTETRIC', 'PEDIATRIC') NOT NULL
- `title`: VARCHAR(255) NOT NULL (e.g. `Apendicectomía`, `Padre con Cardiopatía`, `Tabaquismo`)
- `relationship`: VARCHAR(100) NULL (Para familiares: `Padre`, `Madre`, `Hermano/a`)
- `event_year`: VARCHAR(20) NULL (Año o fecha aproximada)
- `institution`: VARCHAR(255) NULL (Para quirúrgicos)
- `details`: TEXT NULL
- `is_relevant`: BOOLEAN NOT NULL DEFAULT FALSE
- `created_at`, `updated_at`, `deleted_at` (SoftDeletes)

---

### 2.2 Motor de Plantillas Configurables

#### `clinical_templates`
Encabezado de la plantilla por especialidad.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `specialty_id`: BIGINT UNSIGNED NULL (FK -> `specialties.id`, NULL para general)
- `name`: VARCHAR(150) NOT NULL
- `slug`: VARCHAR(150) NOT NULL UNIQUE
- `description`: TEXT NULL
- `is_active`: BOOLEAN NOT NULL DEFAULT TRUE
- `created_by`: BIGINT UNSIGNED NOT NULL (FK -> `users.id`)
- `created_at`, `updated_at`, `deleted_at`

#### `clinical_template_versions`
Versiones inmutables de plantillas.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `clinical_template_id`: BIGINT UNSIGNED NOT NULL (FK -> `clinical_templates.id`)
- `version_number`: INT UNSIGNED NOT NULL (1, 2, 3...)
- `status`: ENUM('DRAFT', 'PUBLISHED', 'ARCHIVED') NOT NULL DEFAULT 'DRAFT'
- `changelog`: TEXT NULL
- `published_at`: TIMESTAMP NULL
- `published_by`: BIGINT UNSIGNED NULL (FK -> `users.id`)
- `created_at`, `updated_at`
- UNIQUE KEY `unique_template_version` (`clinical_template_id`, `version_number`)

#### `clinical_template_sections`
Secciones ordenables pertenecientes a una versión de plantilla.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `template_version_id`: BIGINT UNSIGNED NOT NULL (FK -> `clinical_template_versions.id`)
- `name`: VARCHAR(150) NOT NULL
- `description`: VARCHAR(255) NULL
- `order`: INT UNSIGNED NOT NULL DEFAULT 0
- `is_active`: BOOLEAN NOT NULL DEFAULT TRUE
- `is_required`: BOOLEAN NOT NULL DEFAULT FALSE
- `is_repeatable`: BOOLEAN NOT NULL DEFAULT FALSE
- `visibility`: ENUM('ALL', 'DOCTOR_ONLY', 'MEDICAL_STAFF') NOT NULL DEFAULT 'ALL'
- `created_at`, `updated_at`

#### `clinical_template_fields`
Definición de campos dentro de una sección.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `section_id`: BIGINT UNSIGNED NOT NULL (FK -> `clinical_template_sections.id`)
- `name`: VARCHAR(150) NOT NULL
- `code`: VARCHAR(100) NOT NULL (Clave de acceso de la variable e.g. `sys_bp`, `hr`, `chest_pain`)
- `label`: VARCHAR(255) NOT NULL
- `description`: TEXT NULL
- `field_type`: VARCHAR(50) NOT NULL (TEXT, TEXTAREA, INTEGER, DECIMAL, DATE, DATETIME, TIME, BOOLEAN, SELECT, MULTISELECT, RADIO, CHECKBOX, FILE, IMAGE, DIAGNOSIS, MEDICATION, CLINICAL_CATALOG)
- `placeholder`: VARCHAR(255) NULL
- `default_value`: TEXT NULL
- `unit`: VARCHAR(50) NULL (e.g. `mmHg`, `bpm`, `kg`, `°C`)
- `validation_rules`: JSON NULL (e.g. `{"min": 30, "max": 250, "min_length": 2}`)
- `order`: INT UNSIGNED NOT NULL DEFAULT 0
- `is_required`: BOOLEAN NOT NULL DEFAULT FALSE
- `is_repeatable`: BOOLEAN NOT NULL DEFAULT FALSE
- `is_sensitive`: BOOLEAN NOT NULL DEFAULT FALSE
- `is_clinical_alert`: BOOLEAN NOT NULL DEFAULT FALSE
- `is_patient_visible`: BOOLEAN NOT NULL DEFAULT TRUE
- `is_active`: BOOLEAN NOT NULL DEFAULT TRUE
- `help_text`: VARCHAR(255) NULL
- `created_at`, `updated_at`

#### `clinical_field_options`
Opciones para campos tipo SELECT, RADIO, MULTISELECT, CHECKBOX.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `field_id`: BIGINT UNSIGNED NOT NULL (FK -> `clinical_template_fields.id`)
- `label`: VARCHAR(255) NOT NULL
- `value`: VARCHAR(255) NOT NULL
- `order`: INT UNSIGNED NOT NULL DEFAULT 0
- `is_default`: BOOLEAN NOT NULL DEFAULT FALSE
- `created_at`, `updated_at`

---

### 2.3 Encuentro Clínico y Respuestas de Atención

#### `clinical_encounters`
Instancia del acto médico asistencial.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `clinical_record_id`: BIGINT UNSIGNED NOT NULL (FK -> `clinical_records.id`)
- `patient_id`: BIGINT UNSIGNED NOT NULL (FK -> `patients.id`)
- `doctor_id`: BIGINT UNSIGNED NOT NULL (FK -> `doctors.id`)
- `appointment_id`: BIGINT UNSIGNED NULL (FK -> `appointments.id`)
- `specialty_id`: BIGINT UNSIGNED NOT NULL (FK -> `specialties.id`)
- `template_version_id`: BIGINT UNSIGNED NOT NULL (FK -> `clinical_template_versions.id`)
- `status`: ENUM('DRAFT', 'IN_PROGRESS', 'FINALIZED', 'AMENDED', 'CANCELLED') NOT NULL DEFAULT 'DRAFT'
- `consultation_type`: ENUM('INITIAL', 'FOLLOW_UP', 'CONTROL', 'EMERGENCY', 'OTHER') NOT NULL DEFAULT 'INITIAL'
- `reason_for_consultation`: TEXT NULL
- `vital_signs_summary`: JSON NULL (Snapshot rápido para cabeceras y reportes)
- `started_at`: TIMESTAMP NULL
- `finalized_at`: TIMESTAMP NULL
- `finalized_by`: BIGINT UNSIGNED NULL (FK -> `users.id`)
- `created_at`, `updated_at`, `deleted_at`

#### `clinical_encounter_values`
Valores capturados por campo dinámico para el encuentro.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `encounter_id`: BIGINT UNSIGNED NOT NULL (FK -> `clinical_encounters.id` ON DELETE CASCADE)
- `field_id`: BIGINT UNSIGNED NOT NULL (FK -> `clinical_template_fields.id`)
- `section_id`: BIGINT UNSIGNED NOT NULL (FK -> `clinical_template_sections.id`)
- `repeatable_index`: INT UNSIGNED NOT NULL DEFAULT 0 (Para campos/secciones repetibles)
- `value_text`: LONGTEXT NULL
- `value_number`: DECIMAL(12, 4) NULL
- `value_date`: DATE NULL
- `value_datetime`: DATETIME NULL
- `value_boolean`: BOOLEAN NULL
- `value_json`: JSON NULL (Para multiselects, arrays o estructuras complejas)
- `created_at`, `updated_at`
- INDEX `idx_enc_field_idx` (`encounter_id`, `field_id`, `repeatable_index`)

#### `clinical_diagnoses`
Diagnósticos codificados asociados a la atención.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `encounter_id`: BIGINT UNSIGNED NOT NULL (FK -> `clinical_encounters.id`)
- `clinical_record_id`: BIGINT UNSIGNED NOT NULL (FK -> `clinical_records.id`)
- `code`: VARCHAR(20) NOT NULL (e.g. `I10`, `E11.9`)
- `description`: VARCHAR(255) NOT NULL
- `type`: ENUM('PRIMARY', 'SECONDARY', 'PRESUMPTIVE', 'CONFIRMED') NOT NULL DEFAULT 'PRIMARY'
- `notes`: TEXT NULL
- `created_at`, `updated_at`

#### `clinical_addendums`
Adendas médicas posteriores a la finalización inmutable.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `encounter_id`: BIGINT UNSIGNED NOT NULL (FK -> `clinical_encounters.id`)
- `author_id`: BIGINT UNSIGNED NOT NULL (FK -> `users.id`)
- `reason`: VARCHAR(255) NOT NULL (e.g. `Recepción de resultado de laboratorio`, `Aclaración de posología`)
- `content`: LONGTEXT NOT NULL
- `created_at`: TIMESTAMP NOT NULL

#### `clinical_documents`
Documentos y archivos adjuntos clínicos.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `clinical_record_id`: BIGINT UNSIGNED NOT NULL (FK -> `clinical_records.id`)
- `encounter_id`: BIGINT UNSIGNED NULL (FK -> `clinical_encounters.id`)
- `document_type`: ENUM('LAB_REPORT', 'IMAGING', 'SURGICAL_REPORT', 'CONSENT_FORM', 'EXTERNAL_RECORD', 'OTHER') NOT NULL
- `file_path`: VARCHAR(500) NOT NULL (En disco privado `medical_records`)
- `file_name`: VARCHAR(255) NOT NULL
- `file_size`: BIGINT UNSIGNED NOT NULL
- `mime_type`: VARCHAR(100) NOT NULL
- `description`: TEXT NULL
- `is_sensitive`: BOOLEAN NOT NULL DEFAULT FALSE
- `uploaded_by`: BIGINT UNSIGNED NOT NULL (FK -> `users.id`)
- `created_at`, `updated_at`, `deleted_at`

#### `clinical_audit_logs`
Trazabilidad de alta fidelidad para el módulo clínico.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `user_id`: BIGINT UNSIGNED NULL (FK -> `users.id`)
- `user_name`: VARCHAR(150) NULL
- `role`: VARCHAR(50) NULL
- `event`: VARCHAR(100) NOT NULL (e.g. `EncounterCreated`, `EncounterFinalized`, `AddendumCreated`, `TemplatePublished`, `SensitiveFieldViewed`)
- `auditable_type`: VARCHAR(150) NOT NULL
- `auditable_id`: BIGINT UNSIGNED NOT NULL
- `ip_address`: VARCHAR(45) NULL
- `user_agent`: VARCHAR(255) NULL
- `metadata`: JSON NULL (Sin volcar contenido clínico sensible en texto plano)
- `created_at`: TIMESTAMP NOT NULL
