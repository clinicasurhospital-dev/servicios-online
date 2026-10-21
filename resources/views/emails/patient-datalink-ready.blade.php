<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enlace de Pago DataLink</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F8FAFC; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1E293B;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #F8FAFC; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #FFFFFF; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid #E2E8F0;" cellspacing="0" cellpadding="0">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #7C3AED 0%, #0700FF 100%); padding: 35px 30px; text-align: center;">
                            <div style="display: inline-block; background-color: rgba(255,255,255,0.2); color: #FFFFFF; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;">
                                💳 Enlace de Pago Listo
                            </div>
                            <h1 style="color: #FFFFFF; font-size: 22px; font-weight: 900; margin: 0 0 6px 0; letter-spacing: -0.5px;">
                                Paga tu Cita con DataLink
                            </h1>
                            <p style="color: rgba(255,255,255,0.9); font-size: 13px; margin: 0;">
                                Cita Médica #<strong style="color: #FFFFFF;">{{ $appointment->code }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px 30px 20px 30px;">
                            <p style="font-size: 15px; line-height: 1.6; color: #334155; margin-top: 0; margin-bottom: 18px;">
                                Estimado/a <strong>{{ $patientName }}</strong>,<br><br>
                                Nuestro equipo de recepción ha generado tu <strong>enlace oficial de pago DataLink (Datafast)</strong> para tu consulta médica.
                            </p>

                            <!-- Appointment Summary -->
                            <table role="presentation" width="100%" style="background-color: #F8FAFC; border-radius: 14px; border: 1px solid #E2E8F0; margin-bottom: 24px;" cellspacing="0" cellpadding="14">
                                <tr>
                                    <td>
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; padding-bottom: 6px;">Detalle del Turno:</td>
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
                                                    <span style="font-size: 18px; font-weight: 900; color: #7C3AED; float: right;">${{ number_format($amount, 2) }} USD</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            @if($notes)
                            <div style="background-color: #F1F5F9; border-radius: 12px; padding: 12px 14px; margin-bottom: 20px; font-size: 12px; color: #475569;">
                                <strong>Nota de Recepción:</strong> {{ $notes }}
                            </div>
                            @endif

                            <!-- Big CTA Button to DataLink -->
                            <div style="text-align: center; margin: 28px 0;">
                                <a href="{{ $dataLinkUrl }}" target="_blank" style="display: inline-block; background-color: #7C3AED; color: #FFFFFF; font-size: 16px; font-weight: 900; text-decoration: none; padding: 16px 36px; border-radius: 14px; box-shadow: 0 4px 15px rgba(124, 58, 237, 0.4); letter-spacing: 0.3px;">
                                    💳 Pagar con DataLink Ahora
                                </a>
                                <p style="font-size: 11px; color: #64748B; margin-top: 10px;">
                                    Acepta Visa, Mastercard, American Express, Diners Club y tarjetas de débito.
                                </p>
                            </div>

                            <!-- Fallback Link -->
                            <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 12px; margin-bottom: 24px;">
                                <p style="font-size: 11px; color: #64748B; margin: 0 0 6px 0;">O copia y pega el enlace en tu navegador:</p>
                                <a href="{{ $dataLinkUrl }}" target="_blank" style="font-size: 11px; color: #7C3AED; word-break: break-all; font-family: monospace; font-weight: 600;">{{ $dataLinkUrl }}</a>
                            </div>

                            <!-- Information -->
                            <div style="border-left: 3px solid #16A34A; padding-left: 12px; margin-bottom: 15px; font-size: 12px; color: #15803D; line-height: 1.5;">
                                <strong>Confirmación Inmediata:</strong> Al completar el pago a través de la pasarela segura Datafast, tu cita se confirmará y podrás descargar tu comprobante electrónico.
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
