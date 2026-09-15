# NEXARIS LINK & SURHOSPITAL — UI/UX DESIGN SYSTEM GUIDE

**Concepto Visual:** Healthcare Premium + Modern SaaS  
**Principios:** Confianza, Claridad Clínica, Accesibilidad (WCAG AA), Mobile-First, Microinteracciones Fluidas (150-250ms).  
**Regla de Marca:** White-Label desacoplado. En el portal público y de paciente la marca predominante es **SurHospital**; en el backoffice administrativo se presenta como **Nexaris Link | SurHospital** con footer discreto *Powered by Nexaris Link*.

---

## 1. Paleta de Colores & Tokens

| Token | Hex / Valor | Uso Principal |
| :--- | :--- | :--- |
| `primary` | `#0700FF` | Acciones principales (CTA), links destacados, branding de acento, estados seleccionados. |
| `primary-dark` | `#0500C9` | Estados hover y active en botones principales. |
| `primary-soft` | `#EEF0FF` | Fondos de selección, chips activos, badges suaves primarios. |
| `background` | `#F7F8FC` | Fondo general de la aplicación (evita fatiga visual). |
| `surface` | `#FFFFFF` | Tarjetas, contenedores modales, tablas y drawers. |
| `text-primary` | `#172033` | Títulos, etiquetas principales, texto de alta legibilidad. |
| `text-secondary` | `#667085` | Subtítulos, descripciones secundarias, timestamps. |
| `border` | `#E5E7EB` | Delimitadores de cards, separadores y bordes de inputs. |
| `success` | `#12B76A` | Citas confirmadas, pagos aprobados, validaciones exitosas. |
| `warning` | `#F79009` | Pagos pendientes, citas por confirmar, alertas operativas. |
| `error` | `#D92D20` | Cancelaciones, ausencias, errores de validación inline. |

---

## 2. Tipografía & Jerarquía

- **Fuente:** `Inter`, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif.
- **Jerarquía:**
  - **H1:** `text-3xl lg:text-4xl font-bold tracking-tight text-slate-900` (32px - 40px)
  - **H2:** `text-2xl lg:text-3xl font-semibold text-slate-900` (24px - 30px)
  - **H3:** `text-lg lg:text-xl font-semibold text-slate-800` (18px - 22px)
  - **Body:** `text-sm lg:text-base text-slate-600 leading-relaxed` (14px - 16px)
  - **Labels / Captions:** `text-xs lg:text-sm font-medium text-slate-700` (12px - 14px)

---

## 3. Catálogo de Componentes UI

1. **Botones (`x-button`):**
   - *Primary:* Fondo `#0700FF`, texto blanco, sombra sutil, hover `#0500C9`, focus ring accesible.
   - *Secondary / Outline:* Borde `#E5E7EB`, fondo blanco, texto `#172033`, hover fondo slate-50.
   - *Danger:* Fondo `#D92D20`, texto blanco, hover `#B42318`.
   - *Ghost:* Sin borde, hover suave `bg-slate-100`.
2. **Formularios & Inputs:**
   - Label siempre visible con indicador de obligatorio.
   - Input con altura mínima de 44px (touch target óptimo).
   - Borde de focus con color primario `#0700FF` y ring suave de 2px.
   - Mensajes de error en color rojo debajo del input con ícono de advertencia.
3. **Time Slot Selector (`x-time-slot`):**
   - Botón tipo chip táctil (min-h: 44px).
   - Estado disponible: Fondo blanco, borde `#E5E7EB`, texto `#172033`, hover borde primario.
   - Estado seleccionado: Fondo `#0700FF`, texto blanco, shadow primario.
   - Estado deshabilitado / ocupado: Fondo slate-100, texto slate-400, no clicable con indicador visual.
4. **Badges de Estado (`x-status-badge`):**
   - *Confirmada:* `bg-emerald-50 text-emerald-700 border border-emerald-200`
   - *Pendiente de Pago / Por Confirmar:* `bg-amber-50 text-amber-700 border border-amber-200`
   - *Cancelada:* `bg-rose-50 text-rose-700 border border-rose-200`
   - *Atendida / Completada:* `bg-blue-50 text-blue-700 border border-blue-200`
   - *No Asistió (No Show):* `bg-slate-100 text-slate-700 border border-slate-300`
5. **Feedback & Notificaciones:**
   - Cero uso de `alert()` nativo del navegador.
   - Toasts flotantes con animación de entrada y auto-dismiss (Alpine.js).
   - Modales accesibles con foco bloqueado y cierre con Escape.
   - Drawers laterales suaves para agendamiento rápido y edición sin salir de pantalla.

---

## 4. Experiencia de Usuario & Flujos Clave

### 4.1. Wizard de Agendamiento (Público & Portal Paciente)
Flujo optimizado en 5 pasos móviles completables con una mano:
1. **Especialidad:** Grid de tarjetas visuales con íconos médicos y precio base.
2. **Servicio o Médico:** Selección opcional de procedimiento y médico de preferencia.
3. **Fecha y Turno:** Calendario interactivo + Chips de Time Slots en tiempo real.
4. **Identificación del Paciente:** Autenticación o captura rápida de datos (Cédula, Nombre, Teléfono, Email).
5. **Pago & Confirmación:** Selección de Pago Online / Transferencia Bancaria y emisión de código de cita con resumen descargable.

### 4.2. Layout Administrativo
- Sidebar izquierdo colapsable con navegación segmentada según rol (Admin, Recepción, Médico).
- Header fijo con barra de búsqueda rápida, notificaciones y selector de perfil.
- Vista de Agenda en formato diario y semanal con filtros instantáneos.
- Click en cita abre Drawer lateral instantáneo para confirmación, reprogramación, recepción de pago o cancelación.
