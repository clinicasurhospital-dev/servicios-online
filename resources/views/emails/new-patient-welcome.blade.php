<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido(a) a {{ $brandName }} — Activación de Cuenta</title>
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
                                        <div style="display: inline-block; background: rgba(255,255,255,0.18); padding: 8px 18px; border-radius: 50px; margin-bottom: 12px; border: 1px solid rgba(255,255,255,0.25);">
                                            <span style="color: #FFFFFF; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">🏥 Portal de Pacientes SurHospital</span>
                                        </div>
                                        <h1 style="color: #FFFFFF; font-size: 24px; font-weight: 900; margin: 0 0 6px 0; letter-spacing: -0.5px;">{{ $brandName }}</h1>
                                        <p style="color: #DDE2FF; font-size: 13px; margin: 0; font-weight: 500;">¡Tu cuenta de paciente ha sido registrada exitosamente!</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 35px 30px;">
                            <p style="font-size: 16px; line-height: 1.6; margin: 0 0 16px 0; color: #1E293B;">
                                Estimado(a) <strong>{{ $userName }}</strong>,
                            </p>
                            <p style="font-size: 14px; line-height: 1.6; margin: 0 0 20px 0; color: #475569;">
                                Te damos la más cordial bienvenida a <strong>{{ $brandName }}</strong>. Se ha creado tu cuenta en nuestro sistema para que puedas consultar tus citas médicas, descargar recetas y acceder a tu historial clínico en línea.
                            </p>

                            <!-- Credentials Box -->
                            <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 14px; padding: 20px; margin-bottom: 24px;">
                                <h3 style="font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #0700FF; margin: 0 0 12px 0;">
                                    📋 Datos de Acceso Inicial
                                </h3>
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td style="padding: 6px 0; font-size: 13px; color: #64748B; width: 140px;">Correo electrónico:</td>
                                        <td style="padding: 6px 0; font-size: 13px; font-weight: 700; color: #0F172A;">{{ $userEmail }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; font-size: 13px; color: #64748B;">Clave genérica inicial:</td>
                                        <td style="padding: 6px 0; font-size: 14px; font-weight: 800; font-family: monospace; color: #0700FF; background-color: #EEF2FF; padding: 4px 8px; border-radius: 6px; display: inline-block;">
                                            {{ $genericPassword }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Important Security Notice -->
                            <div style="background-color: #FEF3C7; border-left: 4px solid #F59E0B; padding: 14px 16px; border-radius: 8px; margin-bottom: 28px;">
                                <p style="font-size: 13px; line-height: 1.5; margin: 0; color: #92400E; font-weight: 600;">
                                    ⚠️ <strong>Paso Requerido por Seguridad Médica:</strong> Para proteger la confidencialidad de tu información, debes generar tu propia contraseña personal definitiva haciendo clic en el siguiente enlace.
                                </p>
                            </div>

                            <!-- CTA Button -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}" target="_blank" style="display: inline-block; background-color: #0700FF; color: #FFFFFF; font-size: 15px; font-weight: 800; text-decoration: none; padding: 16px 36px; border-radius: 12px; box-shadow: 0 4px 15px rgba(7, 0, 255, 0.35); text-align: center;">
                                            🔐 Generar Mi Nueva Contraseña Personal
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size: 12px; line-height: 1.5; color: #94A3B8; margin: 0 0 16px 0; text-align: center;">
                                Este enlace seguro es válido durante los próximos <strong>{{ $expiresIn }} minutos</strong>.
                            </p>

                            <hr style="border: none; border-top: 1px solid #E2E8F0; margin: 24px 0;" />

                            <p style="font-size: 12px; line-height: 1.5; color: #64748B; margin: 0 0 8px 0;">
                                Si tienes problemas con el botón superior, copia y pega el siguiente enlace directo en tu navegador web:
                            </p>
                            <p style="font-size: 11px; line-height: 1.4; color: #0700FF; word-break: break-all; margin: 0 0 20px 0; font-family: monospace;">
                                <a href="{{ $resetUrl }}" style="color: #0700FF; text-decoration: underline;">{{ $resetUrl }}</a>
                            </p>

                            <!-- Assistance Box -->
                            <div style="background-color: #F1F5F9; border-radius: 12px; padding: 16px; margin-top: 24px;">
                                <p style="font-size: 12px; font-weight: 700; color: #334155; margin: 0 0 4px 0;">
                                    ¿Necesitas asistencia o deseas agendar una consulta médica?
                                </p>
                                <p style="font-size: 12px; color: #64748B; margin: 0;">
                                    Llámanos al <strong>{{ $brandPhone }}</strong> o escríbenos a nuestro WhatsApp <strong>{{ $brandWhatsapp }}</strong>.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #F8FAFC; padding: 25px 30px; text-align: center; border-top: 1px solid #E2E8F0;">
                            <p style="font-size: 12px; color: #64748B; margin: 0 0 6px 0; font-weight: 600;">
                                {{ $brandName }} — Sistema de Gestión Médica
                            </p>
                            <p style="font-size: 11px; color: #94A3B8; margin: 0 0 10px 0;">
                                {{ $brandAddress }}
                            </p>
                            <p style="font-size: 10px; color: #CBD5E1; margin: 0;">
                                Este es un mensaje automático del sistema de gestión hospitalaria. Por favor no respondas directamente a este correo.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
