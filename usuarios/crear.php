<?php
/**
 * EventCore — Crear Usuario
 */
require_once __DIR__ . '/../config.php';
requireLogin();
requirePermission([1]); // Solo Admin

$db = getDB();
$roles = $db->query("SELECT * FROM roles ORDER BY id_rol")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres   = trim($_POST['nombres'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $id_rol    = (int)($_POST['id_rol'] ?? 0);
    
    // Validaciones
    if (empty($nombres))   $errors[] = 'El nombre es obligatorio.';
    if (empty($apellidos))  $errors[] = 'El apellido es obligatorio.';
    if (empty($email))      $errors[] = 'El email es obligatorio.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email no válido.';
    if (strlen($password) < 6) $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
    if ($password !== $password2) $errors[] = 'Las contraseñas no coinciden.';
    if ($id_rol < 1)        $errors[] = 'Seleccione un rol.';
    
    // Check email único
    if (empty($errors)) {
        $check = $db->prepare("SELECT COUNT(*) FROM usuarios_admin WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetchColumn() > 0) {
            $errors[] = 'Ya existe un usuario con ese email.';
        }
    }
    
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO usuarios_admin (id_rol, nombres, apellidos, email, password_hash, activo) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->execute([$id_rol, $nombres, $apellidos, $email, $hash]);
        
        setFlash('success', 'Usuario creado exitosamente.');
        header('Location: index.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/layout.php';
layout_head('Crear Usuario', 'usuarios');
?>

<div class="page-header">
  <h2>Crear <strong>Usuario</strong></h2>
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
    <div class="form-row">
      <div class="form-group">
        <label>Nombres</label>
        <input type="text" name="nombres" class="form-control" placeholder="Ingrese nombres" value="<?= e($_POST['nombres'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Apellidos</label>
        <input type="text" name="apellidos" class="form-control" placeholder="Ingrese apellidos" value="<?= e($_POST['apellidos'] ?? '') ?>" required>
      </div>
    </div>
    
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" value="<?= e($_POST['email'] ?? '') ?>" required>
    </div>
    
    <div class="form-group">
      <label>Rol</label>
      <select name="id_rol" class="form-control" required>
        <option value="">Seleccionar rol...</option>
        <?php foreach($roles as $r): ?>
        <option value="<?= $r['id_rol'] ?>" <?= (($_POST['id_rol'] ?? '') == $r['id_rol']) ? 'selected' : '' ?>>
          <?= e($r['nombre_rol']) ?> — <?= e($r['descripcion']) ?>
        </option>
        <?php endforeach ?>
      </select>
    </div>
    
    <div class="form-row">
      <div class="form-group">
        <label>Contraseña</label>
        <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required>
      </div>
      <div class="form-group">
        <label>Confirmar Contraseña</label>
        <input type="password" name="password2" class="form-control" placeholder="Repetir contraseña" required>
      </div>
    </div>
    
    <div class="form-actions">
      <button type="submit" class="btn btn-p">💾 Crear Usuario</button>
      <a href="index.php" class="btn btn-g">Cancelar</a>
    </div>
  </form>
</div>

<?php layout_footer(); ?>
