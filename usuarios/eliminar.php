<?php
/**
 * EventCore — Desactivar/Activar Usuario (toggle)
 */
require_once __DIR__ . '/../config.php';
requireLogin();
requirePermission([1]); // Solo Admin

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0 || $id == currentUser()['id']) {
    setFlash('error', 'No se puede desactivar este usuario.');
    header('Location: index.php');
    exit;
}

// Toggle activo
$stmt = $db->prepare("UPDATE usuarios_admin SET activo = NOT activo WHERE id_usuario = ?");
$stmt->execute([$id]);

setFlash('success', 'Estado del usuario actualizado.');
header('Location: index.php');
exit;
