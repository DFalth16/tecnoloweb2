<?php
/**
 * EventCore — Listado de Sedes
 */
require_once __DIR__ . '/../config.php';
requireLogin();

$db = getDB();
$sedes = $db->query("
    SELECT s.*, 
           (SELECT COUNT(*) FROM eventos e WHERE e.id_sede = s.id_sede) AS total_eventos
    FROM sedes s 
    ORDER BY s.nombre
")->fetchAll();

require_once __DIR__ . '/../includes/layout.php';
layout_head('Sedes', 'sedes');
?>

<div class="page-header">
  <h2>Gestión de <strong>Sedes</strong></h2>
  <a href="crear.php" class="btn btn-p">＋ Nueva Sede</a>
</div>

<div class="card">
  <table class="et">
    <thead>
      <tr>
        <th>Sede</th>
        <th>Dirección</th>
        <th>Ciudad</th>
        <th>Capacidad</th>
        <th>Eventos</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($sedes as $s): ?>
    <tr>
      <td><div class="etn"><?= e($s['nombre']) ?></div></td>
      <td style="font-size:12px"><?= e($s['direccion']) ?></td>
      <td><?= e($s['ciudad']) ?>, <?= e($s['pais']) ?></td>
      <td style="font-family:'JetBrains Mono',monospace;font-size:12px"><?= $s['capacidad'] ? number_format($s['capacidad']) : '—' ?></td>
      <td><span class="nb nb-c"><?= $s['total_eventos'] ?></span></td>
      <td>
        <div style="display:flex;gap:6px">
          <a href="editar.php?id=<?= $s['id_sede'] ?>" class="btn btn-g" style="padding:4px 10px;font-size:11px">✏ Editar</a>
          <?php if ($s['total_eventos'] == 0): ?>
          <a href="eliminar.php?id=<?= $s['id_sede'] ?>" class="btn btn-d" style="padding:4px 10px;font-size:11px" onclick="return confirm('¿Eliminar esta sede?')">🗑 Eliminar</a>
          <?php endif ?>
        </div>
      </td>
    </tr>
    <?php endforeach ?>
    <?php if (empty($sedes)): ?>
    <tr><td colspan="6" class="empty-state" style="padding:40px"><div class="icon">⌂</div><p>No hay sedes registradas</p><a href="crear.php" class="btn btn-p">＋ Crear primera sede</a></td></tr>
    <?php endif ?>
    </tbody>
  </table>
</div>

<?php layout_footer(); ?>
