<?php
/**
 * EventCore — Login
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si ya está logueado, redirigir
if (isset($_SESSION['user_id'])) {
    header('Location: /drop/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/config.php';
    
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Ingrese email y contraseña.';
    } else {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT u.*, r.nombre_rol 
            FROM usuarios_admin u 
            JOIN roles r ON u.id_rol = r.id_rol 
            WHERE u.email = ? AND u.activo = 1
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']        = $user['id_usuario'];
            $_SESSION['user_nombres']   = $user['nombres'];
            $_SESSION['user_apellidos'] = $user['apellidos'];
            $_SESSION['user_email']     = $user['email'];
            $_SESSION['user_rol']       = $user['nombre_rol'];
            $_SESSION['user_id_rol']    = $user['id_rol'];
            
            header('Location: /drop/index.php');
            exit;
        } else {
            $error = 'Credenciales incorrectas o cuenta inactiva.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>EventCore · Iniciar Sesión</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@200;300;400;500;600&family=JetBrains+Mono:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root{
  --c0:#04080f;--c1:#080e1a;--c2:#0c1525;
  --border:rgba(255,255,255,0.055);--border2:rgba(255,255,255,0.1);
  --cyan:#00d4ff;--cyan2:#0099cc;--cyan-g:rgba(0,212,255,0.13);
  --rose:#ff4d6d;--rose-g:rgba(255,77,109,0.1);
  --violet:#8b5cf6;
  --t1:#deeaf2;--t2:#6e90a8;--t3:#2e4d62;--t4:#1b3044;
  --r:14px;--r2:9px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{font-size:15px}
body{font-family:'Outfit',sans-serif;font-weight:300;background:var(--c0);color:var(--t1);min-height:100vh;display:flex;align-items:center;justify-content:center;overflow:hidden}

canvas#bg{position:fixed;inset:0;z-index:0;pointer-events:none;opacity:.5}

.login-wrapper{position:relative;z-index:1;width:100%;max-width:400px;padding:20px}

.login-card{
  background:var(--c1);border:1px solid var(--border2);border-radius:var(--r);padding:36px 32px;
  position:relative;overflow:hidden;
  animation:riseUp .5s ease both;
}
.login-card::before{
  content:'';position:absolute;inset:-1px;border-radius:calc(var(--r)+1px);z-index:0;
  background:conic-gradient(from 0deg,transparent 70%,rgba(0,212,255,.4) 85%,transparent 100%);
  animation:rotateBorder 6s linear infinite;
  -webkit-mask:linear-gradient(#fff,#fff) content-box,linear-gradient(#fff,#fff);
  -webkit-mask-composite:xor;mask-composite:exclude;padding:1px;
}
@keyframes rotateBorder{to{transform:rotate(360deg)}}

.login-inner{position:relative;z-index:1}

.logo{display:flex;align-items:center;gap:11px;margin-bottom:28px;justify-content:center}
.logo-icon{width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,var(--cyan),var(--cyan2));display:flex;align-items:center;justify-content:center;font-size:20px;box-shadow:0 0 22px rgba(0,212,255,.4);animation:iconpulse 3.5s ease-in-out infinite}
@keyframes iconpulse{0%,100%{box-shadow:0 0 18px rgba(0,212,255,.4)}50%{box-shadow:0 0 30px rgba(0,212,255,.7),0 0 50px rgba(0,212,255,.2)}}
.logo-text{font-size:22px;font-weight:200;letter-spacing:.03em;background:linear-gradient(135deg,#c8dfe8 30%,var(--cyan));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.logo-text b{font-weight:500}

.login-title{text-align:center;font-size:14px;color:var(--t2);margin-bottom:24px;font-weight:300}

.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:10px;font-weight:500;letter-spacing:.12em;text-transform:uppercase;color:var(--t3);margin-bottom:6px}
.form-control{width:100%;padding:11px 14px;border-radius:var(--r2);border:1px solid var(--border2);background:var(--c2);color:var(--t1);font-family:'Outfit',sans-serif;font-size:13px;font-weight:300;transition:border-color .2s,box-shadow .2s;outline:none}
.form-control:focus{border-color:var(--cyan);box-shadow:0 0 0 3px rgba(0,212,255,.1)}
.form-control::placeholder{color:var(--t4)}

.btn-login{width:100%;padding:12px;border:none;border-radius:var(--r2);background:linear-gradient(135deg,var(--cyan),var(--cyan2));color:#000;font-family:'Outfit',sans-serif;font-size:14px;font-weight:500;cursor:pointer;transition:all .2s;box-shadow:0 0 16px rgba(0,212,255,.28);margin-top:6px}
.btn-login:hover{transform:translateY(-2px);box-shadow:0 4px 26px rgba(0,212,255,.5)}

.error-msg{background:var(--rose-g);color:var(--rose);border:1px solid rgba(255,77,109,.18);padding:10px 14px;border-radius:var(--r2);font-size:12px;margin-bottom:16px;text-align:center}

.login-footer{text-align:center;margin-top:18px;font-size:11px;color:var(--t4)}

@keyframes riseUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
</style>
</head>
<body>
<canvas id="bg"></canvas>

<div class="login-wrapper">
  <div class="login-card">
    <div class="login-inner">
      <div class="logo">
        <div class="logo-icon">⚡</div>
        <span class="logo-text"><b>Event</b>Core</span>
      </div>
      
      <p class="login-title">Gestión interna de eventos</p>
      
      <?php if ($error): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      
      <form method="POST" action="">
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" class="form-control" placeholder="tu@email.com" 
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
        </div>
        <div class="form-group">
          <label>Contraseña</label>
          <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn-login">Iniciar Sesión</button>
      </form>
      
      <div class="login-footer">Sistema de gestión interna · EventCore</div>
    </div>
  </div>
</div>

<script>
/* Particle Network */
const cnv=document.getElementById('bg'),c=cnv.getContext('2d');
let W,H,pts=[];
const resize=()=>{W=cnv.width=window.innerWidth;H=cnv.height=window.innerHeight};
resize();window.addEventListener('resize',resize);
class Pt{
  constructor(){this.init()}
  init(){this.x=Math.random()*W;this.y=Math.random()*H;this.vx=(Math.random()-.5)*.22;this.vy=(Math.random()-.5)*.22;this.r=Math.random()*1.1+.3;this.a=Math.random()*.35+.08;this.col=Math.random()>.5?'0,212,255':'139,92,246';this.life=0;this.max=Math.random()*350+180}
  tick(){this.x+=this.vx;this.y+=this.vy;this.life++;if(this.life>this.max||this.x<0||this.x>W||this.y<0||this.y>H)this.init()}
  draw(){c.beginPath();c.arc(this.x,this.y,this.r,0,Math.PI*2);c.fillStyle=`rgba(${this.col},${this.a})`;c.fill()}
}
for(let i=0;i<60;i++)pts.push(new Pt());
(function frame(){
  c.clearRect(0,0,W,H);
  for(let i=0;i<pts.length;i++){for(let j=i+1;j<pts.length;j++){const d=Math.hypot(pts[i].x-pts[j].x,pts[i].y-pts[j].y);if(d<95){c.beginPath();c.moveTo(pts[i].x,pts[i].y);c.lineTo(pts[j].x,pts[j].y);c.strokeStyle=`rgba(0,212,255,${.06*(1-d/95)})`;c.lineWidth=.5;c.stroke()}}}
  pts.forEach(p=>{p.tick();p.draw()});
  requestAnimationFrame(frame);
})();
</script>
</body>
</html>
