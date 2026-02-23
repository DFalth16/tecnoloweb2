<?php
/**
 * EventCore — Listado de Usuarios
 * Solo accesible para Administradores
 */
require_once __DIR__ . '/../config.php';
requireLogin();
requirePermission([1]); // Solo Admin

$db = getDB();

// Búsqueda
$search = trim($_GET['q'] ?? '');
$rolFilter = $_GET['rol'] ?? '';

$sql = "SELECT u.*, r.nombre_rol FROM usuarios_admin u JOIN roles r ON u.id_rol = r.id_rol WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (u.nombres LIKE ? OR u.apellidos LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($rolFilter) {
    $sql .= " AND u.id_rol = ?";
    $params[] = $rolFilter;
}
$sql .= " ORDER BY u.creado_en DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$usuarios = $stmt->fetchAll();

$roles = $db->query("SELECT * FROM roles ORDER BY id_rol")->fetchAll();

require_once __DIR__ . '/../includes/layout.php';
layout_head('Gestión de Usuarios', 'usuarios');
?>

<div class="page-header">
  <h2>Gestión de <strong>Usuarios</strong></h2>
  <a href="crear.php" class="btn btn-p">＋ Nuevo Usuario</a>
</div>

<div class="filter-bar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <input type="text" name="q" class="form-control search-input" placeholder="Buscar por nombre, email..." value="<?= e($search) ?>">
    <select name="rol" class="form-control" onchange="this.form.submit()">
      <option value="">Todos los roles</option>
      <?php foreach($roles as $r): ?>
      <option value="<?= $r['id_rol'] ?>" <?= $rolFilter == $r['id_rol'] ? 'selected' : '' ?>><?= e($r['nombre_rol']) ?></option>
      <?php endforeach ?>
    </select>
    <button type="submit" class="btn btn-g">🔍 Buscar</button>
    <?php if ($search || $rolFilter): ?>
    <a href="index.php" class="btn btn-g">✕ Limpiar</a>
    <?php endif ?>
  </form>
</div>

<div class="card">
  <table class="et">
    <thead>
      <tr>
        <th>Usuario</th>
        <th>Email</th>
        <th>Rol</th>
        <th>Estado</th>
        <th>Creado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($usuarios as $u): 
      $rolClass = match((int)$u['id_rol']) {
          1 => 'role-admin',
          2 => 'role-organizador',
          3 => 'role-reportes',
          default => 'role-reportes'
      };
    ?>
    <tr>
      <td>
        <div class="etn"><?= e($u['nombres'] . ' ' . $u['apellidos']) ?></div>
      </td>
      <td><?= e($u['email']) ?></td>
      <td><span class="role-tag <?= $rolClass ?>"><?= e($u['nombre_rol']) ?></span></td>
      <td>
        <?php if ($u['activo']): ?>
          <span class="badge-active">● Activo</span>
        <?php else: ?>
          <span class="badge-inactive">● Inactivo</span>
        <?php endif ?>
      </td>
      <td style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--t4)"><?= date('d/m/Y', strtotime($u['creado_en'])) ?></td>
      <td>
        <div style="display:flex;gap:6px">
          <a href="editar.php?id=<?= $u['id_usuario'] ?>" class="btn btn-g" style="padding:4px 10px;font-size:11px">✏ Editar</a>
          <?php if ($u['id_usuario'] != currentUser()['id']): ?>
          <a href="eliminar.php?id=<?= $u['id_usuario'] ?>" class="btn btn-d" style="padding:4px 10px;font-size:11px" onclick="return confirm('¿Desactivar/activar este usuario?')">
            <?= $u['activo'] ? '⏻ Desactivar' : '↺ Activar' ?>
          </a>
          <?php endif ?>
        </div>
      </td>
    </tr>
    <?php endforeach ?>
    <?php if (empty($usuarios)): ?>
    <tr><td colspan="6" class="empty-state" style="padding:40px"><div class="icon">◯</div><p>No se encontraron usuarios</p></td></tr>
    <?php endif ?>
    </tbody>
  </table>
</div>

<?php layout_footer(); ?>
