<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación Final de Cita - {{ $brandName }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F4F6FB; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1E293B;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F4F6FB; padding: 30px 10px;">
        <tr>
            <td align="center">
                <!-- Main Card Container -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #FFFFFF; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #E2E8F0;">
                    
                    <!-- Header Banner -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0700FF 0%, #0500C9 100%); padding: 35px 30px; text-align: center;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <div style="display: inline-block; background: rgba(255,255,255,0.15); padding: 8px 18px; border-radius: 50px; margin-bottom: 12px; border: 1px solid rgba(255,255,255,0.25);">
                                                ✅ CITA CONFIRMADA
                                            </span>
                                        </div>
                                        <h1 style="color: #FFFFFF; font-size: 24px; font-weight: 900; margin: 0 0 6px 0; letter-spacing: -0.5px;">{{ $brandName }}</h1>
                                        <p style="color: #DDE2FF; font-size: 13px; margin: 0; font-weight: 500;">
                                            Administración ha confirmado tu cita médica
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 35px 30px;">
                            <p style="font-size: 16px; line-height: 1.6; margin: 0 0 16px 0; color: #1E293B;">
                                Estimado(a) <strong>{{ $appointment->patient?->full_name ?: 'Paciente' }}</strong>,
                            </p>
                            <p style="font-size: 14px; line-height: 1.6; margin: 0 0 24px 0; color: #475569;">
                                Nos complace informarte que la administración de <strong>{{ $brandName }}</strong> ha <strong>CONFIRMADO</strong> exitosamente tu cita médica. A continuación te presentamos el resumen de tu consulta:
                            </p>

                            <!-- Appointment Summary Box -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 16px; margin-bottom: 28px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 16px 20px; background-color: #EEF0FF; border-bottom: 1px solid #E2E8F0;">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="font-size: 12px; font-weight: 700; color: #0700FF; text-transform: uppercase; letter-spacing: 0.5px;">Código de Turno</td>
                                                <td align="right" style="font-size: 16px; font-weight: 900; color: #0700FF; font-family: monospace;">#{{ $appointment->code }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B; width: 40%;">👤 Paciente:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0F172A;">{{ $appointment->patient?->full_name }} ({{ $appointment->patient?->identification_number }})</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">🩺 Especialidad:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0F172A;">{{ $appointment->specialty?->name ?? 'Consulta General' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">👨‍⚕️ Médico / Especialista:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0F172A;">Dr(a). {{ $appointment->doctor?->user?->name ?? 'Especialista de Turno' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">📅 Fecha de Atención:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0F172A;">{{ $appointment->start_at->translatedFormat('l, d \d\e F \d\e Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">⏰ Hora de Consulta:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0700FF;">{{ $appointment->start_at->format('h:i A') }} (Duración: {{ $appointment->duration_minutes }} min)</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">💰 Valor Consulta:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 900; color: #16A34A;">${{ number_format($appointment->price, 2) }} USD</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">📌 Estado de la Cita:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0F172A;">{{ $appointment->status->label() }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 25px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('patient.appointment.show', $appointment) }}" target="_blank" style="display: inline-block; background-color: #0700FF; color: #FFFFFF; font-size: 14px; font-weight: 700; text-decoration: none; padding: 14px 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(7, 0, 255, 0.35); text-align: center;">
                                            Ver Mi Cita en Portal del Paciente
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Important Guidelines Box -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-left: 4px solid #0700FF; border-radius: 8px; margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 6px 0; font-size: 13px; font-weight: 700; color: #1E293B;">
                                            📋 Recomendaciones para tu atención:
                                        </p>
                                        <ul style="margin: 0; padding-left: 18px; font-size: 12px; line-height: 1.6; color: #64748B;">
                                            <li>Presentarse con <strong>15 minutos de anticipación</strong> a la hora programada en recepción.</li>
                                            <li>Llevar documento de identidad original (Cédula o Pasaporte).</li>
                                            <li>Si tienes exámenes o historias clínicas previas, puedes llevarlos a la consulta.</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #F8FAFC; border-top: 1px solid #E2E8F0; padding: 25px 30px; text-align: center;">
                            <p style="font-size: 13px; font-weight: 700; color: #334155; margin: 0 0 8px 0;">
                                {{ $brandName }}
                            </p>
                            <p style="font-size: 12px; color: #64748B; margin: 0 0 12px 0; line-height: 1.5;">
                                📍 {{ $brandAddress }}<br>
                                📞 {{ $brandPhone }} &nbsp;|&nbsp; 💬 WhatsApp: {{ $brandWhatsapp }}
                            </p>
                            <p style="font-size: 11px; color: #94A3B8; margin: 0;">
                                © {{ date('Y') }} {{ $brandName }}. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
