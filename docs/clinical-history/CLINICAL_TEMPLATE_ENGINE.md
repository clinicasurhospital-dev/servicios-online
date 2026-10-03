# NEXARIS LINK — Motor de Plantillas Clínicas (Clinical Template Engine)
**Cliente:** SurHospital  
**Documento:** CLINICAL_TEMPLATE_ENGINE.md  
**Componente:** Template Schema, Form Engine & Dynamic Renderer  

---

## 1. Arquitectura del Motor de Plantillas

El motor de plantillas de **Nexaris Link** permite a los directores médicos y administradores de SurHospital crear, versionar y publicar formularios clínicos altamente especializados sin modificar una sola línea de código.

```
                          +-------------------------------+
                          |       CLINICAL TEMPLATE       |
                          | (e.g. Cardiología Ambulatoria)|
                          +---------------+---------------+
                                          | 1:N
                                          v
                          +-------------------------------+
                          |   CLINICAL TEMPLATE VERSION   |
                          | (v1 - PUBLISHED [Inmutable])  |
                          | (v2 - DRAFT     [En Edición]) |
                          +---------------+---------------+
                                          | 1:N
                                          v
                          +-------------------------------+
                          |   CLINICAL TEMPLATE SECTION   |
                          | (1. Motivo, 2. Signos, etc.)  |
                          +---------------+---------------+
                                          | 1:N
                                          v
                          +-------------------------------+
                          |   CLINICAL TEMPLATE FIELD     |
                          | (code: 'sys_bp', type:DECIMAL)|
                          +---------------+---------------+
                                          | 1:N (si aplica)
                                          v
                          +-------------------------------+
                          |    CLINICAL FIELD OPTION      |
                          | (e.g. 'Normal', 'Elevada')    |
                          +-------------------------------+
```

---

## 2. Catálogo de Tipos de Campo Soportados

El motor implementa soporte nativo para 16 tipos de campo:

| Tipo (`field_type`) | Descripción | Componente UI / Input | Almacenamiento Primario |
| :--- | :--- | :--- | :--- |
| `TEXT` | Texto corto de una sola línea | `<input type="text">` | `value_text` |
| `TEXTAREA` | Texto enriquecido o multilínea | `<textarea>` | `value_text` |
| `INTEGER` | Número entero (e.g. semanas gestacionales, pulsaciones) | `<input type="number" step="1">` | `value_number` |
| `DECIMAL` | Número con decimales (e.g. peso, temperatura, IMC) | `<input type="number" step="0.01">` | `value_number` |
| `DATE` | Fecha (e.g. FUM, fecha de inicio síntoma) | `<input type="date">` | `value_date` |
| `DATETIME` | Fecha y hora exacta | `<input type="datetime-local">` | `value_datetime` |
| `TIME` | Hora | `<input type="time">` | `value_text` |
| `BOOLEAN` | Interruptor binario Sí/No | Toggle Switch / Switch button | `value_boolean` |
| `SELECT` | Menú desplegable de opción única | `<select>` con opciones dinámicas | `value_text` |
| `MULTISELECT` | Selección múltiple de catálogo | Componente Pills / Multiselect dropdown | `value_json` |
| `RADIO` | Botones de radio mutuamente excluyentes | Radio group | `value_text` |
| `CHECKBOX` | Casilla de verificación múltiple | Checkbox group | `value_json` |
| `FILE` | Carga de archivo adjunto seguro (PDF, DOC) | Dropzone con subida async | `value_text` (file_path) |
| `IMAGE` | Carga de imagen clínica o fotografía dermatológica | Visualizador/Crop dropzone | `value_text` (file_path) |
| `DIAGNOSIS` | Selector rápido CIE-10 con tipo (Principal, Secundario) | Autocomplete con búsqueda rápida | `value_json` |
| `MEDICATION` | Prescriptor estructurado (Fármaco, Dosis, Frecuencia) | Fila estructurada de prescripción | `value_json` |
| `CLINICAL_CATALOG` | Catálogo parametrizado (e.g. procedimientos, laboratorios)| Autocomplete dinámico | `value_text` / `value_json` |

---

## 3. Motor de Validación en Backend

Cada campo configurable define reglas en formato JSON almacenadas en `validation_rules`. El servicio `ClinicalTemplateValidationService` ejecuta las reglas de forma estricta en el servidor antes de guardar un borrador o finalizar la atención:

```json
{
  "min": 0,
  "max": 300,
  "min_length": 3,
  "max_length": 500,
  "regex": "^[A-Z0-9_-]+$",
  "allowed_mimes": ["pdf", "jpg", "png"],
  "max_file_size_kb": 10240
}
```

### Reglas de Validación Automáticas:
1. **Obligatoriedad (`is_required`)**: Si un campo está marcado como requerido y no está presente en la finalización, la transacción es rechazada con un error descriptivo por sección y campo.
2. **Límites Numéricos (`min`, `max`)**: Aplicados automáticamente para campos `INTEGER` y `DECIMAL` (e.g. Temperatura entre 30°C y 45°C).
3. **Límites de Texto (`min_length`, `max_length`)**: Aplicados para `TEXT` y `TEXTAREA`.
4. **Validación de Opciones**: Para `SELECT`, `RADIO` y `MULTISELECT`, se verifica que el valor enviado pertenezca estrictamente a las opciones activas registradas en `clinical_field_options`.

---

## 4. Secciones y Campos Repetibles

El motor permite configurar tanto **secciones repetibles** como **campos repetibles** mediante el identificador `repeatable_index`:

- **Ejemplo de Sección Repetible**: *Cirugías Previas*
  - Cirugía #1: Procedimiento, Año, Clínica.
  - Cirugía #2: Procedimiento, Año, Clínica.
- **Ejemplo de Campo Repetible**: *Alergias Adicionales* o *Medicamentos Actuales*.
  - En la base de datos se almacena una fila en `clinical_encounter_values` por cada `repeatable_index` (0, 1, 2...), garantizando una estructura limpia y sin límites artificiales.

---

## 5. Ciclo de Vida y Versionado Inmutable

```
   [CREAR PLANTILLA]
           │
           ▼
    +--------------+      Edición de secciones, campos, orden y opciones.
    |    DRAFT     | <──  Permite previsualización dinámica.
    +------+-------+
           │
           │ Publicar (Sella la versión v1 como inmutable)
           ▼
    +--------------+
    |  PUBLISHED   | ───► Utilizada activamente por los médicos en consultas.
    +------+-------+      NO admite modificaciones estructurales directas.
           │
           │ Al solicitar edición:
           │ 1. Duplica v1 -> v2 (DRAFT)
           │ 2. v1 permanece activa para atenciones en curso
           ▼
    +--------------+
    |  NUEVA v(N)  |
    +--------------+
```

### Garantía de Integridad:
- Si una consulta médica se inició con `Cardiología v1`, todos sus `clinical_encounter_values` referencian los IDs de campos de la versión `v1`.
- Cuando se publica `Cardiología v2`, las consultas anteriores no sufren desalineaciones ni pérdida de datos históricos.
