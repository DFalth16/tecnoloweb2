<?php
/**
 * EventCore — Editar Usuario + Asignar Rol
 */
require_once __DIR__ . '/../config.php';
requireLogin();
requirePermission([1]); // Solo Admin

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM usuarios_admin WHERE id_usuario = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    setFlash('error', 'Usuario no encontrado.');
    header('Location: index.php');
    exit;
}

$roles = $db->query("SELECT * FROM roles ORDER BY id_rol")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres   = trim($_POST['nombres'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $id_rol    = (int)($_POST['id_rol'] ?? 0);
    $activo    = isset($_POST['activo']) ? 1 : 0;
    $password  = $_POST['password'] ?? '';
    
    // Validaciones
    if (empty($nombres))   $errors[] = 'El nombre es obligatorio.';
    if (empty($apellidos))  $errors[] = 'El apellido es obligatorio.';
    if (empty($email))      $errors[] = 'El email es obligatorio.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email no válido.';
    if ($id_rol < 1)        $errors[] = 'Seleccione un rol.';
    
    // Check email único (excluyendo el actual)
    if (empty($errors)) {
        $check = $db->prepare("SELECT COUNT(*) FROM usuarios_admin WHERE email = ? AND id_usuario != ?");
        $check->execute([$email, $id]);
        if ($check->fetchColumn() > 0) {
            $errors[] = 'Ya existe otro usuario con ese email.';
        }
    }
    
    if (empty($errors)) {
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE usuarios_admin SET nombres=?, apellidos=?, email=?, id_rol=?, activo=?, password_hash=? WHERE id_usuario=?");
                $stmt->execute([$nombres, $apellidos, $email, $id_rol, $activo, $hash, $id]);
            }
        }
        
        if (empty($errors)) {
            if (empty($password)) {
                $stmt = $db->prepare("UPDATE usuarios_admin SET nombres=?, apellidos=?, email=?, id_rol=?, activo=? WHERE id_usuario=?");
                $stmt->execute([$nombres, $apellidos, $email, $id_rol, $activo, $id]);
            }
            
            setFlash('success', 'Usuario actualizado exitosamente.');
            header('Location: index.php');
            exit;
        }
    }
}

require_once __DIR__ . '/../includes/layout.php';
layout_head('Editar Usuario', 'usuarios');
?>

<div class="page-header">
  <h2>Editar <strong>Usuario</strong></h2>
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
        <input type="text" name="nombres" class="form-control" value="<?= e($usuario['nombres']) ?>" required>
      </div>
      <div class="form-group">
        <label>Apellidos</label>
        <input type="text" name="apellidos" class="form-control" value="<?= e($usuario['apellidos']) ?>" required>
      </div>
    </div>
    
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" class="form-control" value="<?= e($usuario['email']) ?>" required>
    </div>
    
    <div class="form-group">
      <label>Rol (Asignar permisos)</label>
      <select name="id_rol" class="form-control" required>
        <?php foreach($roles as $r): ?>
        <option value="<?= $r['id_rol'] ?>" <?= $usuario['id_rol'] == $r['id_rol'] ? 'selected' : '' ?>>
          <?= e($r['nombre_rol']) ?> — <?= e($r['descripcion']) ?>
        </option>
        <?php endforeach ?>
      </select>
    </div>
    
    <div class="form-group">
      <label>Nueva Contraseña (dejar vacío para mantener)</label>
      <input type="password" name="password" class="form-control" placeholder="Dejar vacío si no desea cambiar">
    </div>
    
    <div class="form-group">
      <label class="form-check">
        <input type="checkbox" name="activo" <?= $usuario['activo'] ? 'checked' : '' ?>>
        <span>Usuario activo</span>
      </label>
    </div>
    
    <div class="form-actions">
      <button type="submit" class="btn btn-p">💾 Guardar Cambios</button>
      <a href="index.php" class="btn btn-g">Cancelar</a>
    </div>
  </form>
</div>

<div class="card" style="max-width:600px;margin-top:16px;padding:14px 20px">
  <div style="font-size:11px;color:var(--t3)">
    <strong style="color:var(--t2)">Info:</strong> 
    Creado el <?= date('d/m/Y H:i', strtotime($usuario['creado_en'])) ?> · 
    ID: <?= $usuario['id_usuario'] ?>
  </div>
</div>

<?php layout_footer(); ?>
