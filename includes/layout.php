<?php
/**
 * EventCore — Layout compartido
 * Funciones para generar el HTML del layout premium dark-mode
 */

function layout_head(string $title, string $activePage = ''): void {
    require_once __DIR__ . '/../config.php';
    $user = currentUser();
    $db = getDB();
    
    // Stats para sidebar badges
    $stmtActivos = $db->query("SELECT COUNT(*) FROM eventos WHERE id_estado = 2");
    $eventosActivos = $stmtActivos->fetchColumn();
    
    $flash = getFlash();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>EventCore · <?= e($title) ?></title>
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
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{font-size:15px}
body{font-family:'Outfit',sans-serif;font-weight:300;background:var(--c0);color:var(--t1);min-height:100vh;overflow-x:hidden}

/* Sidebar */
.sb{width:var(--sw);background:linear-gradient(180deg,#07101e 0%,#050a14 100%);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:200;overflow:hidden}
.sb::before{content:'';position:absolute;top:0;left:0;right:0;height:1.5px;background:linear-gradient(90deg,transparent 0%,var(--cyan) 50%,transparent 100%);background-size:200%;animation:scanline 3s linear infinite}
@keyframes scanline{0%{background-position:200%}100%{background-position:-200%}}
.sb::after{content:'';position:absolute;top:0;left:0;right:0;height:80px;background:radial-gradient(ellipse at 50% 0%,rgba(0,212,255,.07),transparent 70%);pointer-events:none}

.sb-logo{padding:24px 18px 16px;display:flex;align-items:center;gap:11px;text-decoration:none;flex-shrink:0}
.sb-icon{width:34px;height:34px;border-radius:9px;flex-shrink:0;background:linear-gradient(135deg,var(--cyan),var(--cyan2));display:flex;align-items:center;justify-content:center;font-size:16px;box-shadow:0 0 18px rgba(0,212,255,.4);animation:iconpulse 3.5s ease-in-out infinite}
@keyframes iconpulse{0%,100%{box-shadow:0 0 18px rgba(0,212,255,.4)}50%{box-shadow:0 0 30px rgba(0,212,255,.7),0 0 50px rgba(0,212,255,.2)}}
.sb-name{font-size:18px;font-weight:200;letter-spacing:.03em;background:linear-gradient(135deg,#c8dfe8 30%,var(--cyan));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.sb-name b{font-weight:500}
.sb-lbl{font-size:9px;font-weight:500;letter-spacing:.16em;text-transform:uppercase;color:var(--t4);padding:14px 18px 5px}
nav{flex:1;overflow-y:auto;padding-bottom:10px}
nav::-webkit-scrollbar{width:0}

.na{display:flex;align-items:center;gap:10px;padding:9px 16px;margin:1px 8px;border-radius:var(--r2);color:var(--t2);font-size:13px;font-weight:300;text-decoration:none;position:relative;overflow:hidden;transition:color .22s,background .22s,padding-left .18s}
.na:hover{color:var(--t1);background:rgba(255,255,255,.028);padding-left:20px}
.na.on{color:var(--cyan);background:var(--cyan-gs)}
.pip{position:absolute;left:0;top:20%;bottom:20%;width:2px;background:var(--cyan);border-radius:0 2px 2px 0;box-shadow:0 0 8px var(--cyan);transform:scaleY(0);transition:transform .22s cubic-bezier(.34,1.56,.64,1)}
.na.on .pip{transform:scaleY(1)}
.na::after{content:'';position:absolute;top:0;left:-70%;width:40%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.05),transparent);transition:left .45s}
.na:hover::after{left:130%}
.na .i{font-size:14px;width:16px;text-align:center;flex-shrink:0;transition:filter .22s}
.na.on .i{filter:drop-shadow(0 0 5px rgba(0,212,255,.8))}
.nb{margin-left:auto;font-size:10px;font-weight:400;font-family:'JetBrains Mono',monospace;padding:2px 7px;border-radius:20px}
.nb-c{background:var(--cyan-g);color:var(--cyan);border:1px solid rgba(0,212,255,.18)}
.nb-a{background:var(--amber-g);color:var(--amber);border:1px solid rgba(245,158,11,.18)}
.sb-hr{height:1px;background:var(--border);margin:9px 14px}

.sb-user{padding:13px 14px;border-top:1px solid var(--border);display:flex;align-items:center;gap:10px;cursor:pointer;transition:background .2s;text-decoration:none}
.sb-user:hover{background:rgba(255,255,255,.022)}
.av{width:32px;height:32px;border-radius:50%;flex-shrink:0;background:linear-gradient(135deg,var(--violet),var(--cyan));display:flex;align-items:center;justify-content:center;font-size:11.5px;font-weight:500;box-shadow:0 0 10px rgba(139,92,246,.3);color:#fff}
.un{font-size:12.5px;font-weight:400;color:var(--t1)}
.ur{font-size:10.5px;color:var(--t3);margin-top:1px}

/* Main */
.shell{display:flex;min-height:100vh;position:relative;z-index:1}
.main{margin-left:var(--sw);flex:1;display:flex;flex-direction:column;min-width:0}
.tb{position:sticky;top:0;z-index:100;height:58px;background:rgba(4,8,15,.85);backdrop-filter:blur(22px);border-bottom:1px solid var(--border);padding:0 26px;display:flex;align-items:center;gap:12px}
.tb-title{font-size:15px;font-weight:300;flex:1;letter-spacing:.01em}
.tb-title span{display:block;font-size:10.5px;color:var(--t3);margin-top:1px;font-family:'JetBrains Mono',monospace;font-weight:300}
.con{padding:24px 26px 40px;flex:1}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:6px;padding:7px 15px;border-radius:8px;font-size:12.5px;font-weight:300;font-family:'Outfit',sans-serif;cursor:pointer;border:none;text-decoration:none;transition:all .2s;position:relative;overflow:hidden}
.btn::after{content:'';position:absolute;top:0;left:-100%;width:55%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.07),transparent);transition:left .4s}
.btn:hover::after{left:150%}
.btn-g{background:transparent;color:var(--t2);border:1px solid var(--border2)}
.btn-g:hover{color:var(--t1);border-color:rgba(255,255,255,.18);background:rgba(255,255,255,.03)}
.btn-p{background:linear-gradient(135deg,var(--cyan),var(--cyan2));color:#000;font-weight:500;box-shadow:0 0 16px rgba(0,212,255,.28)}
.btn-p:hover{transform:translateY(-1px);box-shadow:0 4px 26px rgba(0,212,255,.5)}
.btn-d{background:var(--rose-g);color:var(--rose);border:1px solid rgba(255,77,109,.18)}
.btn-d:hover{background:rgba(255,77,109,.18)}

/* Cards */
.card{background:var(--c1);border:1px solid var(--border);border-radius:var(--r);padding:20px;transition:border-color .3s}
.card:hover{border-color:rgba(255,255,255,.09)}
.chd{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px}
.ctitle{font-size:13.5px;font-weight:400;letter-spacing:.01em}
.csub{font-size:11px;color:var(--t3);margin-top:3px;font-weight:300}

/* Tables */
.et{width:100%;border-collapse:collapse}
.et th{font-size:9.5px;font-weight:500;letter-spacing:.11em;text-transform:uppercase;color:var(--t4);padding:0 11px 11px;text-align:left;border-bottom:1px solid var(--border)}
.et td{padding:12px 11px;font-size:12.5px;color:var(--t2);font-weight:300;border-bottom:1px solid var(--border);vertical-align:middle;transition:color .18s}
.et tr:last-child td{border-bottom:none}
.et tr{transition:background .15s}
.et tr:hover td{background:rgba(255,255,255,.016);color:var(--t1)}
.etn{color:var(--t1)!important;font-weight:400}
.etc{font-size:10.5px;color:var(--t4);margin-top:2px}

/* Status badges */
.sbg{display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:5px;font-size:11px;font-weight:300}
.sbg.activo,.sbg.Activo{background:rgba(163,230,53,.09);color:var(--lime)}
.sbg.finalizado,.sbg.Finalizado{background:rgba(255,255,255,.055);color:var(--t3)}
.sbg.cancelado,.sbg.Cancelado{background:var(--rose-g);color:var(--rose)}
.sbg.borrador,.sbg.Borrador{background:var(--amber-g);color:var(--amber)}
.sbg.agotado,.sbg.Agotado{background:var(--violet-g);color:var(--violet)}
.sbg-d{width:4px;height:4px;border-radius:50%;background:currentColor}

/* Occupation bar */
.obar{width:66px;height:3px;background:rgba(255,255,255,.07);border-radius:2px;overflow:hidden;display:inline-block;vertical-align:middle;margin-right:6px}
.ofill{height:100%;border-radius:2px;transition:width 1.3s cubic-bezier(.4,0,.2,1)}

/* Forms */
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:10px;font-weight:500;letter-spacing:.12em;text-transform:uppercase;color:var(--t3);margin-bottom:6px}
.form-control{width:100%;padding:10px 14px;border-radius:var(--r2);border:1px solid var(--border2);background:var(--c2);color:var(--t1);font-family:'Outfit',sans-serif;font-size:13px;font-weight:300;transition:border-color .2s,box-shadow .2s;outline:none}
.form-control:focus{border-color:var(--cyan);box-shadow:0 0 0 3px rgba(0,212,255,.1)}
.form-control::placeholder{color:var(--t4)}
select.form-control{cursor:pointer;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236e90a8' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center}
select.form-control option{background:var(--c1);color:var(--t1)}
textarea.form-control{min-height:80px;resize:vertical}

.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}
.form-check{display:flex;align-items:center;gap:8px;cursor:pointer}
.form-check input[type="checkbox"]{width:16px;height:16px;accent-color:var(--cyan);cursor:pointer}
.form-actions{display:flex;gap:10px;margin-top:20px;padding-top:16px;border-top:1px solid var(--border)}

/* Flash messages */
.flash{padding:12px 16px;border-radius:var(--r2);margin-bottom:16px;font-size:12.5px;font-weight:300;display:flex;align-items:center;gap:8px;animation:riseUp .4s ease}
.flash-success{background:var(--lime-g);color:var(--lime);border:1px solid rgba(163,230,53,.18)}
.flash-error{background:var(--rose-g);color:var(--rose);border:1px solid rgba(255,77,109,.18)}
.flash-warning{background:var(--amber-g);color:var(--amber);border:1px solid rgba(245,158,11,.18)}

/* Page header */
.page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
.page-header h2{font-size:20px;font-weight:300;letter-spacing:-.01em}
.page-header h2 strong{font-weight:500;color:var(--cyan)}

/* Empty state */
.empty-state{text-align:center;padding:60px 20px;color:var(--t3)}
.empty-state .icon{font-size:48px;margin-bottom:12px;opacity:.5}
.empty-state p{font-size:13px;margin-bottom:16px}

/* Animations */
@keyframes riseUp{from{opacity:0;transform:translateY(15px)}to{opacity:1;transform:translateY(0)}}

/* Scrollbar */
::-webkit-scrollbar{width:3px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:rgba(255,255,255,.07);border-radius:2px}

/* Search / filter bar */
.filter-bar{display:flex;gap:10px;margin-bottom:16px;align-items:center;flex-wrap:wrap}
.filter-bar .form-control{width:auto;min-width:160px;padding:7px 12px;font-size:12px}
.filter-bar .search-input{flex:1;min-width:200px}

/* User role tag */
.role-tag{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:10.5px;font-weight:400}
.role-admin{background:var(--cyan-g);color:var(--cyan);border:1px solid rgba(0,212,255,.18)}
.role-organizador{background:var(--violet-g);color:var(--violet);border:1px solid rgba(139,92,246,.18)}
.role-reportes{background:var(--amber-g);color:var(--amber);border:1px solid rgba(245,158,11,.18)}

/* Active/Inactive badge */
.badge-active{color:var(--lime);font-size:11px}
.badge-inactive{color:var(--rose);font-size:11px}

/* Modal / confirm */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);backdrop-filter:blur(4px);z-index:1000;align-items:center;justify-content:center}
.modal-overlay.show{display:flex}
.modal-box{background:var(--c1);border:1px solid var(--border2);border-radius:var(--r);padding:24px;max-width:400px;width:90%;animation:riseUp .3s ease}
.modal-box h3{font-size:16px;font-weight:400;margin-bottom:10px}
.modal-box p{font-size:13px;color:var(--t2);margin-bottom:20px}
</style>
</head>
<body>
<div class="shell">

<!-- SIDEBAR -->
<aside class="sb">
  <a class="sb-logo" href="<?= BASE_URL ?>/index.php">
    <div class="sb-icon">⚡</div>
    <span class="sb-name"><b>Event</b>Core</span>
  </a>

  <p class="sb-lbl">Principal</p>
  <nav>
    <a class="na <?= $activePage==='dashboard'?'on':'' ?>" href="<?= BASE_URL ?>/index.php">
      <span class="pip"></span><span class="i">⊞</span> Dashboard
      <span class="nb nb-c">Hoy</span>
    </a>
    <a class="na <?= $activePage==='eventos'?'on':'' ?>" href="<?= BASE_URL ?>/eventos/index.php">
      <span class="pip"></span><span class="i">◉</span> Eventos
      <span class="nb nb-c"><?= $eventosActivos ?></span>
    </a>
    <div class="sb-hr"></div>
    <p class="sb-lbl">Gestión</p>
    <a class="na <?= $activePage==='sedes'?'on':'' ?>" href="<?= BASE_URL ?>/sedes/index.php">
      <span class="pip"></span><span class="i">⌂</span> Sedes
    </a>
    <?php if (isAdmin()): ?>
    <div class="sb-hr"></div>
    <p class="sb-lbl">Sistema</p>
    <a class="na <?= $activePage==='usuarios'?'on':'' ?>" href="<?= BASE_URL ?>/usuarios/index.php">
      <span class="pip"></span><span class="i">◯</span> Usuarios
    </a>
    <?php endif; ?>
  </nav>

  <a class="sb-user" href="<?= BASE_URL ?>/logout.php" title="Cerrar sesión">
    <div class="av"><?= strtoupper(substr($user['nombres'],0,1) . substr($user['apellidos'],0,1)) ?></div>
    <div><div class="un"><?= e($user['nombres']) ?></div><div class="ur"><?= e($user['rol']) ?></div></div>
    <span style="margin-left:auto;font-size:13px;color:var(--t4)">⏻</span>
  </a>
</aside>

<!-- MAIN -->
<div class="main">
  <header class="tb">
    <div class="tb-title">
      <?= e($title) ?>
      <span id="clock">—</span>
    </div>
    <a href="<?= BASE_URL ?>/eventos/crear.php" class="btn btn-p">＋ Nuevo Evento</a>
  </header>

  <div class="con">
    <?php if ($flash): ?>
    <div class="flash flash-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>
<?php
}

function layout_footer(): void {
?>
  </div><!-- /con -->
</div><!-- /main -->
</div><!-- /shell -->

<script>
/* Clock */
(function tick(){
  const n=new Date(),p=x=>String(x).padStart(2,'0');
  const el=document.getElementById('clock');
  if(el) el.textContent=`${n.getFullYear()}-${p(n.getMonth()+1)}-${p(n.getDate())}  ${p(n.getHours())}:${p(n.getMinutes())}:${p(n.getSeconds())}`;
  setTimeout(tick,1000);
})();

/* Nav hover */
document.querySelectorAll('.na').forEach(el=>{
  if(!el.classList.contains('on')){
    el.addEventListener('mouseenter',()=>el.style.paddingLeft='22px');
    el.addEventListener('mouseleave',()=>el.style.paddingLeft='');
  }
});

/* Progress bars */
setTimeout(()=>document.querySelectorAll('.ofill').forEach(el=>el.style.width=el.dataset.w+'%'),300);
</script>
</body>
</html>
<?php
}
