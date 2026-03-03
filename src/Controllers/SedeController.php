<?php

namespace EventCore\Controllers;

use EventCore\Config\Database;
use EventCore\Helpers\SessionHelper;

class SedeController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        SessionHelper::requireLogin();
        $sedes = $this->db->query("SELECT * FROM sedes ORDER BY nombre ASC")->fetchAll();
        require ROOT_PATH . '/src/Views/sedes/index.php';
    }

    public function create() {
        SessionHelper::requireLogin();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre    = trim($_POST['nombre'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $ciudad    = trim($_POST['ciudad'] ?? '');
            $pais      = trim($_POST['pais'] ?? 'Bolivia');
            $capacidad = (int)($_POST['capacidad'] ?? 0);

            if (empty($nombre))    $errors[] = 'El nombre es obligatorio.';
            if (empty($direccion)) $errors[] = 'La dirección es obligatoria.';
            if (empty($ciudad))    $errors[] = 'La ciudad es obligatoria.';
            if ($capacidad < 1)    $errors[] = 'La capacidad debe ser mayor a 0.';

            if (empty($errors)) {
                $stmt = $this->db->prepare("INSERT INTO sedes (nombre, direccion, ciudad, pais, capacidad) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $direccion, $ciudad, $pais, $capacidad]);

                SessionHelper::setFlash('success', "Sede «{$nombre}» creada correctamente.");
                header('Location: ' . BASE_URL . '/sedes');
                exit;
            }
        }
        require ROOT_PATH . '/src/Views/sedes/crear.php';
    }

    public function edit($id) {
        SessionHelper::requireLogin();
        $id = (int)$id;

        $stmt = $this->db->prepare("SELECT * FROM sedes WHERE id_sede = ?");
        $stmt->execute([$id]);
        $sede = $stmt->fetch();

        if (!$sede) {
            SessionHelper::setFlash('error', 'Sede no encontrada.');
            header('Location: ' . BASE_URL . '/sedes');
            exit;
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre    = trim($_POST['nombre'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $ciudad    = trim($_POST['ciudad'] ?? '');
            $pais      = trim($_POST['pais'] ?? 'Bolivia');
            $capacidad = (int)($_POST['capacidad'] ?? 0);

            if (empty($nombre))    $errors[] = 'El nombre es obligatorio.';
            if (empty($direccion)) $errors[] = 'La dirección es obligatoria.';
            if (empty($ciudad))    $errors[] = 'La ciudad es obligatoria.';

            if (empty($errors)) {
                $stmt = $this->db->prepare("UPDATE sedes SET nombre=?, direccion=?, ciudad=?, pais=?, capacidad=? WHERE id_sede=?");
                $stmt->execute([$nombre, $direccion, $ciudad, $pais, $capacidad, $id]);

                SessionHelper::setFlash('success', "Sede actualizada correctamente.");
                header('Location: ' . BASE_URL . '/sedes');
                exit;
            }
        }
        require ROOT_PATH . '/src/Views/sedes/editar.php';
    }

    public function delete($id) {
        SessionHelper::requireLogin();
        $id = (int)$id;

        try {
            $stmt = $this->db->prepare("DELETE FROM sedes WHERE id_sede = ?");
            $stmt->execute([$id]);
            SessionHelper::setFlash('success', "Sede eliminada correctamente.");
        } catch (\PDOException $e) {
            SessionHelper::setFlash('error', 'No se puede eliminar la sede porque tiene eventos asociados.');
        }

        header('Location: ' . BASE_URL . '/sedes');
        exit;
    }
}
