<?php
/**
 * EventCore — Crear Evento
 */
require_once __DIR__ . '/../config.php';
requireLogin();
requirePermission([1, 2]); // Admin u Organizador

$db = getDB();
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
    
    // Validaciones
    if (empty($titulo))      $errors[] = 'El título es obligatorio.';
    if ($id_categoria < 1)   $errors[] = 'Seleccione una categoría.';
    if ($id_sede < 1)        $errors[] = 'Seleccione una sede.';
    if (empty($fecha_inicio)) $errors[] = 'La fecha de inicio es obligatoria.';
    if (empty($fecha_fin))   $errors[] = 'La fecha de fin es obligatoria.';
    if ($cupo_maximo < 1)    $errors[] = 'El cupo máximo debe ser mayor a 0.';
    if (!$es_gratuito && $precio <= 0) $errors[] = 'Ingrese un precio válido o marque como gratuito.';
    
    if (empty($errors) && $fecha_fin <= $fecha_inicio) {
        $errors[] = 'La fecha de fin debe ser posterior a la de inicio.';
    }

    if ($es_gratuito) $precio = 0.00;
    
    if (empty($errors)) {
        $codigo = generateCode('EVT');
        $stmt = $db->prepare("
            INSERT INTO eventos (id_categoria, id_estado, id_sede, id_organizador, codigo_evento, titulo, descripcion, fecha_inicio, fecha_fin, cupo_maximo, precio_entrada, es_gratuito)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $id_categoria, $id_estado, $id_sede, currentUser()['id'],
            $codigo, $titulo, $descripcion,
            $fecha_inicio, $fecha_fin,
            $cupo_maximo, $precio, $es_gratuito
        ]);
        
        setFlash('success', "Evento «{$titulo}» creado exitosamente. Código: {$codigo}");
        header('Location: index.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/layout.php';
layout_head('Crear Evento', 'eventos');
?>

<div class="page-header">
  <h2>Crear <strong>Evento</strong></h2>
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
  <form method="POST" action="">
    <div class="form-group">
      <label>Título del Evento</label>
      <input type="text" name="titulo" class="form-control" placeholder="Ej: Tech Summit Bolivia 2026" value="<?= e($_POST['titulo'] ?? '') ?>" required>
    </div>
    
    <div class="form-group">
      <label>Descripción</label>
      <textarea name="descripcion" class="form-control" placeholder="Descripción detallada del evento..."><?= e($_POST['descripcion'] ?? '') ?></textarea>
    </div>
    
    <div class="form-row-3">
      <div class="form-group">
        <label>Categoría</label>
        <select name="id_categoria" class="form-control" required>
          <option value="">Seleccionar...</option>
          <?php foreach($categorias as $c): ?>
          <option value="<?= $c['id_categoria'] ?>" <?= (($_POST['id_categoria'] ?? '') == $c['id_categoria']) ? 'selected' : '' ?>><?= e($c['nombre']) ?></option>
          <?php endforeach ?>
        </select>
      </div>
      <div class="form-group">
        <label>Estado</label>
        <select name="id_estado" class="form-control" required>
          <?php foreach($estados as $es): ?>
          <option value="<?= $es['id_estado'] ?>" <?= (($_POST['id_estado'] ?? '1') == $es['id_estado']) ? 'selected' : '' ?>><?= e($es['nombre']) ?></option>
          <?php endforeach ?>
        </select>
      </div>
      <div class="form-group">
        <label>Sede</label>
        <select name="id_sede" class="form-control" required>
          <option value="">Seleccionar...</option>
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
        <input type="number" name="cupo_maximo" class="form-control" min="1" placeholder="Ej: 200" value="<?= e($_POST['cupo_maximo'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Precio Entrada (Bs.)</label>
        <input type="number" name="precio_entrada" class="form-control" min="0" step="0.01" placeholder="0.00" value="<?= e($_POST['precio_entrada'] ?? '') ?>" id="precioInput">
      </div>
      <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:2px">
        <label class="form-check">
          <input type="checkbox" name="es_gratuito" id="gratuitoCheck" <?= isset($_POST['es_gratuito']) ? 'checked' : '' ?> onchange="document.getElementById('precioInput').disabled=this.checked;if(this.checked)document.getElementById('precioInput').value='0'">
          <span>Evento gratuito</span>
        </label>
      </div>
    </div>
    
    <div class="form-actions">
      <button type="submit" class="btn btn-p">＋ Crear Evento</button>
      <a href="index.php" class="btn btn-g">Cancelar</a>
    </div>
  </form>
</div>

<?php layout_footer(); ?>
