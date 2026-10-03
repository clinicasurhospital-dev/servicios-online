# NEXARIS LINK — Roadmap de Implementación del Módulo de Historia Clínica
**Cliente:** SurHospital  
**Documento:** CLINICAL_ROADMAP.md  
**Especialidad:** Technical Project Management & Quality Engineering  

---

## 1. Fases de Ejecución y Plan de Trabajo

| Fase | Alcance y Entregables | Estado |
| :---: | :--- | :---: |
| **FASE 1** | **Auditoría, Diseño Arquitectónico y Documentación**<br>• Inspección de código, base de datos y permisos existentes.<br>• Creación de los 6 documentos de arquitectura en `docs/clinical-history/`.<br>• Definición de modelos, enums, contratos y DTOs. | ✅ **COMPLETADO** |
| **FASE 2** | **Persistencia Longitudinal & Migraciones Core**<br>• Migraciones para `clinical_records`, `patient_allergies`, `patient_conditions`, `patient_medications`, `patient_backgrounds`.<br>• Modelos Eloquent con relaciones, scopes y soft-deletes.<br>• Detección y activación automática de `is_clinical_alert`. | 🚀 **EN CURSO** |
| **FASE 3** | **Motor de Plantillas, Secciones y Campos Dinámicos**<br>• Migraciones de `clinical_templates`, `clinical_template_versions`, `clinical_template_sections`, `clinical_template_fields`, `clinical_field_options`.<br>• Soporte para 16 tipos de campo y campos repetibles.<br>• Motor de validación backend (`min`, `max`, `regex`, `required`). | ⏳ **PENDIENTE** |
| **FASE 4** | **Versionado Inmutable, Publicación y Previsualización**<br>• Transiciones de estado de plantillas (`DRAFT`, `PUBLISHED`, `ARCHIVED`).<br>• Clonación automática de versión al editar plantillas publicadas.<br>• Motor de Previsualización para médicos y administradores. | ⏳ **PENDIENTE** |
| **FASE 5** | **Encuentro Clínico (`ClinicalEncounter`) & Flujo Asistencial**<br>• Integración con Citas (`appointments`) y Médicos (`doctors`).<br>• Ciclo de vida: `DRAFT` -> `IN_PROGRESS` -> `FINALIZED` -> `AMENDED`.<br>• Almacenamiento tipado en `clinical_encounter_values`.<br>• Bloqueo estricto tras finalización y validación server-side. | ⏳ **PENDIENTE** |
| **FASE 6** | **Diagnósticos Estructurados, Adendas y Documentos**<br>• Diagnósticos CIE-10 (Principal, Secundario, Presuntivo, Definitivo).<br>• Adendas clínicas firmadas (`ClinicalAddendum`).<br>• Almacenamiento seguro en disco privado `medical_records`. | ⏳ **PENDIENTE** |
| **FASE 7** | **Interfaz de Historia Clínica del Paciente (UI/UX)**<br>• Cabecera de Alertas Clínicas permanentes.<br>• Pestañas: Resumen, Antecedentes, Consultas (Timeline), Diagnósticos, Documentos.<br>• Filtrado por rol: Enmascaramiento para Recepción y Pacientes. | ⏳ **PENDIENTE** |
| **FASE 8** | **Constructor de Plantillas UI (Template Builder)**<br>• Interfaz de 3 columnas (Secciones, Campos, Inspector de Propiedades).<br>• Reordenamiento Drag & Drop de secciones y campos.<br>• Modal de Vista Previa interactiva y flujo de publicación. | ⏳ **PENDIENTE** |
| **FASE 9** | **Permisos Spatie, Auditoría Asistencial y Seguridad**<br>• Permisos `clinical-record.*`, `clinical-template.*`, `clinical-encounter.*`.<br>• Trazabilidad forense en `clinical_audit_logs`.<br>• Protección de datos sensibles (`is_sensitive`) y anonimización en logs. | ⏳ **PENDIENTE** |
| **FASE 10** | **Suite de Pruebas Automatizadas, QA y Verificación Final**<br>• Pruebas unitarias y de integración exhaustivas.<br>• Verificación de los 6 casos de prueba críticos.<br>• Validación de performance y definición de terminado (DoD). | ⏳ **PENDIENTE** |
