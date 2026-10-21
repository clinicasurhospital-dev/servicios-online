<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acción Requerida: Enlace de Pago DataLink</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F8FAFC; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1E293B;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #F8FAFC; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #FFFFFF; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid #E2E8F0;" cellspacing="0" cellpadding="0">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #7C3AED 0%, #4F46E5 100%); padding: 30px 30px; text-align: center;">
                            <div style="display: inline-block; background-color: rgba(255,255,255,0.2); color: #FFFFFF; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;">
                                ⚠️ Gestión Manual de Recepción / Caja
                            </div>
                            <h1 style="color: #FFFFFF; font-size: 22px; font-weight: 900; margin: 0 0 6px 0; letter-spacing: -0.5px;">
                                Emitir Enlace DataLink
                            </h1>
                            <p style="color: rgba(255,255,255,0.9); font-size: 13px; margin: 0;">
                                Cita Médica #<strong style="color: #FFFFFF;">{{ $appointment->code }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px 30px 20px 30px;">
                            <p style="font-size: 14px; line-height: 1.6; color: #334155; margin-top: 0; margin-bottom: 20px;">
                                Estimado equipo de <strong>Recepción y Administración</strong>,<br><br>
                                El paciente <strong>{{ $patientName }}</strong> ha agendado una cita médica y seleccionó el método de pago <strong style="color: #7C3AED;">Enlace de Pago (DataLink manual)</strong>. Se requiere generar el link en el portal DataLink / Datafast y remitirlo al paciente para completar el cobro.
                            </p>

                            <!-- Patient & Appointment Card -->
                            <table role="presentation" width="100%" style="background-color: #F8FAFC; border-radius: 14px; border: 1px solid #E2E8F0; margin-bottom: 24px;" cellspacing="0" cellpadding="14">
                                <tr>
                                    <td>
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; padding-bottom: 6px;">Datos del Paciente:</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 13px; font-weight: 800; color: #0F172A; padding-bottom: 3px;">
                                                    👤 {{ $patientName }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 12px; color: #475569; padding-bottom: 3px;">
                                                    📱 Teléfono / WhatsApp: <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $patientPhone) }}" style="color: #059669; font-weight: 700; text-decoration: none;">{{ $patientPhone ?: 'No registrado' }}</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 12px; color: #475569; padding-bottom: 12px;">
                                                    ✉️ Correo: <span style="color: #2563EB;">{{ $patientEmail ?: 'No registrado' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border-top: 1px dashed #CBD5E1; padding-top: 10px; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; padding-bottom: 6px;">Detalle del Servicio:</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 12px; color: #334155; padding-bottom: 3px;">
                                                    👨‍⚕️ <strong>Médico:</strong> Dr(a). {{ $appointment->doctor?->user?->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 12px; color: #334155; padding-bottom: 3px;">
                                                    🩺 <strong>Especialidad:</strong> {{ $appointment->specialty?->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 12px; color: #334155; padding-bottom: 3px;">
                                                    📅 <strong>Fecha y Hora:</strong> {{ $appointment->start_at?->format('d/m/Y - H:i') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border-top: 1px dashed #CBD5E1; padding-top: 10px; font-size: 13px; font-weight: 800; color: #0F172A;">
                                                    Valor a Cobrar en DataLink:
                                                    <span style="font-size: 18px; font-weight: 900; color: #7C3AED; float: right;">${{ number_format($amount, 2) }} USD</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Action Instructions -->
                            <div style="background-color: #FAF5FF; border: 1px solid #E9D5FF; border-radius: 14px; padding: 16px; margin-bottom: 24px;">
                                <h4 style="margin: 0 0 8px 0; font-size: 12px; font-weight: 800; color: #6B21A8; text-transform: uppercase;">
                                    Pasos para Completar la Gestión:
                                </h4>
                                <ol style="margin: 0; padding-left: 20px; font-size: 12px; color: #581C87; line-height: 1.6;">
                                    <li>Ingresa al portal de DataLink (Datafast) y genera un link por <strong>${{ number_format($amount, 2) }} USD</strong>.</li>
                                    <li>Haz clic en el botón inferior para abrir el módulo <strong>Validar Pagos</strong> en el sistema.</li>
                                    <li>Pega el enlace generado y presiona <strong>Enviar por WhatsApp</strong> o <strong>Enviar por Correo</strong> para que el paciente pague.</li>
                                    <li>Una vez acreditado, marca la cita como <strong>Pagada</strong>.</li>
                                </ol>
                            </div>

                            <!-- CTA Button -->
                            <div style="text-align: center; margin-bottom: 10px;">
                                <a href="{{ $adminUrl }}" target="_blank" style="display: inline-block; background-color: #7C3AED; color: #FFFFFF; font-size: 14px; font-weight: 800; text-decoration: none; padding: 15px 32px; border-radius: 14px; box-shadow: 0 4px 15px rgba(124, 58, 237, 0.35);">
                                    🔗 Abrir Módulo de Validar Pagos &rarr;
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #0F172A; padding: 20px 30px; text-align: center; color: #94A3B8; font-size: 11px;">
                            <p style="margin: 0 0 4px 0; color: #FFFFFF; font-weight: 700;">{{ $brandName }}</p>
                            <p style="margin: 0;">Sistema de Gestión de Citas Médicas y Recepción</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
