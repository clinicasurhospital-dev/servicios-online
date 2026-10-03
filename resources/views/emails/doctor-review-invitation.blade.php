<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valoración y Sugerencias de tu Consulta Médica - {{ $brandName }}</title>
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
                                        <div style="display: inline-block; background: rgba(255,255,255,0.18); padding: 8px 18px; border-radius: 50px; margin-bottom: 12px; border: 1px solid rgba(255,255,255,0.3);">
                                            <span style="color: #FFFFFF; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">⭐ Experiencia del Paciente</span>
                                        </div>
                                        <h1 style="color: #FFFFFF; font-size: 24px; font-weight: 900; margin: 0 0 6px 0; letter-spacing: -0.5px;">{{ $brandName }}</h1>
                                        <p style="color: #DDE2FF; font-size: 13px; margin: 0; font-weight: 500;">Tu opinión nos ayuda a seguir mejorando para ti</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 35px 30px;">
                            <p style="font-size: 16px; line-height: 1.6; margin: 0 0 16px 0; color: #1E293B;">
                                Hola <strong>{{ $patientName ?: 'Paciente' }}</strong>,
                            </p>
                            <p style="font-size: 14px; line-height: 1.6; margin: 0 0 24px 0; color: #475569;">
                                Esperamos que tu reciente consulta médica haya sido de total satisfacción. Para nosotros en <strong>{{ $brandName }}</strong> tu experiencia y tus sugerencias son fundamentales para mantener nuestro compromiso con la excelencia médica.
                            </p>

                            <!-- Consultation Summary Box -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 16px; margin-bottom: 28px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 16px 20px; background-color: #EEF0FF; border-bottom: 1px solid #E2E8F0;">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="font-size: 12px; font-weight: 700; color: #0700FF; text-transform: uppercase; letter-spacing: 0.5px;">Detalle de la Consulta</td>
                                                <td align="right" style="font-size: 13px; font-weight: 800; color: #0700FF; font-family: monospace;">#{{ $appointment->code }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B; width: 42%;">👨‍⚕️ Médico Especialista:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0F172A;">{{ $doctor?->user?->name ?? 'Especialista' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">🩺 Especialidad:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0F172A;">{{ $appointment->specialty?->name ?? 'Consulta General' }}</td>
                                            </tr>
                                            @if($appointment->procedure)
                                                <tr>
                                                    <td style="padding: 6px 0; font-size: 13px; color: #64748B;">📋 Procedimiento:</td>
                                                    <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0F172A;">{{ $appointment->procedure->name }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #64748B;">📅 Fecha de Atención:</td>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0700FF;">
                                                    {{ $appointment->start_at->translatedFormat('l, d \d\e F \d\e Y') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Interactive Stars CTA Box -->
                            <div style="text-align: center; background: linear-gradient(180deg, #F8FAFC 0%, #EFF6FF 100%); border: 1px solid #DBEAFE; border-radius: 18px; padding: 26px 20px; margin-bottom: 28px;">
                                <p style="font-size: 15px; font-weight: 800; color: #1E293B; margin: 0 0 12px 0;">
                                    ¿Cómo calificarías la atención de tu doctor?
                                </p>
                                <div style="margin-bottom: 20px; font-size: 28px; line-height: 1; letter-spacing: 4px;">
                                    ⭐⭐⭐⭐⭐
                                </div>
                                <a href="{{ $reviewUrl }}" target="_blank" style="display: inline-block; background-color: #0700FF; color: #FFFFFF; font-size: 15px; font-weight: 800; text-decoration: none; padding: 16px 36px; border-radius: 14px; box-shadow: 0 4px 15px rgba(7, 0, 255, 0.35); text-align: center; letter-spacing: 0.3px;">
                                    ✨ Dejar mi Valoración y Sugerencias &rarr;
                                </a>
                                <p style="font-size: 12px; color: #64748B; margin: 12px 0 0 0;">
                                    Solo te tomará <strong>1 minuto</strong> y puedes enviarlo de forma <strong>anónima</strong> si lo prefieres.
                                </p>
                            </div>

                            <!-- Direct Link Fallback -->
                            <div style="background-color: #F1F5F9; border-radius: 12px; padding: 14px; margin-bottom: 24px;">
                                <p style="font-size: 11px; color: #64748B; margin: 0 0 6px 0;">Enlace directo:</p>
                                <a href="{{ $reviewUrl }}" style="font-size: 12px; color: #0700FF; word-break: break-all; font-family: monospace; font-weight: 600;">{{ $reviewUrl }}</a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #0F172A; padding: 25px 30px; text-align: center; color: #94A3B8; font-size: 12px; line-height: 1.6;">
                            <p style="margin: 0 0 6px 0; color: #FFFFFF; font-weight: 700;">{{ $brandName }}</p>
                            <p style="margin: 0 0 10px 0;">{{ $brandAddress }}</p>
                            <p style="margin: 0 0 12px 0;">
                                📞 PBX: <strong style="color: #CBD5E1;">{{ $brandPhone }}</strong> &nbsp;|&nbsp; 
                                📱 WhatsApp: <strong style="color: #4ADE80;">{{ $brandWhatsapp }}</strong>
                            </p>
                            <p style="margin: 0; font-size: 11px; color: #64748B; border-top: 1px solid #1E293B; padding-top: 12px;">
                                &copy; {{ date('Y') }} {{ $brandName }}. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
