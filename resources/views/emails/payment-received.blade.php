<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Pago - {{ $brandName }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F4F6FB; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1E293B;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F4F6FB; padding: 30px 10px;">
        <tr>
            <td align="center">
                <!-- Main Card Container -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #FFFFFF; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #E2E8F0;">
                    
                    <!-- Header Banner -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #16A34A 0%, #15803D 100%); padding: 35px 30px; text-align: center;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <div style="display: inline-block; background: rgba(255,255,255,0.2); padding: 8px 18px; border-radius: 50px; margin-bottom: 12px; border: 1px solid rgba(255,255,255,0.3);">
                                            <span style="color: #FFFFFF; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">
                                                {{ $isForStaff ? '💰 Recaudación / Pago Registrado' : '✅ Pago Confirmado' }}
                                            </span>
                                        </div>
                                        <h1 style="color: #FFFFFF; font-size: 24px; font-weight: 900; margin: 0 0 6px 0; letter-spacing: -0.5px;">{{ $brandName }}</h1>
                                        <p style="color: #DCFCE7; font-size: 13px; margin: 0; font-weight: 500;">
                                            {{ $isForStaff ? 'Comprobante de recaudación para Administración y Recepción' : 'Hemos recibido el pago de tu consulta médica' }}
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
                                    Estimado equipo de <strong>Administración y Recepción</strong>,
                                </p>
                                <p style="font-size: 14px; line-height: 1.6; margin: 0 0 24px 0; color: #475569;">
                                    Se ha registrado un nuevo pago en el sistema correspondiente a la cita médica <strong>#{{ $appointment->code }}</strong> del paciente <strong>{{ $appointment->patient?->full_name }}</strong>.
                                </p>
                            @else
                                <p style="font-size: 16px; line-height: 1.6; margin: 0 0 16px 0; color: #1E293B;">
                                    Estimado(a) <strong>{{ $appointment->patient?->full_name ?: 'Paciente' }}</strong>,
                                </p>
                                <p style="font-size: 14px; line-height: 1.6; margin: 0 0 24px 0; color: #475569;">
                                    Confirmamos la recepción de tu pago para la cita médica <strong>#{{ $appointment->code }}</strong> en <strong>{{ $brandName }}</strong>. Tu cita se encuentra formalizada.
                                </p>
                            @endif

                            <!-- Payment Detail Box -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 16px; margin-bottom: 28px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 16px 20px; background-color: #F0FDF4; border-bottom: 1px solid #E2E8F0;">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="font-size: 12px; font-weight: 700; color: #16A34A; text-transform: uppercase; letter-spacing: 0.5px;">Monto Total Procesado</td>
                                                <td align="right" style="font-size: 18px; font-weight: 900; color: #15803D; font-family: monospace;">${{ number_format((float) $payment->amount, 2) }} USD</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B; width: 40%;">💳 Método de Pago:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0F172A;">{{ $payment->payment_method->label() }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">👤 Paciente:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0F172A;">{{ $appointment->patient?->full_name }} ({{ $appointment->patient?->identification_number }})</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">🩺 Especialidad:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0F172A;">{{ $appointment->specialty?->name ?? 'Consulta General' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">👨‍⚕️ Médico Asignado:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0F172A;">Dr(a). {{ $appointment->doctor?->user?->name ?? 'Especialista' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">📅 Fecha y Hora Cita:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0700FF;">{{ $appointment->start_at->translatedFormat('d M Y - h:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">⏱️ Fecha del Pago:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0F172A;">{{ ($payment->paid_at ?? now())->translatedFormat('d \d\e F \d\e Y, H:i') }}</td>
                                            </tr>
                                            @if($note)
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">📝 Detalle / Nota:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 500; color: #0F172A;">{{ $note }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 25px;">
                                <tr>
                                    <td align="center">
                                        @if($isForStaff)
                                            <a href="{{ route('admin.appointments') }}?search={{ $appointment->code }}" target="_blank" style="display: inline-block; background-color: #16A34A; color: #FFFFFF; font-size: 14px; font-weight: 700; text-decoration: none; padding: 14px 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(22, 163, 74, 0.35); text-align: center;">
                                                Gestionar Cita y Pagos en Panel
                                            </a>
                                        @else
                                            <a href="{{ route('patient.appointment.show', $appointment) }}" target="_blank" style="display: inline-block; background-color: #0700FF; color: #FFFFFF; font-size: 14px; font-weight: 700; text-decoration: none; padding: 14px 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(7, 0, 255, 0.35); text-align: center;">
                                                Ver Detalles en Portal del Paciente
                                            </a>
                                        @endif
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
