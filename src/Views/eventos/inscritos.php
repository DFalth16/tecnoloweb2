<?php
use EventCore\Helpers\SessionHelper;
$e = function($str) { return htmlspecialchars($str ?? ''); };
$pct = $evento['cupo_maximo'] > 0 ? round(($evento['total_inscritos'] / $evento['cupo_maximo']) * 100) : 0;
$estadoColor = ['Pendiente'=>'--amber','Confirmada'=>'--lime','Cancelada'=>'--rose','Lista de espera'=>'--violet'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>EventCore · Inscritos — <?= $e($evento['titulo']) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@200;300;400;500;600&family=JetBrains+Mono:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root{
  --c0:#04080f;--c1:#080e1a;--c2:#0c1525;
  --border:rgba(255,255,255,0.055);--border2:rgba(255,255,255,0.1);
  --cyan:#00d4ff;--cyan2:#0099cc;--cyan-gs:rgba(0,212,255,0.055);--cyan-g:rgba(0,212,255,0.13);
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
.na.on::after{content:'';position:absolute;left:0;top:20%;bottom:20%;width:3px;background:var(--cyan);border-radius:0 4px 4px 0;box-shadow:0 0 10px var(--cyan)}
.main{margin-left:var(--sw);flex:1;display:flex;flex-direction:column;min-width:0}
.tb{position:sticky;top:0;z-index:100;height:62px;background:rgba(4,8,15,.7);backdrop-filter:blur(30px);border-bottom:1px solid var(--border);padding:0 30px;display:flex;align-items:center}
.con{padding:30px 30px 60px;animation:fade-up 0.8s var(--ease)}
@keyframes fade-up{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.card{background:var(--c1);border:1px solid var(--border);border-radius:var(--r);padding:24px;transition:border .3s}
.card:hover{border-color:rgba(255,255,255,0.08)}
.et{width:100%;border-collapse:collapse}
.et th{font-size:10px;text-transform:uppercase;color:var(--t4);text-align:left;padding:12px 15px;border-bottom:1px solid var(--border);letter-spacing:1px}
.et td{padding:14px 15px;font-size:13.5px;color:var(--t2);border-bottom:1px solid var(--border);transition:all .2s}
.et tr:last-child td{border-bottom:none}
.et tr:hover td{color:var(--t1);background:rgba(255,255,255,0.015)}
.btn{padding:10px 20px;border-radius:10px;font-size:13px;cursor:pointer;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .3s var(--ease);font-weight:500}
.btn-p{background:linear-gradient(135deg,var(--cyan),var(--cyan2));color:#000;box-shadow:0 4px 15px rgba(0,212,255,0.2)}
.btn-p:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(0,212,255,0.4)}
.btn-g{background:rgba(255,255,255,0.03);color:var(--t1);border:1px solid var(--border)}
.btn-g:hover{background:rgba(255,255,255,0.06);transform:translateY(-2px)}
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.stat-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px}
.stat{background:var(--c1);border:1px solid var(--border);border-radius:var(--r);padding:20px 24px;display:flex;flex-direction:column;gap:6px}
.stat-val{font-size:32px;font-weight:200;line-height:1}
.stat-lbl{font-size:11px;color:var(--t3);text-transform:uppercase;letter-spacing:1px}
/* Aforo bar */
.aforo-bar{height:8px;border-radius:4px;background:rgba(255,255,255,.06);overflow:hidden;margin-top:10px}
.aforo-fill{height:100%;border-radius:4px;transition:width 1s var(--ease)}
/* Inscribir form */
.ins-form{display:flex;gap:12px;align-items:flex-end}
.form-control{background:var(--c2);border:1px solid var(--border);color:var(--t1);padding:11px 14px;border-radius:10px;outline:none;font-family:inherit;font-size:13.5px;transition:border .3s;flex:1}
.form-control:focus{border-color:var(--cyan)}
.flash-s{background:var(--lime-g);border:1px solid rgba(163,230,53,0.25);color:var(--lime);padding:14px 18px;border-radius:10px;margin-bottom:24px;font-size:13px}
.flash-e{background:var(--rose-g);border:1px solid rgba(255,77,109,0.25);color:var(--rose);padding:14px 18px;border-radius:10px;margin-bottom:24px;font-size:13px}
.form-errors{background:var(--rose-g);border:1px solid rgba(255,77,109,0.25);color:var(--rose);padding:14px 18px;border-radius:10px;margin-bottom:18px;font-size:13px}
.avatar{width:32px;height:32px;border-radius:50%;background:var(--cyan-g);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:var(--cyan);border:1px solid var(--border2);flex-shrink:0}
.empty{text-align:center;padding:50px 20px;color:var(--t3)}
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
      <a class="na on" href="<?= BASE_URL ?>/eventos?action=index">🗓 Eventos</a>
      <a class="na" href="<?= BASE_URL ?>/participantes?action=index">👥 Participantes</a>
      <a class="na" href="<?= BASE_URL ?>/usuarios?action=index">👤 Usuarios</a>
      <a class="na" href="<?= BASE_URL ?>/sedes?action=index">📍 Sedes</a>
      <div style="margin:20px 18px;border-top:1px solid var(--border)"></div>
      <a class="na" href="<?= BASE_URL ?>/logout">🚪 Cerrar Sesión</a>
    </nav>
  </aside>
  <div class="main">
    <header class="tb">
      <div style="font-size:15px;font-weight:500">Inscritos — <?= $e($evento['titulo']) ?></div>
    </header>
    <div class="con">
      <?php $flash = SessionHelper::getFlash(); if ($flash): ?>
      <div class="<?= $flash['type']==='success'?'flash-s':'flash-e' ?>"><?= $e($flash['message']) ?></div>
      <?php endif; ?>

      <!-- Header -->
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px">
        <div>
          <h2 style="font-weight:200;font-size:24px">Lista de <strong style="color:var(--cyan)">Inscritos</strong></h2>
          <div style="font-size:13px;color:var(--t3);margin-top:4px">📍 <?= $e($evento['sede']) ?> &nbsp;·&nbsp; 🗓 <?= date('d/m/Y', strtotime($evento['fecha_inicio'])) ?></div>
        </div>
        <a href="<?= BASE_URL ?>/eventos?action=index" class="btn btn-g">← Volver a Eventos</a>
      </div>

      <!-- Stats -->
      <div class="stat-cards">
        <div class="stat">
          <div class="stat-val" style="color:var(--cyan)"><?= (int)$evento['total_inscritos'] ?></div>
          <div class="stat-lbl">Inscritos</div>
          <div class="aforo-bar">
            <div class="aforo-fill" style="width:<?= $pct ?>%;background:<?= $pct >= 90 ? 'var(--rose)' : ($pct >= 70 ? 'var(--amber)' : 'var(--cyan)') ?>"></div>
          </div>
        </div>
        <div class="stat">
          <div class="stat-val" style="color:var(--lime)"><?= (int)$evento['cupo_maximo'] ?></div>
          <div class="stat-lbl">Cupo Máximo</div>
        </div>
        <div class="stat">
          <div class="stat-val" style="color:<?= $pct >= 100 ? 'var(--rose)' : 'var(--amber)' ?>"><?= max(0, (int)$evento['cupo_maximo'] - (int)$evento['total_inscritos']) ?></div>
          <div class="stat-lbl">Plazas Disponibles</div>
        </div>
      </div>

      <!-- Formulario de inscripción manual -->
      <div class="card" style="margin-bottom:24px">
        <div style="font-size:14px;font-weight:500;margin-bottom:16px;color:var(--t1)">✍️ Inscribir Participante Manualmente</div>
        <p style="font-size:12.5px;color:var(--t3);margin-bottom:16px">Ingrese el email de un participante ya registrado en el sistema para inscribirlo a este evento.</p>

        <?php if (!empty($errors)): ?>
        <div class="form-errors">
          <?php foreach($errors as $err): ?><div>• <?= $e($err) ?></div><?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ((int)$evento['total_inscritos'] < (int)$evento['cupo_maximo']): ?>
        <form method="POST" class="ins-form">
          <div style="flex:1">
            <label style="font-size:11px;color:var(--t4);text-transform:uppercase;letter-spacing:1.2px;font-weight:600;display:block;margin-bottom:8px">Email del Participante</label>
            <input type="email" name="email_participante" class="form-control" placeholder="Ej. pedro@email.com" value="<?= $e($_POST['email_participante'] ?? '') ?>" required>
          </div>
          <button type="submit" class="btn btn-p" style="height:44px">Inscribir</button>
        </form>
        <div style="margin-top:12px;font-size:12px;color:var(--t3)">
          ¿El participante no está registrado? <a href="<?= BASE_URL ?>/participantes?action=crear" style="color:var(--cyan)">Crear participante →</a>
        </div>
        <?php else: ?>
        <div style="color:var(--rose);font-size:13px;padding:14px;background:var(--rose-g);border-radius:10px;border:1px solid rgba(255,77,109,.2)">
          🚫 Este evento ha alcanzado su aforo máximo de <?= (int)$evento['cupo_maximo'] ?> personas.
        </div>
        <?php endif; ?>
      </div>

      <!-- Tabla de inscritos -->
      <div class="card">
        <div style="font-size:14px;font-weight:500;margin-bottom:16px;color:var(--t1)">Listado de Participantes Inscritos</div>
        <?php if (empty($inscritos)): ?>
        <div class="empty">
          <div style="font-size:40px;margin-bottom:12px;opacity:.4">📋</div>
          <div>No hay participantes inscritos en este evento aún.</div>
        </div>
        <?php else: ?>
        <table class="et">
          <thead>
            <tr>
              <th>Participante</th>
              <th>Email</th>
              <th>Teléfono</th>
              <th>Código</th>
              <th>Estado</th>
              <th>Fecha Inscripción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($inscritos as $ins):
              $initials = strtoupper(mb_substr($ins['nombres'],0,1) . mb_substr($ins['apellidos'],0,1));
              $estNombre = $ins['estado_inscripcion'];
              $estColor  = $estadoColor[$estNombre] ?? '--t2';
            ?>
            <tr>
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <div class="avatar"><?= $e($initials) ?></div>
                  <div>
                    <div style="font-weight:500;color:var(--t1)"><?= $e($ins['nombres'].' '.$ins['apellidos']) ?></div>
                  </div>
                </div>
              </td>
              <td style="font-family:'JetBrains Mono',monospace;font-size:12px"><?= $e($ins['email']) ?></td>
              <td style="font-size:12.5px"><?= $e($ins['telefono'] ?: '—') ?></td>
              <td style="font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--cyan)"><?= $e($ins['codigo_inscripcion']) ?></td>
              <td>
                <span class="badge" style="color:var(<?= $estColor ?>);background:rgba(0,0,0,.15);border:1px solid rgba(255,255,255,.05)">
                  <?= $e($estNombre) ?>
                </span>
              </td>
              <td style="font-size:12px"><?= date('d/m/Y H:i', strtotime($ins['fecha_inscripcion'])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>
