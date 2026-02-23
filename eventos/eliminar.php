<?php
/**
 * EventCore — Cancelar Evento (eliminación lógica)
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

// Estado 4 = Cancelado
$stmt = $db->prepare("UPDATE eventos SET id_estado = 4 WHERE id_evento = ?");
$stmt->execute([$id]);

setFlash('success', 'Evento cancelado exitosamente.');
header('Location: index.php');
exit;
