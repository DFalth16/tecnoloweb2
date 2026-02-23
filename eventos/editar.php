<?php
/**
 * EventCore — Editar Evento
 */
require_once __DIR__ . '/../config.php';
requireLogin();
requirePermission([1, 2]);

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM eventos WHERE id_evento = ?");
$stmt->execute([$id]);
$evento = $stmt->fetch();

if (!$evento) {
    setFlash('error', 'Evento no encontrado.');
    header('Location: index.php');
    exit;
}

$categorias = $db->query("SELECT * FROM categorias_evento ORDER BY nombre")->fetchAll();
$estados    = $db->query("SELECT * FROM estados_evento ORDER BY id_estado")->fetchAll();
$sedes      = $db->query("SELECT * FROM sedes ORDER BY nombre")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo       = trim($_POST['titulo'] ?? '');
    $descripcion  = trim($_POST['descripcion'] ?? '');
    $id_categoria = (int)($_POST['id_categoria'] ?? 0);
    $id_estado    = (int)($_POST['id_estado'] ?? 1);
    $id_sede      = (int)($_POST['id_sede'] ?? 0);
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin    = $_POST['fecha_fin'] ?? '';
    $cupo_maximo  = (int)($_POST['cupo_maximo'] ?? 0);
    $precio       = (float)($_POST['precio_entrada'] ?? 0);
    $es_gratuito  = isset($_POST['es_gratuito']) ? 1 : 0;
    
    if (empty($titulo))      $errors[] = 'El título es obligatorio.';
    if ($id_categoria < 1)   $errors[] = 'Seleccione una categoría.';
    if ($id_sede < 1)        $errors[] = 'Seleccione una sede.';
    if (empty($fecha_inicio)) $errors[] = 'La fecha de inicio es obligatoria.';
    if (empty($fecha_fin))   $errors[] = 'La fecha de fin es obligatoria.';
    if ($cupo_maximo < 1)    $errors[] = 'El cupo máximo debe ser mayor a 0.';
    
    if (empty($errors) && $fecha_fin <= $fecha_inicio) {
        $errors[] = 'La fecha de fin debe ser posterior a la de inicio.';
    }

    if ($es_gratuito) $precio = 0.00;
    
    if (empty($errors)) {
        $stmt = $db->prepare("
            UPDATE eventos SET titulo=?, descripcion=?, id_categoria=?, id_estado=?, id_sede=?,
            fecha_inicio=?, fecha_fin=?, cupo_maximo=?, precio_entrada=?, es_gratuito=?
            WHERE id_evento=?
        ");
        $stmt->execute([
            $titulo, $descripcion, $id_categoria, $id_estado, $id_sede,
            $fecha_inicio, $fecha_fin, $cupo_maximo, $precio, $es_gratuito, $id
        ]);
        
        setFlash('success', "Evento «{$titulo}» actualizado exitosamente.");
        header('Location: index.php');
        exit;
    }
} else {
    // Pre-fill from DB
    $_POST = $evento;
    $_POST['fecha_inicio'] = date('Y-m-d\TH:i', strtotime($evento['fecha_inicio']));
    $_POST['fecha_fin']    = date('Y-m-d\TH:i', strtotime($evento['fecha_fin']));
}

require_once __DIR__ . '/../includes/layout.php';
layout_head('Editar Evento', 'eventos');
?>

<div class="page-header">
  <h2>Editar <strong>Evento</strong></h2>
  <a href="index.php" class="btn btn-g">← Volver</a>
</div>

<?php if ($errors): ?>
<div class="flash flash-error">
  <?php foreach($errors as $err): ?>
    <div><?= e($err) ?></div>
  <?php endforeach ?>
</div>
<?php endif ?>

<div class="card" style="max-width:700px">
  <div style="margin-bottom:16px;padding:10px 14px;border-radius:var(--r2);background:var(--cyan-g);border:1px solid rgba(0,212,255,.16)">
    <span style="font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--cyan)">📋 <?= e($evento['codigo_evento']) ?></span>
  </div>
  
  <form method="POST" action="">
    <div class="form-group">
      <label>Título del Evento</label>
      <input type="text" name="titulo" class="form-control" value="<?= e($_POST['titulo'] ?? '') ?>" required>
    </div>
    
    <div class="form-group">
      <label>Descripción</label>
      <textarea name="descripcion" class="form-control"><?= e($_POST['descripcion'] ?? '') ?></textarea>
    </div>
    
    <div class="form-row-3">
      <div class="form-group">
        <label>Categoría</label>
        <select name="id_categoria" class="form-control" required>
          <?php foreach($categorias as $c): ?>
          <option value="<?= $c['id_categoria'] ?>" <?= (($_POST['id_categoria'] ?? '') == $c['id_categoria']) ? 'selected' : '' ?>><?= e($c['nombre']) ?></option>
          <?php endforeach ?>
        </select>
      </div>
      <div class="form-group">
        <label>Estado</label>
        <select name="id_estado" class="form-control" required>
          <?php foreach($estados as $es): ?>
          <option value="<?= $es['id_estado'] ?>" <?= (($_POST['id_estado'] ?? '') == $es['id_estado']) ? 'selected' : '' ?>><?= e($es['nombre']) ?></option>
          <?php endforeach ?>
        </select>
      </div>
      <div class="form-group">
        <label>Sede</label>
        <select name="id_sede" class="form-control" required>
          <?php foreach($sedes as $s): ?>
          <option value="<?= $s['id_sede'] ?>" <?= (($_POST['id_sede'] ?? '') == $s['id_sede']) ? 'selected' : '' ?>><?= e($s['nombre']) ?> (<?= e($s['ciudad']) ?>)</option>
          <?php endforeach ?>
        </select>
      </div>
    </div>
    
    <div class="form-row">
      <div class="form-group">
        <label>Fecha y hora de inicio</label>
        <input type="datetime-local" name="fecha_inicio" class="form-control" value="<?= e($_POST['fecha_inicio'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Fecha y hora de fin</label>
        <input type="datetime-local" name="fecha_fin" class="form-control" value="<?= e($_POST['fecha_fin'] ?? '') ?>" required>
      </div>
    </div>
    
    <div class="form-row-3">
      <div class="form-group">
        <label>Cupo Máximo</label>
        <input type="number" name="cupo_maximo" class="form-control" min="1" value="<?= e($_POST['cupo_maximo'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Precio Entrada (Bs.)</label>
        <input type="number" name="precio_entrada" class="form-control" min="0" step="0.01" value="<?= e($_POST['precio_entrada'] ?? '') ?>" id="precioInput" <?= !empty($_POST['es_gratuito']) ? 'disabled' : '' ?>>
      </div>
      <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:2px">
        <label class="form-check">
          <input type="checkbox" name="es_gratuito" id="gratuitoCheck" <?= !empty($_POST['es_gratuito']) ? 'checked' : '' ?> onchange="document.getElementById('precioInput').disabled=this.checked;if(this.checked)document.getElementById('precioInput').value='0'">
          <span>Evento gratuito</span>
        </label>
      </div>
    </div>
    
    <div class="form-actions">
      <button type="submit" class="btn btn-p">💾 Guardar Cambios</button>
      <a href="index.php" class="btn btn-g">Cancelar</a>
    </div>
  </form>
</div>

<?php layout_footer(); ?>
