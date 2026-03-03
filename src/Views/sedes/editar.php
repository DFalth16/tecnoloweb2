<?php
use EventCore\Helpers\SessionHelper;
$e = function($str) { return htmlspecialchars($str ?? ''); };
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>EventCore · Editar Sede</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@200;300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --c0:#04080f;--c1:#080e1a;--c2:#0c1525;
  --border:rgba(255,255,255,0.055);
  --cyan:#00d4ff;--cyan2:#0099cc;--cyan-gs:rgba(0,212,255,0.055);
  --rose:#ff4d6d;
  --t1:#deeaf2;--t2:#6e90a8;--t4:#1b3044;
  --sw:252px;--r:14px;--r2:9px;
  --ease:cubic-bezier(0.16,1,0.3,1);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Outfit',sans-serif;font-weight:300;background:var(--c0);color:var(--t1);min-height:100vh;overflow-x:hidden}
.shell{display:flex;min-height:100vh}
.sb{width:var(--sw);background:rgba(7,16,30,0.8);backdrop-filter:blur(30px);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:200}
.sb-logo{padding:24px 18px 16px;display:flex;align-items:center;gap:11px;text-decoration:none}
.sb-icon{width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,var(--cyan),var(--cyan2));display:flex;align-items:center;justify-content:center;font-size:16px;box-shadow:0 0 20px rgba(0,212,255,.3);animation:pulse-icon 3s infinite ease-in-out}
@keyframes pulse-icon{0%,100%{transform:scale(1)}50%{transform:scale(1.05)}}
.sb-name{font-size:18px;font-weight:200;background:linear-gradient(135deg,#c8dfe8 30%,var(--cyan));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.sb-name b{font-weight:500}
.sb-lbl{font-size:10px;color:var(--t4);text-transform:uppercase;letter-spacing:1.5px;padding:12px 18px 6px;font-weight:600}
.na{display:flex;align-items:center;gap:10px;padding:10px 16px;margin:2px 10px;border-radius:var(--r2);color:var(--t2);font-size:13px;text-decoration:none;transition:all .3s var(--ease)}
.na:hover{color:var(--t1);background:rgba(255,255,255,.04);transform:translateX(4px)}
.na.on{color:var(--cyan);background:var(--cyan-gs);position:relative}
.na.on::after{content:'';position:absolute;left:0;top:20%;bottom:20%;width:3px;background:var(--cyan);border-radius:0 4px 4px 0}
.main{margin-left:var(--sw);flex:1}
.con{padding:30px 30px 60px;animation:fade-up 0.8s var(--ease)}
@keyframes fade-up{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.card{background:var(--c1);border:1px solid var(--border);border-radius:var(--r);padding:40px;max-width:720px;margin:0 auto;box-shadow:0 20px 50px rgba(0,0,0,0.3)}
.form-group{margin-bottom:22px}
.form-group label{display:block;font-size:11px;color:var(--t4);margin-bottom:9px;text-transform:uppercase;letter-spacing:1.5px;font-weight:600}
.form-control{width:100%;background:var(--c2);border:1px solid var(--border);color:var(--t1);padding:13px 16px;border-radius:11px;outline:none;transition:all .3s var(--ease);font-family:inherit;font-size:14px}
.form-control:focus{border-color:var(--cyan);box-shadow:0 0 0 3px var(--cyan-gs)}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.btn{padding:13px 26px;border-radius:11px;font-size:14px;cursor:pointer;border:none;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;transition:all .3s var(--ease);font-weight:500;gap:8px}
.btn-p{background:linear-gradient(135deg,var(--cyan),var(--cyan2));color:#000;box-shadow:0 4px 15px rgba(0,212,255,0.2)}
.btn-p:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(0,212,255,0.4)}
.btn-g{background:rgba(255,255,255,0.03);color:var(--t1);border:1px solid var(--border)}
.btn-g:hover{background:rgba(255,255,255,0.06);transform:translateY(-2px)}
.flash-error{background:rgba(255,77,109,0.1);border:1px solid rgba(255,77,109,0.2);color:var(--rose);padding:18px;border-radius:12px;margin-bottom:30px;font-size:13.5px}
</style>
</head>
<body>
<div class="shell">
  <aside class="sb">
    <a class="sb-logo" href="<?= BASE_URL ?>/dashboard">
      <div class="sb-icon">⚡</div>
      <span class="sb-name"><b>Event</b>Core</span>
    </a>
    <p class="sb-lbl">Principal</p>
    <nav>
      <a class="na" href="<?= BASE_URL ?>/dashboard">📊 Dashboard</a>
      <a class="na" href="<?= BASE_URL ?>/eventos?action=index">🗓 Eventos</a>
      <a class="na" href="<?= BASE_URL ?>/participantes?action=index">👥 Participantes</a>
      <a class="na" href="<?= BASE_URL ?>/usuarios?action=index">👤 Usuarios</a>
      <a class="na on" href="<?= BASE_URL ?>/sedes?action=index">📍 Sedes</a>
      <div style="margin:20px 18px;border-top:1px solid var(--border)"></div>
      <a class="na" href="<?= BASE_URL ?>/logout">🚪 Cerrar Sesión</a>
    </nav>
  </aside>
  <div class="main">
    <div class="con">
      <div style="margin-bottom:30px;display:flex;justify-content:space-between;align-items:center">
        <h2 style="font-weight:200;font-size:28px">Editar <strong>Sede</strong></h2>
        <a href="<?= BASE_URL ?>/sedes" class="btn btn-g">← Volver</a>
      </div>

      <?php if (!empty($errors)): ?>
      <div class="flash-error">
        <?php foreach($errors as $err): ?><div>• <?= $e($err) ?></div><?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="card">
        <form method="POST">
          <div class="form-group">
            <label>Nombre de la Sede</label>
            <input type="text" name="nombre" class="form-control" required value="<?= $e($sede['nombre']) ?>">
          </div>
          <div class="form-group">
            <label>Dirección Física</label>
            <input type="text" name="direccion" class="form-control" required value="<?= $e($sede['direccion']) ?>">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Ciudad</label>
              <input type="text" name="ciudad" class="form-control" required value="<?= $e($sede['ciudad']) ?>">
            </div>
            <div class="form-group">
              <label>País</label>
              <input type="text" name="pais" class="form-control" value="<?= $e($sede['pais']) ?>">
            </div>
          </div>
          <div class="form-group">
            <label>Capacidad Máxima (Aforo)</label>
            <input type="number" name="capacidad" class="form-control" required min="1" value="<?= $e($sede['capacidad']) ?>">
          </div>
          <div style="margin-top:30px;display:flex;gap:15px">
            <button type="submit" class="btn btn-p" style="flex:1">Guardar Cambios</button>
            <a href="<?= BASE_URL ?>/sedes" class="btn btn-g">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
