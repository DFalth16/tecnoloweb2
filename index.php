<?php
/**
 * EventCore — Dashboard Principal
 */
require_once __DIR__ . '/config.php';
requireLogin();

$db = getDB();
$user = currentUser();

// ── Stats reales ──
$totalEventos     = $db->query("SELECT COUNT(*) FROM eventos")->fetchColumn();
$eventosActivos   = $db->query("SELECT COUNT(*) FROM eventos WHERE id_estado = 2")->fetchColumn();
$eventosCancelados= $db->query("SELECT COUNT(*) FROM eventos WHERE id_estado = 4")->fetchColumn();
$eventosFinalizados=$db->query("SELECT COUNT(*) FROM eventos WHERE id_estado = 5")->fetchColumn();
$totalAsistentes  = $db->query("SELECT COUNT(*) FROM participantes")->fetchColumn();

$stmtIngresos = $db->query("SELECT COALESCE(SUM(p.monto),0) FROM pagos p JOIN estados_pago ep ON p.id_estado_pago = ep.id_estado_pago WHERE ep.nombre = 'Confirmado'");
$totalIngresos = $stmtIngresos->fetchColumn();

$nuevosHoy = $db->query("SELECT COUNT(*) FROM inscripciones WHERE DATE(fecha_inscripcion) = CURDATE()")->fetchColumn();

$stats = [
    'total_eventos'        => $totalEventos,
    'eventos_activos'      => $eventosActivos,
    'total_asistentes'     => $totalAsistentes,
    'total_ingresos'       => $totalIngresos,
    'eventos_cancelados'   => $eventosCancelados,
    'eventos_finalizados'  => $eventosFinalizados,
    'nuevos_inscritos_hoy' => $nuevosHoy,
];

// ── Eventos recientes ──
$eventos = $db->query("
    SELECT e.titulo AS nombre, e.fecha_inicio AS fecha, 
           (SELECT COUNT(*) FROM inscripciones i WHERE i.id_evento = e.id_evento) AS inscritos,
           e.cupo_maximo AS cupo,
           ee.nombre AS estado,
           ce.nombre AS cat
    FROM eventos e
    JOIN estados_evento ee ON e.id_estado = ee.id_estado
    JOIN categorias_evento ce ON e.id_categoria = ce.id_categoria
    ORDER BY e.fecha_inicio DESC
    LIMIT 5
")->fetchAll();

// ── Actividad reciente (últimas inscripciones) ──
$actividad = $db->query("
    SELECT 
        CASE 
            WHEN p2.id_pago IS NOT NULL AND ep.nombre = 'Confirmado' THEN 'Pago confirmado'
            WHEN ei.nombre = 'Cancelada' THEN 'Cancelación'
            ELSE 'Nueva inscripción'
        END AS accion,
        CONCAT(pa.nombres, ' ', pa.apellidos, ' → ', ev.titulo) AS det,
        i.fecha_inscripcion AS fecha,
        CASE 
            WHEN p2.id_pago IS NOT NULL AND ep.nombre = 'Confirmado' THEN 'pay'
            WHEN ei.nombre = 'Cancelada' THEN 'can'
            ELSE 'ins'
        END AS tipo
    FROM inscripciones i
    JOIN participantes pa ON i.id_participante = pa.id_participante
    JOIN eventos ev ON i.id_evento = ev.id_evento
    JOIN estados_inscripcion ei ON i.id_estado_inscripcion = ei.id_estado_inscripcion
    LEFT JOIN pagos p2 ON i.id_inscripcion = p2.id_inscripcion
    LEFT JOIN estados_pago ep ON p2.id_estado_pago = ep.id_estado_pago
    ORDER BY i.fecha_inscripcion DESC
    LIMIT 6
")->fetchAll();

// Tiempo relativo
function timeAgo($datetime) {
    $now = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->diff($past);
    if ($diff->d > 0) return "hace {$diff->d}d";
    if ($diff->h > 0) return "hace {$diff->h}h";
    if ($diff->i > 0) return "hace {$diff->i} min";
    return "justo ahora";
}

require_once __DIR__ . '/includes/layout.php';
layout_head('Panel de Control', 'dashboard');
?>

<!-- HERO -->
<div style="position:relative;overflow:hidden;border-radius:14px;border:1px solid rgba(255,255,255,.1);padding:30px 34px;margin-bottom:20px;background:linear-gradient(135deg,rgba(0,212,255,.035) 0%,var(--c2) 60%,rgba(139,92,246,.025) 100%);animation:riseUp .5s ease both">
  <div style="position:absolute;top:-50%;right:-8%;width:360px;height:360px;border-radius:50%;background:radial-gradient(circle,rgba(0,212,255,.1),transparent 65%);animation:orb1 8s ease-in-out infinite;pointer-events:none;z-index:0"></div>
  <div style="position:absolute;bottom:-60%;left:15%;width:280px;height:280px;border-radius:50%;background:radial-gradient(circle,rgba(139,92,246,.07),transparent 65%);animation:orb2 10s ease-in-out infinite;pointer-events:none;z-index:0"></div>
  <style>@keyframes orb1{0%,100%{transform:translate(0,0)}50%{transform:translate(-25px,18px)}}@keyframes orb2{0%,100%{transform:translate(0,0)}50%{transform:translate(18px,-22px)}}</style>
  <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;gap:20px">
    <div>
      <div style="font-size:10.5px;font-weight:400;letter-spacing:.14em;text-transform:uppercase;color:var(--cyan);margin-bottom:9px;display:flex;align-items:center;gap:8px"><span style="display:inline-block;width:18px;height:1px;background:var(--cyan)"></span>Sistema de gestión interna</div>
      <h1 style="font-size:27px;font-weight:200;letter-spacing:-.025em;line-height:1.25;margin-bottom:9px">Bienvenido de nuevo,<br><strong style="font-weight:500;color:var(--cyan)"><?= e($user['nombres']) ?></strong></h1>
      <p style="font-size:13px;color:var(--t2);font-weight:300;max-width:400px;line-height:1.65">Todo bajo control. Resumen en tiempo real de tu plataforma de eventos.</p>
      <div style="display:flex;gap:9px;margin-top:16px;flex-wrap:wrap">
        <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;font-size:11.5px;font-weight:300;background:var(--cyan-g);color:var(--cyan);border:1px solid rgba(0,212,255,.16)"><span style="width:5px;height:5px;border-radius:50%;background:currentColor;animation:blink 1.8s infinite"></span><?= $stats['eventos_activos'] ?> eventos activos</span>
        <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;font-size:11.5px;font-weight:300;background:rgba(163,230,53,.11);color:var(--lime);border:1px solid rgba(163,230,53,.16)"><span style="width:5px;height:5px;border-radius:50%;background:currentColor;animation:blink 1.8s infinite"></span>+<?= $stats['nuevos_inscritos_hoy'] ?> inscritos hoy</span>
        <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;font-size:11.5px;font-weight:300;background:var(--amber-g);color:var(--amber);border:1px solid rgba(245,158,11,.16)"><span style="width:5px;height:5px;border-radius:50%;background:currentColor;animation:blink 1.8s infinite"></span>$<?= number_format($stats['total_ingresos'],0,',','.') ?> recaudados</span>
      </div>
    </div>
    <div style="display:flex;flex-direction:column;gap:9px;flex-shrink:0">
      <a href="<?= BASE_URL ?>/eventos/crear.php" class="btn btn-p" style="justify-content:center">＋ Crear Evento</a>
      <a href="<?= BASE_URL ?>/eventos/index.php" class="btn btn-g" style="justify-content:center">Ver Eventos →</a>
    </div>
  </div>
</div>

<style>@keyframes blink{0%,100%{opacity:1}50%{opacity:.35}}</style>

<!-- STAT CARDS -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:13px;margin-bottom:20px">
  <?php
  $cards = [
      ['label'=>'Total Eventos','val'=>$stats['total_eventos'],'icon'=>'📅','c'=>'var(--cyan)','g'=>'var(--cyan-g)','meta'=>'Todos los eventos'],
      ['label'=>'Eventos Activos','val'=>$stats['eventos_activos'],'icon'=>'⚡','c'=>'var(--lime)','g'=>'var(--lime-g)','meta'=>'En curso ahora'],
      ['label'=>'Participantes','val'=>$stats['total_asistentes'],'icon'=>'👥','c'=>'var(--amber)','g'=>'var(--amber-g)','meta'=>'Registrados'],
      ['label'=>'Ingresos','val'=>'$'.number_format($stats['total_ingresos'],0,',','.'),'icon'=>'💰','c'=>'var(--violet)','g'=>'var(--violet-g)','meta'=>'Pagos confirmados'],
  ];
  foreach($cards as $i => $card): ?>
  <div style="background:var(--c1);border:1px solid var(--border);border-radius:var(--r);padding:19px 20px;position:relative;overflow:hidden;cursor:default;opacity:0;transform:translateY(22px);animation:riseUp .5s <?= $i*.07 ?>s ease forwards;transition:border-color .3s,transform .25s" 
       onmouseenter="this.style.transform='translateY(-3px)';this.style.borderColor='<?= $card['c'] ?>'" 
       onmouseleave="this.style.transform='';this.style.borderColor=''">
    <div style="position:absolute;top:0;left:0;width:100%;height:2px;background:<?= $card['c'] ?>;border-radius:2px;box-shadow:0 0 7px <?= $card['c'] ?>"></div>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px">
      <div style="font-size:10px;font-weight:400;letter-spacing:.1em;text-transform:uppercase;color:var(--t3)"><?= $card['label'] ?></div>
      <div style="width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:16px;background:<?= $card['g'] ?>;color:<?= $card['c'] ?>"><?= $card['icon'] ?></div>
    </div>
    <div style="font-family:'JetBrains Mono',monospace;font-size:34px;font-weight:300;line-height:1;color:var(--t1);letter-spacing:-.02em"><?= $card['val'] ?></div>
    <div style="margin-top:9px;font-size:11.5px;color:var(--t3)"><?= $card['meta'] ?></div>
  </div>
  <?php endforeach; ?>
</div>

<!-- BOTTOM ROW -->
<div style="display:grid;grid-template-columns:1.5fr 1fr;gap:13px">
  <!-- Eventos Recientes -->
  <div class="card" style="animation:riseUp .5s .3s ease forwards;opacity:0">
    <div class="chd">
      <div><div class="ctitle">Eventos Recientes</div><div class="csub">Próximas e iniciativas activas</div></div>
      <a href="<?= BASE_URL ?>/eventos/index.php" class="btn btn-g" style="padding:5px 12px;font-size:11.5px">Ver todos →</a>
    </div>
    <table class="et">
      <thead><tr><th>Evento</th><th>Fecha</th><th>Ocupación</th><th>Estado</th></tr></thead>
      <tbody>
      <?php foreach($eventos as $ev):
        $p = $ev['cupo'] > 0 ? round($ev['inscritos']*100/$ev['cupo']) : 0;
        $bc = $p>=90?'var(--rose)':($p>=60?'var(--amber)':'var(--cyan)');
        $estadoClass = strtolower($ev['estado']);
      ?>
      <tr>
        <td><div class="etn"><?= e($ev['nombre']) ?></div><div class="etc"><?= e($ev['cat']) ?></div></td>
        <td><?= date('d M Y', strtotime($ev['fecha'])) ?></td>
        <td style="white-space:nowrap">
          <span class="obar"><span class="ofill" data-w="<?= $p ?>" style="background:<?= $bc ?>"></span></span>
          <span style="font-family:'JetBrains Mono',monospace;font-size:11.5px;color:<?= $bc ?>"><?= $p ?>%</span>
        </td>
        <td><span class="sbg <?= $estadoClass ?>"><span class="sbg-d"></span><?= e($ev['estado']) ?></span></td>
      </tr>
      <?php endforeach ?>
      <?php if (empty($eventos)): ?>
      <tr><td colspan="4" style="text-align:center;padding:30px;color:var(--t3)">No hay eventos aún</td></tr>
      <?php endif ?>
      </tbody>
    </table>
  </div>

  <!-- Actividad Reciente -->
  <div class="card" style="animation:riseUp .5s .4s ease forwards;opacity:0">
    <div class="chd">
      <div><div class="ctitle">Actividad Reciente</div><div class="csub">Últimas acciones</div></div>
    </div>
    <div style="display:flex;flex-direction:column;gap:1px">
      <?php
      $cm=['ins'=>'background:var(--cyan-g);color:var(--cyan)','pay'=>'background:var(--lime-g);color:var(--lime)','can'=>'background:var(--rose-g);color:var(--rose)'];
      $em=['ins'=>'＋','pay'=>'$','can'=>'✕'];
      foreach($actividad as $k=>$a):?>
      <div style="display:flex;align-items:flex-start;gap:11px;padding:10px 9px;border-radius:8px;transition:background .15s;animation:riseUp .4s <?= .42+$k*.07 ?>s ease both;opacity:0"
           onmouseenter="this.style.background='rgba(255,255,255,.022)'" onmouseleave="this.style.background=''">
        <div style="width:28px;height:28px;border-radius:7px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:12px;margin-top:1px;<?= $cm[$a['tipo']] ?? $cm['ins'] ?>"><?= $em[$a['tipo']] ?? '＋' ?></div>
        <div style="flex:1;min-width:0">
          <div style="font-size:12.5px;font-weight:400;color:var(--t1)"><?= e($a['accion']) ?></div>
          <div style="font-size:11px;color:var(--t3);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($a['det']) ?></div>
        </div>
        <div style="font-size:10.5px;color:var(--t4);white-space:nowrap;font-family:'JetBrains Mono',monospace;font-weight:300"><?= timeAgo($a['fecha']) ?></div>
      </div>
      <?php endforeach ?>
      <?php if (empty($actividad)): ?>
      <div style="text-align:center;padding:30px;color:var(--t3)">Sin actividad reciente</div>
      <?php endif ?>
    </div>
  </div>
</div>

<?php layout_footer(); ?>
