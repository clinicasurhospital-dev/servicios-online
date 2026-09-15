<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesión Expirada — 419</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: #f8fafc; color: #0f172a; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem; }
        .card { max-width: 440px; width: 100%; text-align: center; background: white; border-radius: 1.5rem; border: 1px solid #e2e8f0; padding: 2.5rem 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }
        .icon-box { width: 4.5rem; height: 4.5rem; border-radius: 1.25rem; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem; }
        .badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; background: #fef3c7; color: #b45309; margin-bottom: 0.75rem; }
        h1 { font-size: 1.5rem; font-weight: 900; color: #0f172a; margin-bottom: 0.75rem; }
        p { font-size: 0.875rem; color: #64748b; line-height: 1.6; margin-bottom: 2rem; }
        .actions { display: flex; gap: 0.75rem; justify-content: center; }
        .btn { display: inline-block; padding: 0.75rem 1.5rem; border-radius: 0.875rem; font-size: 0.875rem; font-weight: 700; text-decoration: none; transition: all 0.2s; cursor: pointer; border: none; }
        .btn-primary { background: #0700FF; color: white; }
        .btn-primary:hover { opacity: 0.9; }
        .btn-secondary { background: #f1f5f9; color: #334155; }
        .btn-secondary:hover { background: #e2e8f0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-box">⏳</div>
        <span class="badge">Error 419</span>
        <h1>Sesión Expirada</h1>
        <p>Tu sesión o el formulario ha caducado por inactividad o seguridad. Por favor recarga la página para continuar con tu proceso.</p>
        <div class="actions">
            <button onclick="window.location.reload();" class="btn btn-primary">Recargar Página</button>
            <a href="/" class="btn btn-secondary">Ir al Inicio</a>
        </div>
    </div>
</body>
</html>
