<?php
/**
 * EventCore — Eliminar Sede
 */
require_once __DIR__ . '/../config.php';
requireLogin();
requirePermission([1]);

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Check no tiene eventos
$check = $db->prepare("SELECT COUNT(*) FROM eventos WHERE id_sede = ?");
$check->execute([$id]);
if ($check->fetchColumn() > 0) {
    setFlash('error', 'No se puede eliminar: la sede tiene eventos asociados.');
    header('Location: index.php');
    exit;
}

$stmt = $db->prepare("DELETE FROM sedes WHERE id_sede = ?");
$stmt->execute([$id]);

setFlash('success', 'Sede eliminada exitosamente.');
header('Location: index.php');
exit;
