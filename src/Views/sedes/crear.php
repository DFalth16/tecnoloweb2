<?php
use EventCore\Helpers\SessionHelper;
$e = function($str) { return htmlspecialchars($str ?? ''); };
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>EventCore · Crear Sede</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@200;300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --c0:#04080f;--c1:#080e1a;--c2:#0c1525;
  --border:rgba(255,255,255,0.055);--border2:rgba(255,255,255,0.1);
  --cyan:#00d4ff;--cyan2:#0099cc;--cyan-g:rgba(0,212,255,0.13);--cyan-gs:rgba(0,212,255,0.055);
  --t1:#deeaf2;--t2:#6e90a8;--t3:#2e4d62;--t4:#1b3044;
  --sw:252px;--r:14px;--r2:9px;
  --ease:cubic-bezier(0.16,1,0.3,1);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Outfit',sans-serif;font-weight:300;background:var(--c0);color:var(--t1);min-height:100vh;overflow-x:hidden}
.shell{display:flex;min-height:100vh;position:relative;z-index:1}
.sb{width:var(--sw);background:rgba(7,16,30,0.8);backdrop-filter:blur(30px);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:200}
.sb-logo{padding:24px 18px 16px;display:flex;align-items:center;gap:11px;text-decoration:none}
.sb-icon{width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,var(--cyan),var(--cyan2));display:flex;align-items:center;justify-content:center;font-size:16px;box-shadow:0 0 20px rgba(0,212,255,.3);animation:pulse-icon 3s infinite ease-in-out}
@keyframes pulse-icon{0%,100%{transform:scale(1);box-shadow:0 0 20px rgba(0,212,255,.3)}50%{transform:scale(1.05);box-shadow:0 0 35px rgba(0,212,255,.5)}}
.sb-name{font-size:18px;font-weight:200;letter-spacing:.03em;background:linear-gradient(135deg,#c8dfe8 30%,var(--cyan));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.sb-name b{font-weight:500}
.na{display:flex;align-items:center;gap:10px;padding:10px 16px;margin:2px 10px;border-radius:var(--r2);color:var(--t2);font-size:13px;text-decoration:none;transition:all .3s var(--ease)}
.na:hover{color:var(--t1);background:rgba(255,255,255,.04);transform:translateX(4px)}
.na.on{color:var(--cyan);background:var(--cyan-gs);position:relative}
.main{margin-left:var(--sw);flex:1;display:flex;flex-direction:column;min-width:0}
.con{padding:30px 30px 60px;animation:fade-up 0.8s var(--ease)}
@keyframes fade-up{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.card{background:var(--c1);border:1px solid var(--border);border-radius:var(--r);padding:40px;max-width:700px;margin:0 auto;box-shadow:0 20px 50px rgba(0,0,0,0.3)}
.form-group{margin-bottom:24px}
.form-group label{display:block;font-size:11px;color:var(--t4);margin-bottom:10px;text-transform:uppercase;letter-spacing:1.5px;font-weight:600}
.form-control{width:100%;background:var(--c2);border:1px solid var(--border);color:var(--t1);padding:14px 18px;border-radius:12px;outline:none;transition:all .3s var(--ease);font-family:inherit;font-size:14px}
.form-control:focus{border-color:var(--cyan);box-shadow:0 0 0 3px var(--cyan-gs);background:var(--c0)}
.btn{padding:14px 28px;border-radius:12px;font-size:14px;cursor:pointer;border:none;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;transition:all .3s var(--ease);font-weight:500;gap:10px}
.btn-p{background:linear-gradient(135deg,var(--cyan),var(--cyan2));color:#000;box-shadow:0 4px 15px rgba(0,212,255,0.2)}
.btn-p:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(0,212,255,0.4)}
.btn-g{background:rgba(255,255,255,0.03);color:var(--t1);border:1px solid var(--border)}
.btn-g:hover{background:rgba(255,255,255,0.06);transform:translateY(-2px)}
.flash-error{background:rgba(255,77,109,0.1);border:1px solid rgba(255,77,109,0.2);color:#ff4d6d;padding:18px;border-radius:12px;margin-bottom:30px;font-size:13.5px;animation:shake 0.4s var(--ease)}
@keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-5px)}75%{transform:translateX(5px)}}
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
      <a class="na" href="<?= BASE_URL ?>/dashboard">Dashboard</a>
      <a class="na" href="<?= BASE_URL ?>/eventos?action=index">Eventos</a>
      <a class="na" href="<?= BASE_URL ?>/usuarios?action=index">Usuarios</a>
      <a class="na on" href="<?= BASE_URL ?>/sedes?action=index">Sedes</a>
      <div style="margin:20px 18px;border-top:1px solid var(--border)"></div>
      <a class="na" href="<?= BASE_URL ?>/logout">Cerrar Sesión</a>
    </nav>
  </aside>
  <div class="main">
    <div class="con">
      <div style="margin-bottom:30px;display:flex;justify-content:space-between;align-items:center">
        <h2 style="font-weight:200;font-size:28px">Nueva <strong>Sede</strong></h2>
        <a href="<?= BASE_URL ?>/sedes" class="btn btn-g">← Volver</a>
      </div>

      <?php if (!empty($errors)): ?>
      <div class="flash-error">
        <?php foreach($errors as $err): ?><div><?= $e($err) ?></div><?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="card">
        <form method="POST">
          <div class="form-group">
            <label>Nombre de la Sede</label>
            <input type="text" name="nombre" class="form-control" required value="<?= $e($_POST['nombre'] ?? '') ?>" placeholder="Ej. Auditorio Central">
          </div>
          <div class="form-group">
            <label>Dirección Física</label>
            <input type="text" name="direccion" class="form-control" required value="<?= $e($_POST['direccion'] ?? '') ?>" placeholder="Ej. Calle 123, Zona Norte">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Ciudad</label>
              <input type="text" name="ciudad" class="form-control" required value="<?= $e($_POST['ciudad'] ?? '') ?>" placeholder="Ej. La Paz">
            </div>
            <div class="form-group">
              <label>País</label>
              <input type="text" name="pais" class="form-control" value="<?= $e($_POST['pais'] ?? 'Bolivia') ?>">
            </div>
          </div>
          <div class="form-group">
            <label>Capacidad Máxima (Aforo)</label>
            <input type="number" name="capacidad" class="form-control" required min="1" value="<?= $e($_POST['capacidad'] ?? '') ?>" placeholder="Ej. 500">
          </div>
          <div style="margin-top:30px;display:flex;gap:15px">
            <button type="submit" class="btn btn-p" style="flex:1">Registrar Sede</button>
            <a href="<?= BASE_URL ?>/sedes" class="btn btn-g">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
