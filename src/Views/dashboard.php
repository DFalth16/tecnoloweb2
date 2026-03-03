<?php
use EventCore\Helpers\SessionHelper;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>EventCore · Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@200;300;400;500;600&family=JetBrains+Mono:wght@300;400;500&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
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
canvas#bg{position:fixed;inset:0;z-index:0;pointer-events:none;opacity:.6}
.shell{display:flex;min-height:100vh;position:relative;z-index:1}
.sb{width:var(--sw);background:rgba(7,16,30,0.8);backdrop-filter:blur(30px);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:200}
.sb-logo{padding:24px 18px 16px;display:flex;align-items:center;gap:11px;text-decoration:none}
.sb-icon{width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,var(--cyan),var(--cyan2));display:flex;align-items:center;justify-content:center;font-size:16px;box-shadow:0 0 20px rgba(0,212,255,.3);animation:pulse-icon 3s infinite ease-in-out}
@keyframes pulse-icon{0%,100%{transform:scale(1);box-shadow:0 0 20px rgba(0,212,255,.3)}50%{transform:scale(1.05);box-shadow:0 0 35px rgba(0,212,255,.5)}}
.sb-name{font-size:18px;font-weight:200;letter-spacing:.03em;background:linear-gradient(135deg,#c8dfe8 30%,var(--cyan));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.sb-name b{font-weight:500}
.sb-lbl{font-size:9px;font-weight:500;letter-spacing:.16em;text-transform:uppercase;color:var(--t4);padding:14px 18px 5px}
nav{flex:1;overflow-y:auto;padding-bottom:10px}
.na{display:flex;align-items:center;gap:10px;padding:10px 16px;margin:2px 10px;border-radius:var(--r2);color:var(--t2);font-size:13px;text-decoration:none;transition:all .3s var(--ease)}
.na:hover{color:var(--t1);background:rgba(255,255,255,.04);transform:translateX(4px)}
.na.on{color:var(--cyan);background:var(--cyan-gs);position:relative}
.na.on::after{content:'';position:absolute;left:0;top:20%;bottom:20%;width:3px;background:var(--cyan);border-radius:0 4px 4px 0;box-shadow:0 0 10px var(--cyan)}
.main{margin-left:var(--sw);flex:1;display:flex;flex-direction:column;min-width:0}
.tb{position:sticky;top:0;z-index:100;height:62px;background:rgba(4,8,15,.7);backdrop-filter:blur(30px);border-bottom:1px solid var(--border);padding:0 30px;display:flex;align-items:center}
.tb-title{font-size:15px;font-weight:300;flex:1;letter-spacing:1px;text-transform:uppercase;color:var(--t2)}
.con{padding:30px 30px 60px;animation:fade-up 0.8s var(--ease)}
@keyframes fade-up{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.hero{position:relative;overflow:hidden;border-radius:var(--r);border:1px solid var(--border2);padding:40px;margin-bottom:24px;background:linear-gradient(135deg,rgba(0,212,255,.05) 0%,var(--c2) 50%,rgba(139,92,246,.04) 100%);box-shadow:0 10px 30px rgba(0,0,0,0.2)}
.hero::before{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(circle at center,rgba(0,212,255,0.03) 0%,transparent 70%);animation:rotate 20s linear infinite}
@keyframes rotate{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
.sc-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px}
.sc-card{background:var(--c1);border:1px solid var(--border);border-radius:var(--r);padding:22px 24px;position:relative;transition:all .4s var(--ease);overflow:hidden}
.sc-card:hover{transform:translateY(-8px) scale(1.02);border-color:var(--border2);background:var(--c2);box-shadow:0 15px 30px rgba(0,0,0,0.3)}
.sc-card::after{content:'';position:absolute;inset:0;background:linear-gradient(45deg,transparent,rgba(255,255,255,0.02),transparent);transform:translateX(-100%);transition:transform 0.6s var(--ease)}
.sc-card:hover::after{transform:translateX(100%)}
.sc-val{font-family:'JetBrains Mono',monospace;font-size:32px;font-weight:200;margin-top:10px;letter-spacing:-1px}
.card{background:var(--c1);border:1px solid var(--border);border-radius:var(--r);padding:24px;transition:all .4s var(--ease)}
.card:hover{border-color:rgba(255,255,255,0.08)}
.et{width:100%;border-collapse:collapse}
.et th{font-size:10px;text-transform:uppercase;color:var(--t4);text-align:left;padding:12px 15px;border-bottom:1px solid var(--border);letter-spacing:1px}
.et td{padding:15px;font-size:13.5px;color:var(--t2);border-bottom:1px solid var(--border);transition:all .2s}
.et tr:hover td{color:var(--t1);background:rgba(255,255,255,0.015)}
.sbg{padding:4px 10px;border-radius:6px;font-size:10px;text-transform:uppercase;font-weight:600;letter-spacing:0.5px}
.sbg.activo{background:var(--lime-g);color:var(--lime);box-shadow:0 0 10px var(--lime-g)}
.sbg.finalizado{background:rgba(255,255,255,0.05);color:var(--t3)}
.stat-item{padding:12px;border-radius:10px;background:rgba(255,255,255,0.015);border:1px solid var(--border);transition:all .3s var(--ease)}
.stat-item:hover{background:rgba(255,255,255,0.03);transform:scale(1.02)}
</style>
</head>
<body>
<canvas id="bg"></canvas>
<div class="shell">
  <aside class="sb">
    <a class="sb-logo" href="<?= BASE_URL ?>/dashboard">
      <div class="sb-icon">⚡</div>
      <span class="sb-name"><b>Event</b>Core</span>
    </a>
    <p class="sb-lbl">Principal</p>
    <nav>
      <a class="na on" href="<?= BASE_URL ?>/dashboard">Dashboard</a>
      <a class="na" href="<?= BASE_URL ?>/eventos?action=index">Eventos</a>
      <a class="na" href="<?= BASE_URL ?>/usuarios?action=index">Usuarios</a>
      <a class="na" href="<?= BASE_URL ?>/sedes?action=index">Sedes</a>
      <div style="margin:20px 18px;border-top:1px solid var(--border)"></div>
      <a class="na" href="<?= BASE_URL ?>/logout">Cerrar Sesión</a>
    </nav>
  </aside>
  <div class="main">
    <header class="tb"><div class="tb-title">Panel de Control</div></header>
    <div class="con">
      <div class="hero">
        <h1 style="font-weight:200;font-size:32px">Bienvenido de nuevo, <strong style="font-weight:500;color:var(--cyan);text-shadow:0 0 20px rgba(0,212,255,0.2)"><?= SessionHelper::get('user_nombres') ?></strong></h1>
        <p style="font-size:14px;color:var(--t2);margin-top:12px;max-width:500px">Gestiona tus eventos de manera eficiente con datos precisos y en tiempo real.</p>
      </div>
      <div class="sc-grid">
        <div class="sc-card" style="animation-delay:0.1s"><div style="font-size:10px;color:var(--t3);letter-spacing:1px">TOTAL EVENTOS</div><div class="sc-val"><?= $stats['total_eventos'] ?></div><div style="position:absolute;right:20px;top:20px;opacity:0.1;font-size:24px">📅</div></div>
        <div class="sc-card" style="animation-delay:0.2s"><div style="font-size:10px;color:var(--t3);letter-spacing:1px">EVENTOS ACTIVOS</div><div class="sc-val" style="color:var(--lime)"><?= $stats['eventos_activos'] ?></div><div style="position:absolute;right:20px;top:20px;opacity:0.1;font-size:24px">🔥</div></div>
        <div class="sc-card" style="animation-delay:0.3s"><div style="font-size:10px;color:var(--t3);letter-spacing:1px">PARTICIPANTES</div><div class="sc-val" style="color:var(--amber)"><?= $stats['total_asistentes'] ?></div><div style="position:absolute;right:20px;top:20px;opacity:0.1;font-size:24px">👥</div></div>
        <div class="sc-card" style="animation-delay:0.4s"><div style="font-size:10px;color:var(--t3);letter-spacing:1px">INGRESOS TOTALES</div><div class="sc-val" style="color:var(--violet)">$<?= number_format($stats['total_ingresos'],0,',','.') ?></div><div style="position:absolute;right:20px;top:20px;opacity:0.1;font-size:24px">💰</div></div>
      </div>
      <div style="display:grid;grid-template-columns:1.6fr 1fr;gap:20px">
        <div class="card" style="animation-delay:0.5s">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
             <h3 style="font-size:14px;font-weight:500;color:var(--t1)">Eventos Estratégicos</h3>
             <a href="<?= BASE_URL ?>/eventos" style="font-size:11px;color:var(--cyan);text-decoration:none">Ver todos →</a>
          </div>
          <table class="et">
            <thead><tr><th>Evento / Categoría</th><th>Fecha</th><th>Ocupación</th><th>Estado</th></tr></thead>
            <tbody>
              <?php foreach($eventos as $ev): 
                $p = $ev['cupo'] > 0 ? round($ev['inscritos']*100/$ev['cupo']) : 0;
              ?>
              <tr>
                <td>
                  <div style="font-weight:500;color:var(--t1)"><?= htmlspecialchars($ev['nombre']) ?></div>
                  <div style="font-size:11px;color:var(--t4);margin-top:2px"><?= htmlspecialchars($ev['cat']) ?></div>
                </td>
                <td><div style="font-family:'JetBrains Mono',monospace;font-size:12px"><?= date('d/m/Y', strtotime($ev['fecha'])) ?></div></td>
                <td>
                  <div style="display:flex;align-items:center;gap:8px">
                    <div style="width:60px;height:4px;background:rgba(255,255,255,0.05);border-radius:2px;overflow:hidden">
                      <div style="width:<?= $p ?>%;height:100%;background:var(--cyan);box-shadow:0 0 10px var(--cyan)"></div>
                    </div>
                    <span style="font-size:11px;font-family:'JetBrains Mono'"><?= $p ?>%</span>
                  </div>
                </td>
                <td><span class="sbg <?= strtolower($ev['estado']) ?>"><?= $ev['estado'] ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="card" style="animation-delay:0.6s">
          <h3 style="font-size:14px;font-weight:500;color:var(--t1);margin-bottom:20px">Flujo de Actividad</h3>
          <div style="display:flex;flex-direction:column;gap:12px">
            <?php foreach($actividad as $i => $a): ?>
            <div class="stat-item" style="animation:fade-up 0.5s var(--ease) backwards; animation-delay:<?= 0.7 + ($i*0.1) ?>s">
              <div style="display:flex;justify-content:space-between;align-items:center">
                <div style="font-weight:500;font-size:12.5px;color:var(--t1)"><?= htmlspecialchars($a['accion']) ?></div>
                <div style="font-family:'JetBrains Mono';font-size:10px;color:var(--t4)"><?= date('H:i', strtotime($a['fecha'])) ?></div>
              </div>
              <div style="color:var(--t2);font-size:11.5px;margin-top:4px"><?= htmlspecialchars($a['det']) ?></div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
const cnv=document.getElementById('bg'),c=cnv.getContext('2d');
let W,H,pts=[];
const resize=()=>{W=cnv.width=window.innerWidth;H=cnv.height=window.innerHeight;pts=[]};resize();window.addEventListener('resize',resize);
class Pt{constructor(){this.init()}init(){this.x=Math.random()*W;this.y=Math.random()*H;this.vx=(Math.random()-.5)*.3;this.vy=(Math.random()-.5)*.3;this.r=Math.random()*1.5;this.a=Math.random()*.4;this.col='0,212,255'}tick(){this.x+=this.vx;this.y+=this.vy;if(this.x<0||this.x>W||this.y<0||this.y>H)this.init()}draw(){c.beginPath();c.arc(this.x,this.y,this.r,0,Math.PI*2);c.fillStyle=`rgba(${this.col},${this.a})`;c.fill();
  pts.forEach(p=>{
    let d=Math.hypot(this.x-p.x,this.y-p.y);
    if(d<120){
      c.beginPath();c.moveTo(this.x,this.y);c.lineTo(p.x,p.y);
      c.strokeStyle=`rgba(0,180,255,${0.15*(1-d/120)})`;c.lineWidth=0.5;c.stroke();
    }
  });
}}
for(let i=0;i<60;i++)pts.push(new Pt());
(function frame(){c.clearRect(0,0,W,H);pts.forEach(p=>{p.tick();p.draw()});requestAnimationFrame(frame);})();
</script>
</body>
</html>
</body>
</html>
