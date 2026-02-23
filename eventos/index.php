<?php
/**
 * EventCore — Listado de Eventos
 */
require_once __DIR__ . '/../config.php';
requireLogin();

$db = getDB();

// Filtros
$search    = trim($_GET['q'] ?? '');
$catFilter = $_GET['cat'] ?? '';
$estFilter = $_GET['estado'] ?? '';

$sql = "SELECT e.*, ee.nombre AS estado, ce.nombre AS categoria, s.nombre AS sede,
        ua.nombres AS org_nombres, ua.apellidos AS org_apellidos,
        (SELECT COUNT(*) FROM inscripciones i WHERE i.id_evento = e.id_evento) AS inscritos
        FROM eventos e
        JOIN estados_evento ee ON e.id_estado = ee.id_estado
        JOIN categorias_evento ce ON e.id_categoria = ce.id_categoria
        JOIN sedes s ON e.id_sede = s.id_sede
        JOIN usuarios_admin ua ON e.id_organizador = ua.id_usuario
        WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (e.titulo LIKE ? OR e.codigo_evento LIKE ? OR s.nombre LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($catFilter) {
    $sql .= " AND e.id_categoria = ?";
    $params[] = $catFilter;
}
if ($estFilter) {
    $sql .= " AND e.id_estado = ?";
    $params[] = $estFilter;
}

$sql .= " ORDER BY e.fecha_inicio DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$eventos = $stmt->fetchAll();

$categorias = $db->query("SELECT * FROM categorias_evento ORDER BY nombre")->fetchAll();
$estados    = $db->query("SELECT * FROM estados_evento ORDER BY id_estado")->fetchAll();

require_once __DIR__ . '/../includes/layout.php';
layout_head('Eventos', 'eventos');
?>

<div class="page-header">
  <h2>Gestión de <strong>Eventos</strong></h2>
  <a href="crear.php" class="btn btn-p">＋ Nuevo Evento</a>
</div>

<div class="filter-bar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <input type="text" name="q" class="form-control search-input" placeholder="Buscar evento, código, sede..." value="<?= e($search) ?>">
    <select name="cat" class="form-control" onchange="this.form.submit()">
      <option value="">Todas las categorías</option>
      <?php foreach($categorias as $c): ?>
      <option value="<?= $c['id_categoria'] ?>" <?= $catFilter == $c['id_categoria'] ? 'selected' : '' ?>><?= e($c['nombre']) ?></option>
      <?php endforeach ?>
    </select>
    <select name="estado" class="form-control" onchange="this.form.submit()">
      <option value="">Todos los estados</option>
      <?php foreach($estados as $es): ?>
      <option value="<?= $es['id_estado'] ?>" <?= $estFilter == $es['id_estado'] ? 'selected' : '' ?>><?= e($es['nombre']) ?></option>
      <?php endforeach ?>
    </select>
    <button type="submit" class="btn btn-g">🔍 Buscar</button>
    <?php if ($search || $catFilter || $estFilter): ?>
    <a href="index.php" class="btn btn-g">✕ Limpiar</a>
    <?php endif ?>
  </form>
</div>

<div class="card">
  <table class="et">
    <thead>
      <tr>
        <th>Evento</th>
        <th>Sede</th>
        <th>Fecha</th>
        <th>Ocupación</th>
        <th>Precio</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($eventos as $ev):
      $p = $ev['cupo_maximo'] > 0 ? round($ev['inscritos']*100/$ev['cupo_maximo']) : 0;
      $bc = $p>=90?'var(--rose)':($p>=60?'var(--amber)':'var(--cyan)');
      $estadoClass = strtolower($ev['estado']);
    ?>
    <tr>
      <td>
        <div class="etn"><?= e($ev['titulo']) ?></div>
        <div class="etc"><?= e($ev['categoria']) ?> · <?= e($ev['codigo_evento']) ?></div>
      </td>
      <td style="font-size:12px"><?= e($ev['sede']) ?></td>
      <td style="white-space:nowrap;font-size:12px">
        <div><?= date('d M Y', strtotime($ev['fecha_inicio'])) ?></div>
        <div class="etc"><?= date('H:i', strtotime($ev['fecha_inicio'])) ?> - <?= date('H:i', strtotime($ev['fecha_fin'])) ?></div>
      </td>
      <td style="white-space:nowrap">
        <span class="obar"><span class="ofill" data-w="<?= $p ?>" style="background:<?= $bc ?>"></span></span>
        <span style="font-family:'JetBrains Mono',monospace;font-size:11.5px;color:<?= $bc ?>"><?= $ev['inscritos'] ?>/<?= $ev['cupo_maximo'] ?></span>
      </td>
      <td style="font-family:'JetBrains Mono',monospace;font-size:12px">
        <?php if ($ev['es_gratuito']): ?>
          <span style="color:var(--lime)">Gratis</span>
        <?php else: ?>
          $<?= number_format($ev['precio_entrada'],2) ?>
        <?php endif ?>
      </td>
      <td><span class="sbg <?= $estadoClass ?>"><span class="sbg-d"></span><?= e($ev['estado']) ?></span></td>
      <td>
        <div style="display:flex;gap:6px">
          <a href="editar.php?id=<?= $ev['id_evento'] ?>" class="btn btn-g" style="padding:4px 10px;font-size:11px">✏ Editar</a>
          <?php if ($ev['estado'] !== 'Cancelado'): ?>
          <a href="eliminar.php?id=<?= $ev['id_evento'] ?>" class="btn btn-d" style="padding:4px 10px;font-size:11px" onclick="return confirm('¿Cancelar este evento?')">✕ Cancelar</a>
          <?php endif ?>
        </div>
      </td>
    </tr>
    <?php endforeach ?>
    <?php if (empty($eventos)): ?>
    <tr><td colspan="7" class="empty-state" style="padding:40px"><div class="icon">◉</div><p>No hay eventos registrados</p><a href="crear.php" class="btn btn-p">＋ Crear primer evento</a></td></tr>
    <?php endif ?>
    </tbody>
  </table>
</div>

<?php layout_footer(); ?>
