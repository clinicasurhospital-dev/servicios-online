# NEXARIS LINK — Guía de Diseño, UI y Experiencia de Usuario (UX) Clínica
**Cliente:** SurHospital  
**Documento:** CLINICAL_UI_UX.md  
**Especialidad:** Healthcare SaaS UX, Clinical Usability & Modern UI Engineering  

---

## 1. Fundamentos Visuales y Marca SurHospital

El módulo de Historia Clínica adopta y enriquece el Design System de **Nexaris Link** adaptado para la identidad visual de **SurHospital**:

- **Color Primario (Nexaris Brand)**: `#0700FF` (Azul eléctrico quirúrgico de alta legibilidad).
- **Colores de Acento y Semántica Médica**:
  - Alerta Clínica / Riesgo Alto: `#EF4444` (Rojo carmesí / Alert banner).
  - Estado Favorable / Resuelto: `#10B981` (Verde esmeralda clínico).
  - Precaución / Advertencia: `#F59E0B` (Ámbar dorado).
  - Información / Signos Vitales: `#3B82F6` / `#6366F1` (Azul zafiro e índigo).
- **Tipografía**: Fuentes geométricas modernas (`Inter`, `Plus Jakarta Sans`, sans-serif) con jerarquía clara de títulos, etiquetas de campo y valores clínicos.
- **Micro-interacciones y Accesibilidad**: Contrastes calculados (cumplimiento WCAG 2.1 AA), transiciones suaves de 150ms-200ms y estados hover/focus nítidos para evitar errores de digitación en entornos clínicos de ritmo acelerado.

---

## 2. Experiencia de la Ficha Clínica del Paciente

La vista integral del paciente incorpora una cabecera con **Alertas Clínicas Permanentes** y navegación por pestañas optimizadas:

```
+---------------------------------------------------------------------------------------+
|  [FOTO]  MARÍA LÓPEZ VALENCIA — 34 años (HC-2026-0042)                [Tipo Sangre: O+]|
|  -----------------------------------------------------------------------------------  |
|  ⚠️ ALERTAS CLÍNICAS: Alergia a Penicilina (Severa) | Embarazo 24 sem | Hipertensión  |
+---------------------------------------------------------------------------------------+
|  [ Resumen ]  [ Antecedentes ]  [ Consultas / Timeline ]  [ Diagnósticos ]  [ Docs ]  |
+---------------------------------------------------------------------------------------+
```

### Pestañas Principales:
1. **Resumen**: Panel unificado con alertas críticas, condiciones activas, medicamentos continuos, fecha de última atención y próxima cita programada.
2. **Antecedentes**: Vista organizada por tarjetas colapsables: Patológicos personales, Familiares con grado de parentesco, Quirúrgicos con institución y fecha, y Hábitos.
3. **Consultas (Timeline Cronológico)**: Línea de tiempo visual con badge de especialidad, médico tratante, diagnósticos emitidos, estado de la atención y botón de visualización rápida o adenda.
4. **Diagnósticos**: Registro histórico acumulado clasificado por Primarios y Secundarios con código CIE-10.
5. **Documentos**: Galería estructurada de estudios de laboratorio, ecografías, informes en PDF con preview y descarga segura.

---

## 3. Experiencia de la Consulta Médica (`ClinicalEncounter`)

Para evitar la fatiga cognitiva de formularios interminables, la atención médica se estructura en un **Stepper de Secciones con Progreso Visual**:

```
+---------------------------------------------------------------------------------------+
| ATENCIÓN MÉDICA: MARÍA LÓPEZ          Especialidad: Cardiología       Dr. Juan Pérez  |
+---------------------------------------------------------------------------------------+
|  1. Motivo  |  2. Antecedentes  |  3. Signos  |  4. Examen  |  5. Diagnóstico  |  6. Plan |
+---------------------------------------------------------------------------------------+
|                                                                                       |
|  [ SECCIÓN ACTIVA: 3. Signos Vitales ]                                                |
|                                                                                       |
|  Peso (kg)      Talla (cm)       IMC (Auto)       Presión (Sys/Dia)     FC (bpm)      |
|  [ 70.5  ]      [ 168    ]       [ 25.0 - Normal] [ 120 / 80      ]     [ 72   ]      |
|                                                                                       |
|  [< Anterior]                              [Guardar Borrador]    [Siguiente / Finalizar>]|
+---------------------------------------------------------------------------------------+
```

### Características Clave:
- **Cálculo de IMC en Tiempo Real**: Al ingresar peso y talla, el IMC y su categoría (Bajo peso, Normal, Sobrepeso, Obesidad) se computan instantáneamente en la interfaz y se envían calculados.
- **Selector de Diagnósticos Inteligente**: Búsqueda asistida por código CIE-10 o descripción con clasificación rápida (Presuntivo, Definitivo, Primario, Secundario).
- **Autoguardado de Borrador**: El médico puede pausar la consulta o guardar el borrador en cualquier momento sin perder avances.
- **Sellado Seguro al Finalizar**: Modal de confirmación con validación de campos obligatorios. Al finalizar, la vista conmuta al modo lectura inmutable con opción visible de **"Agregar Adenda Clínica"**.

---

## 4. Constructor de Plantillas Clínicas (Template Builder)

El configurador administrativo utiliza una disposición en **3 Columnas** ergonómica:

```
+-------------------+------------------------------------+------------------------------+
|   PANEL IZQUIERDO |           PANEL CENTRAL            |        PANEL DERECHO         |
|   (Secciones)     |     (Campos de la Sección)         |   (Configuración de Campo)   |
+-------------------+------------------------------------+------------------------------+
| + Nueva Sección   | + Agregar Campo                    | Label: Presión Sistólica     |
|                   |                                    | Code: sys_bp                 |
| 1. Motivo [::]    | [::] Presión Sistólica (DECIMAL)   | Tipo: DECIMAL                |
| 2. Signos [::]    | [::] Presión Diastólica (DECIMAL)  | Unidad: mmHg                 |
| 3. Examen [::]    | [::] Frecuencia Cardíaca (INTEGER) | Min: 40 | Max: 250           |
| 4. Plan   [::]    | [::] Observaciones (TEXTAREA)      | [x] Requerido                |
|                   |                                    | [x] Alerta Clínica           |
|                   |                                    | [x] Visible al Paciente      |
+-------------------+------------------------------------+------------------------------+
| [ Previsualizar Plantilla ]                    [ Guardar Borrador ]    [ Publicar v1 ]|
+---------------------------------------------------------------------------------------+
```

### Funcionalidades del Builder:
- **Drag & Drop**: Reordenamiento fluido de secciones y campos con persistencia inmediata del atributo `order`.
- **Vista Previa en Vivo (Live Preview)**: Modal que renderiza la plantilla exactamente como la experimentará el médico durante la consulta.
- **Control de Versiones**: Botón de publicación con confirmación de inmutabilidad y generación de nuevas versiones con un clic.

---

## 5. Adaptabilidad Responsive (Desktop, Tablet y Móvil)

- **Configurador de Plantillas**: Optimizado para pantallas de escritorio (>= 1024px) con scroll independiente por columna.
- **Consulta Médica (Médico)**: Optimizado para Desktop y Tablets (iPad / Galaxy Tab asistenciales en consultorio), con controles táctiles amplios, teclados numéricos para signos vitales y botones de acción destacados.
- **Resumen del Paciente (Móvil)**: Completamente adaptado para smartphones para que el paciente o el médico de guardia revisen alertas y antecedentes de manera vertical sin fricción.
