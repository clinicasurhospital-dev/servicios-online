<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelación de Cita - {{ $brandName }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F4F6FB; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1E293B;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F4F6FB; padding: 30px 10px;">
        <tr>
            <td align="center">
                <!-- Main Card Container -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #FFFFFF; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #E2E8F0;">
                    
                    <!-- Header Banner -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%); padding: 35px 30px; text-align: center;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <div style="display: inline-block; background: rgba(255,255,255,0.2); padding: 8px 18px; border-radius: 50px; margin-bottom: 12px; border: 1px solid rgba(255,255,255,0.3);">
                                            <span style="color: #FFFFFF; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">
                                                ❌ Cancelación de Cita
                                            </span>
                                        </div>
                                        <h1 style="color: #FFFFFF; font-size: 24px; font-weight: 900; margin: 0 0 6px 0; letter-spacing: -0.5px;">{{ $brandName }}</h1>
                                        <p style="color: #FEE2E2; font-size: 13px; margin: 0; font-weight: 500;">
                                            Notificación de turno médico cancelado y cupo liberado
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 35px 30px;">
                            @if($isForStaff)
                                <p style="font-size: 16px; line-height: 1.6; margin: 0 0 16px 0; color: #1E293B;">
                                    Estimado equipo de <strong>Administración, Recepción y Especialista</strong>,
                                </p>
                                <p style="font-size: 14px; line-height: 1.6; margin: 0 0 24px 0; color: #475569;">
                                    Se ha procesado la cancelación de la cita médica <strong>#{{ $appointment->code }}</strong> del paciente <strong>{{ $appointment->patient?->full_name }}</strong>. El cupo ha quedado liberado en la agenda.
                                </p>
                            @else
                                <p style="font-size: 16px; line-height: 1.6; margin: 0 0 16px 0; color: #1E293B;">
                                    Estimado(a) <strong>{{ $appointment->patient?->full_name ?: 'Paciente' }}</strong>,
                                </p>
                                <p style="font-size: 14px; line-height: 1.6; margin: 0 0 24px 0; color: #475569;">
                                    Te informamos que tu cita médica <strong>#{{ $appointment->code }}</strong> en <strong>{{ $brandName }}</strong> ha sido cancelada satisfactoriamente.
                                </p>
                            @endif

                            <!-- Details Box -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 16px; margin-bottom: 28px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 16px 20px; background-color: #FEF2F2; border-bottom: 1px solid #FEE2E2;">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="font-size: 12px; font-weight: 700; color: #DC2626; text-transform: uppercase; letter-spacing: 0.5px;">Código de Cita Cancelada</td>
                                                <td align="right" style="font-size: 16px; font-weight: 900; color: #DC2626; font-family: monospace;">#{{ $appointment->code }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B; width: 40%;">🩺 Especialidad:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0F172A;">{{ $appointment->specialty?->name ?? 'Consulta General' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">👨‍⚕️ Médico Asignado:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0F172A;">Dr(a). {{ $appointment->doctor?->user?->name ?? 'Especialista' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">📅 Horario que correspondía:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #64748B;">{{ $appointment->start_at->translatedFormat('d M Y - h:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">⚠️ Motivo de Cancelación:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #DC2626;">{{ $reason }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">👤 Gestionado por:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 500; color: #0F172A;">{{ $cancelledByRole }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            @if(!$isForStaff)
                                <!-- Re-book Notice Box -->
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-left: 4px solid #0700FF; border-radius: 8px; margin-bottom: 25px;">
                                    <tr>
                                        <td style="padding: 16px 20px;">
                                            <p style="margin: 0 0 6px 0; font-size: 13px; font-weight: 700; color: #1E293B;">
                                                ¿Deseas agendar en otra ocasión?
                                            </p>
                                            <p style="margin: 0; font-size: 12px; line-height: 1.5; color: #64748B;">
                                                Puedes ingresar a nuestra plataforma en cualquier momento para elegir una nueva fecha y especialista de tu preferencia.
                                            </p>
                                        </td>
                                    </tr>
                                </table>

                                <!-- CTA Button -->
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 25px;">
                                    <tr>
                                        <td align="center">
                                            <a href="{{ route('booking') }}" target="_blank" style="display: inline-block; background-color: #0700FF; color: #FFFFFF; font-size: 14px; font-weight: 700; text-decoration: none; padding: 14px 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(7, 0, 255, 0.35); text-align: center;">
                                                Agendar Nueva Cita Médica
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            @endif
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
