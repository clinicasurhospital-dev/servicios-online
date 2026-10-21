<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita Reservada - Enlace DataLink en Gestión</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F8FAFC; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1E293B;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #F8FAFC; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #FFFFFF; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid #E2E8F0;" cellspacing="0" cellpadding="0">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0700FF 0%, #4F46E5 100%); padding: 35px 30px; text-align: center;">
                            <div style="display: inline-block; background-color: rgba(255,255,255,0.2); color: #FFFFFF; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;">
                                ⏳ Cita Reservada con Éxito
                            </div>
                            <h1 style="color: #FFFFFF; font-size: 22px; font-weight: 900; margin: 0 0 6px 0; letter-spacing: -0.5px;">
                                Enlace de Pago DataLink en Proceso
                            </h1>
                            <p style="color: rgba(255,255,255,0.9); font-size: 13px; margin: 0;">
                                Código de Cita: <strong style="color: #FFFFFF;">#{{ $appointment->code }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px 30px 20px 30px;">
                            <p style="font-size: 15px; line-height: 1.6; color: #334155; margin-top: 0; margin-bottom: 18px;">
                                Estimado/a <strong>{{ $patientName }}</strong>,<br><br>
                                Tu cita médica ha sido <strong>registrada y reservada exitosamente</strong> en nuestro sistema institucional.
                            </p>

                            <!-- Notice Box DataLink -->
                            <div style="background-color: #FAF5FF; border: 1px solid #E9D5FF; border-left: 4px solid #7C3AED; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
                                <div style="display: flex; align-items: center; margin-bottom: 6px;">
                                    <span style="font-size: 18px; margin-right: 8px;">🔗</span>
                                    <strong style="color: #6B21A8; font-size: 13px;">Pago Asistido con Enlace DataLink (Datafast)</strong>
                                </div>
                                <p style="font-size: 12px; color: #581C87; margin: 0; line-height: 1.6;">
                                    Has seleccionado el método de <strong>Enlace de Pago</strong>. Nuestro personal de recepción está generando tu link oficial y te lo remitirá por <strong>WhatsApp</strong> y <strong>Correo Electrónico</strong> para que puedas pagar con cualquier tarjeta de crédito o débito de forma rápida y segura.
                                </p>
                            </div>

                            <!-- Appointment Summary -->
                            <table role="presentation" width="100%" style="background-color: #F8FAFC; border-radius: 14px; border: 1px solid #E2E8F0; margin-bottom: 24px;" cellspacing="0" cellpadding="14">
                                <tr>
                                    <td>
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; padding-bottom: 8px;">Resumen de tu Consulta:</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 13px; color: #334155; padding-bottom: 4px;">
                                                    👨‍⚕️ <strong>Médico:</strong> Dr(a). {{ $appointment->doctor?->user?->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 13px; color: #334155; padding-bottom: 4px;">
                                                    🩺 <strong>Especialidad:</strong> {{ $appointment->specialty?->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 13px; color: #334155; padding-bottom: 8px;">
                                                    📅 <strong>Fecha y Hora:</strong> {{ $appointment->start_at?->format('d/m/Y - H:i') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border-top: 1px dashed #CBD5E1; padding-top: 10px; font-size: 14px; font-weight: 800; color: #0F172A;">
                                                    Total a Pagar:
                                                    <span style="font-size: 18px; font-weight: 900; color: #0700FF; float: right;">${{ number_format($amount, 2) }} USD</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Instructions -->
                            <div style="background-color: #EFF6FF; border-radius: 12px; padding: 14px; margin-bottom: 24px; font-size: 12px; color: #1E40AF; line-height: 1.5;">
                                <strong>¿Qué sucede después?</strong><br>
                                Tan pronto realices el pago a través del enlace de DataLink, tu turno quedará inmediatamente confirmado en la agenda de tu especialista y recibirás tu comprobante electrónico.
                            </div>

                            <!-- WhatsApp Contact Button -->
                            <div style="text-align: center; margin-bottom: 24px;">
                                <p style="font-size: 12px; color: #64748B; margin-bottom: 10px;">¿Deseas agilizar la emisión de tu enlace?</p>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $brandWhatsapp) }}?text={{ urlencode('Hola SurHospital, acabo de reservar la cita #' . $appointment->code . ' para ' . $patientName . ' con método Enlace de Pago DataLink. ¿Me ayudan con el link de pago?') }}" target="_blank" style="display: inline-block; background-color: #16A34A; color: #FFFFFF; font-size: 13px; font-weight: 800; text-decoration: none; padding: 12px 24px; border-radius: 12px; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);">
                                    💬 Escribir a Recepción por WhatsApp
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #0F172A; padding: 25px 30px; text-align: center; color: #94A3B8; font-size: 11px; line-height: 1.6;">
                            <p style="margin: 0 0 4px 0; color: #FFFFFF; font-weight: 700;">{{ $brandName }}</p>
                            <p style="margin: 0 0 6px 0;">{{ $brandAddress }}</p>
                            <p style="margin: 0;">PBX: {{ $brandPhone }} &nbsp;|&nbsp; WhatsApp: {{ $brandWhatsapp }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
