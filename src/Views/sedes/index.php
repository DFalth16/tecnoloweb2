<?php
$e = function($str) { return htmlspecialchars($str); };
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>EventCore · Sedes</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@200;300;400;500;600&family=JetBrains+Mono:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root{
  --c0:#04080f;--c1:#080e1a;--c2:#0c1525;
  --border:rgba(255,255,255,0.055);--border2:rgba(255,255,255,0.1);
  --cyan:#00d4ff;--cyan2:#0099cc;--cyan-g:rgba(0,212,255,0.13);--cyan-gs:rgba(0,212,255,0.055);
  --lime:#a3e635;--lime-g:rgba(163,230,53,0.11);
  --rose:#ff4d6d;--rose-g:rgba(255,77,109,0.1);
  --amber:#f59e0b;--amber-g:rgba(245,158,11,0.1);
  --violet:#8b5cf6;--violet-g:rgba(139,92,246,0.1);
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
.na.on::after{content:'';position:absolute;left:0;top:20%;bottom:20%;width:3px;background:var(--cyan);border-radius:0 4px 4px 0;box-shadow:0 0 10px var(--cyan)}
.main{margin-left:var(--sw);flex:1;display:flex;flex-direction:column;min-width:0}
.tb{position:sticky;top:0;z-index:100;height:62px;background:rgba(4,8,15,.7);backdrop-filter:blur(30px);border-bottom:1px solid var(--border);padding:0 30px;display:flex;align-items:center}
.con{padding:30px 30px 60px;animation:fade-up 0.8s var(--ease)}
@keyframes fade-up{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.card{background:var(--c1);border:1px solid var(--border);border-radius:var(--r);padding:24px;transition:all .4s var(--ease);position:relative;overflow:hidden}
.card:hover{border-color:rgba(255,255,255,0.08);box-shadow:0 10px 30px rgba(0,0,0,0.2)}
.et{width:100%;border-collapse:collapse}
.et th{font-size:10px;text-transform:uppercase;color:var(--t4);text-align:left;padding:12px 15px;border-bottom:1px solid var(--border);letter-spacing:1px}
.et td{padding:15px;font-size:13.5px;color:var(--t2);border-bottom:1px solid var(--border);transition:all .2s}
.et tr:hover td{color:var(--t1);background:rgba(255,255,255,0.015)}
.btn{padding:10px 20px;border-radius:10px;font-size:13px;cursor:pointer;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .3s var(--ease);font-weight:500}
.btn-p{background:linear-gradient(135deg,var(--cyan),var(--cyan2));color:#000;box-shadow:0 4px 15px rgba(0,212,255,0.2)}
.btn-p:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(0,212,255,0.4)}
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
    <header class="tb"><div class="tb-title">Gestión de Sedes</div></header>
    <div class="con">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:30px">
        <h2 style="font-weight:200;font-size:24px">Espacios <strong style="color:var(--cyan)">Registrados</strong></h2>
        <a href="<?= BASE_URL ?>/sedes?action=crear" class="btn btn-p">+ Nueva Sede</a>
      </div>

      <div class="card">
        <table class="et">
          <thead>
            <tr>
              <th>Nombre de la Sede</th>
              <th>Ciudad</th>
              <th>Dirección</th>
              <th>Capacidad</th>
              <th style="text-align:right">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($sedes as $s): ?>
            <tr>
              <td>
                <div style="font-weight:500;color:var(--t1)"><?= $e($s['nombre']) ?></div>
              </td>
              <td style="color:var(--t1);font-size:12.5px"><?= $e($s['ciudad']) ?></td>
              <td style="color:var(--t2);font-size:12.5px"><?= $e($s['direccion']) ?></td>
              <td>
                <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.03);padding:4px 10px;border-radius:6px;font-size:12px;color:var(--t2)">
                  <span style="color:var(--cyan)">👥</span> <?= $e($s['capacidad']) ?>
                </div>
              </td>
              <td style="text-align:right">
                <div style="display:flex;justify-content:flex-end;gap:12px">
                  <a href="<?= BASE_URL ?>/sedes?action=editar&id=<?= $s['id_sede'] ?>" style="color:var(--cyan);text-decoration:none;font-size:12px;font-weight:500">Editar</a>
                  <a href="<?= BASE_URL ?>/sedes?action=eliminar&id=<?= $s['id_sede'] ?>" onclick="return confirm('¿De verdad quieres eliminar esta sede?')" style="color:var(--rose);text-decoration:none;font-size:12px;font-weight:500">Eliminar</a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</body>
</html>
