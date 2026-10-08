<?php
// Configuración básica para obtener estado del sistema
$php_version = phpversion();
$server_software = $_SERVER['SERVER_SOFTWARE'] ?? 'IIS / Windows Server';
$server_ip = $_SERVER['LOCAL_ADDR'] ?? $_SERVER['SERVER_ADDR'] ?? '127.0.0.1';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios Web - Clínica Sur Hospital</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f4f7f6;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            max-width: 650px;
            width: 100%;
            overflow: hidden;
            border-top: 6px solid #0056b3;
        }
        .header {
            padding: 30px;
            text-align: center;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        .header h1 {
            color: #0056b3;
            font-size: 24px;
            margin-bottom: 8px;
        }
        .header p {
            color: #6c757d;
            font-size: 15px;
            font-weight: 500;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #e6f4ea;
            color: #137333;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 15px;
        }
        .status-dot {
            width: 10px;
            height: 10px;
            background-color: #34a853;
            border-radius: 50%;
            display: inline-block;
        }
        .content {
            padding: 30px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid #0056b3;
        }
        .info-box span {
            display: block;
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
        }
        .info-box strong {
            display: block;
            font-size: 15px;
            color: #212529;
            margin-top: 4px;
        }
        .notice {
            background-color: #e8f0fe;
            border-left: 4px solid #1a73e8;
            padding: 15px;
            border-radius: 4px;
            font-size: 14px;
            color: #174ea6;
            line-height: 1.5;
        }
        .footer {
            padding: 15px 30px;
            background: #f8f9fa;
            text-align: center;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Proyecto de Servicios Web</h1>
        <p>Clínica Sur Hospital</p>
        <div class="status-badge">
            <span class="status-dot"></span> Servidor de Producción Activo
        </div>
    </div>

    <div class="content">
        <div class="info-grid">
            <div class="info-box">
                <span>Servidor Web</span>
                <strong><?php echo htmlspecialchars($server_software); ?></strong>
            </div>
            <div class="info-box">
                <span>Versión de PHP</span>
                <strong>PHP <?php echo htmlspecialchars($php_version); ?></strong>
            </div>
            <div class="info-box">
                <span>Motor de BDD</span>
                <strong>MySQL Server 8.x</strong>
            </div>
            <div class="info-box">
                <span>Despliegue CI/CD</span>
                <strong>GitHub Actions (IIS-Runner)</strong>
            </div>
        </div>

        <div class="notice">
            <strong>Entorno Listo para Desarrollo:</strong> La infraestructura web y la canalización de integración continua (CI/CD) han sido verificadas. Cada confirmación (<code>git push</code>) en la rama principal sincronizará los archivos automáticamente en este servidor.
        </div>
    </div>

    <div class="footer">
        &copy; <?php echo date('Y'); ?> Clínica Sur Hospital — Todos los derechos reservados.
    </div>
</div>

</body>
</html>
