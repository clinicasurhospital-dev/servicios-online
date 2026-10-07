<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña — {{ $brandName }}</title>
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
                                            <span style="color: #FFFFFF; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">🔐 Seguridad de la Cuenta</span>
                                        </div>
                                        <h1 style="color: #FFFFFF; font-size: 24px; font-weight: 900; margin: 0 0 6px 0; letter-spacing: -0.5px;">{{ $brandName }}</h1>
                                        <p style="color: #DDE2FF; font-size: 13px; margin: 0; font-weight: 500;">Solicitud de Restablecimiento de Contraseña</p>
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
                                Hemos recibido una solicitud para restablecer la contraseña asociada a tu cuenta (<strong>{{ $userEmail }}</strong>) en la plataforma de <strong>{{ $brandName }}</strong>.
                            </p>
                            <p style="font-size: 14px; line-height: 1.6; margin: 0 0 28px 0; color: #475569;">
                                Para crear una nueva contraseña y recuperar el acceso a tu portal de citas y expedientes médicos, haz clic en el siguiente botón seguro:
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}" target="_blank" style="display: inline-block; background-color: #0700FF; color: #FFFFFF; font-size: 15px; font-weight: 700; text-decoration: none; padding: 16px 36px; border-radius: 12px; box-shadow: 0 4px 15px rgba(7, 0, 255, 0.35); text-align: center;">
                                            Restablecer mi Contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Important Expiration Notice Box -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-left: 4px solid #0700FF; border-radius: 8px; margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 6px 0; font-size: 13px; font-weight: 700; color: #1E293B;">
                                            ⏱️ Validez del enlace
                                        </p>
                                        <p style="margin: 0; font-size: 12px; line-height: 1.5; color: #64748B;">
                                            Este enlace de recuperación es de un solo uso y expirará automáticamente en <strong>{{ $expiresIn }} minutos</strong> por motivos de seguridad.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Direct Link Fallback -->
                            <p style="font-size: 12px; line-height: 1.5; color: #94A3B8; margin: 0 0 8px 0;">
                                Si tienes problemas al hacer clic en el botón, copia y pega la siguiente URL en tu navegador web:
                            </p>
                            <p style="font-size: 11px; word-break: break-all; margin: 0 0 25px 0;">
                                <a href="{{ $resetUrl }}" style="color: #0700FF; text-decoration: underline;">{{ $resetUrl }}</a>
                            </p>

                            <!-- Security Warning -->
                            <div style="border-top: 1px dashed #E2E8F0; padding-top: 20px;">
                                <p style="font-size: 12px; line-height: 1.5; color: #64748B; margin: 0;">
                                    🛡️ <strong>¿No solicitaste este cambio?</strong> Si no realizaste esta solicitud, puedes ignorar este mensaje de forma segura. Tu contraseña no cambiará y tu cuenta permanece protegida.
                                </p>
                            </div>
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
