<?php
/**
 * EventCore — Crear Sede
 */
require_once __DIR__ . '/../config.php';
requireLogin();
requirePermission([1, 2]);

$db = getDB();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $ciudad    = trim($_POST['ciudad'] ?? '');
    $pais      = trim($_POST['pais'] ?? 'Bolivia');
    $capacidad = (int)($_POST['capacidad'] ?? 0);
    $referencia= trim($_POST['referencia'] ?? '');
    
    if (empty($nombre))    $errors[] = 'El nombre es obligatorio.';
    if (empty($direccion))  $errors[] = 'La dirección es obligatoria.';
    if (empty($ciudad))     $errors[] = 'La ciudad es obligatoria.';
    
    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO sedes (nombre, direccion, ciudad, pais, capacidad, referencia) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $direccion, $ciudad, $pais, $capacidad ?: null, $referencia ?: null]);
        
        setFlash('success', "Sede «{$nombre}» creada exitosamente.");
        header('Location: index.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/layout.php';
layout_head('Crear Sede', 'sedes');
?>

<div class="page-header">
  <h2>Crear <strong>Sede</strong></h2>
  <a href="index.php" class="btn btn-g">← Volver</a>
</div>

<?php if ($errors): ?>
<div class="flash flash-error">
  <?php foreach($errors as $err): ?>
    <div><?= e($err) ?></div>
  <?php endforeach ?>
</div>
<?php endif ?>

<div class="card" style="max-width:600px">
  <form method="POST" action="">
    <div class="form-group">
      <label>Nombre de la Sede</label>
      <input type="text" name="nombre" class="form-control" placeholder="Ej: Centro de Convenciones Illimani" value="<?= e($_POST['nombre'] ?? '') ?>" required>
    </div>
    
    <div class="form-group">
      <label>Dirección</label>
      <input type="text" name="direccion" class="form-control" placeholder="Av. / Calle / Número" value="<?= e($_POST['direccion'] ?? '') ?>" required>
    </div>
    
    <div class="form-row">
      <div class="form-group">
        <label>Ciudad</label>
        <input type="text" name="ciudad" class="form-control" placeholder="Ej: La Paz" value="<?= e($_POST['ciudad'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>País</label>
        <input type="text" name="pais" class="form-control" value="<?= e($_POST['pais'] ?? 'Bolivia') ?>">
      </div>
    </div>
    
    <div class="form-row">
      <div class="form-group">
        <label>Capacidad (personas)</label>
        <input type="number" name="capacidad" class="form-control" min="0" placeholder="Ej: 500" value="<?= e($_POST['capacidad'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Referencia</label>
        <input type="text" name="referencia" class="form-control" placeholder="Ej: Frente a la plaza..." value="<?= e($_POST['referencia'] ?? '') ?>">
      </div>
    </div>
    
    <div class="form-actions">
      <button type="submit" class="btn btn-p">＋ Crear Sede</button>
      <a href="index.php" class="btn btn-g">Cancelar</a>
    </div>
  </form>
</div>

<?php layout_footer(); ?>
