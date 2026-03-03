<?php

namespace EventCore\Controllers;

use EventCore\Config\Database;
use EventCore\Helpers\SessionHelper;

class ParticipanteController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index()
    {
        SessionHelper::requireLogin();

        $search = trim($_GET['q'] ?? '');
        $sql    = "SELECT * FROM participantes WHERE 1=1";
        $params = [];

        if ($search) {
            $sql   .= " AND (nombres LIKE ? OR apellidos LIKE ? OR email LIKE ? OR documento_id LIKE ?)";
            $like   = "%{$search}%";
            $params = [$like, $like, $like, $like];
        }

        $sql .= " ORDER BY apellidos ASC, nombres ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $participantes = $stmt->fetchAll();

        require ROOT_PATH . '/src/Views/participantes/index.php';
    }

    public function create()
    {
        SessionHelper::requireLogin();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombres     = trim($_POST['nombres']     ?? '');
            $apellidos   = trim($_POST['apellidos']   ?? '');
            $email       = trim($_POST['email']       ?? '');
            $telefono    = trim($_POST['telefono']    ?? '');
            $documento   = trim($_POST['documento_id']?? '');

            if (empty($nombres))   $errors[] = 'El nombre es obligatorio.';
            if (empty($apellidos)) $errors[] = 'El apellido es obligatorio.';
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
                $errors[] = 'Ingrese un email válido.';

            if (empty($errors)) {
                // Verificar email único
                $chk = $this->db->prepare("SELECT id_participante FROM participantes WHERE email = ?");
                $chk->execute([$email]);
                if ($chk->fetch()) {
                    $errors[] = "Ya existe un participante con el email {$email}.";
                }
            }

            if (empty($errors)) {
                $stmt = $this->db->prepare(
                    "INSERT INTO participantes (nombres, apellidos, email, telefono, documento_id)
                     VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->execute([$nombres, $apellidos, $email, $telefono, $documento]);

                SessionHelper::setFlash('success', "Participante «{$nombres} {$apellidos}» registrado correctamente.");
                header('Location: ' . BASE_URL . '/participantes');
                exit;
            }
        }

        require ROOT_PATH . '/src/Views/participantes/crear.php';
    }

    public function edit($id)
    {
        SessionHelper::requireLogin();
        $id = (int)$id;

        $stmt = $this->db->prepare("SELECT * FROM participantes WHERE id_participante = ?");
        $stmt->execute([$id]);
        $participante = $stmt->fetch();

        if (!$participante) {
            SessionHelper::setFlash('error', 'Participante no encontrado.');
            header('Location: ' . BASE_URL . '/participantes');
            exit;
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombres   = trim($_POST['nombres']      ?? '');
            $apellidos = trim($_POST['apellidos']    ?? '');
            $email     = trim($_POST['email']        ?? '');
            $telefono  = trim($_POST['telefono']     ?? '');
            $documento = trim($_POST['documento_id'] ?? '');

            if (empty($nombres))   $errors[] = 'El nombre es obligatorio.';
            if (empty($apellidos)) $errors[] = 'El apellido es obligatorio.';
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
                $errors[] = 'Ingrese un email válido.';

            if (empty($errors)) {
                $chk = $this->db->prepare("SELECT id_participante FROM participantes WHERE email = ? AND id_participante != ?");
                $chk->execute([$email, $id]);
                if ($chk->fetch()) {
                    $errors[] = "El email {$email} ya pertenece a otro participante.";
                }
            }

            if (empty($errors)) {
                $stmt = $this->db->prepare(
                    "UPDATE participantes SET nombres=?, apellidos=?, email=?, telefono=?, documento_id=?
                     WHERE id_participante=?"
                );
                $stmt->execute([$nombres, $apellidos, $email, $telefono, $documento, $id]);

                SessionHelper::setFlash('success', "Participante actualizado correctamente.");
                header('Location: ' . BASE_URL . '/participantes');
                exit;
            }
        }

        require ROOT_PATH . '/src/Views/participantes/editar.php';
    }

    public function delete($id)
    {
        SessionHelper::requireLogin();
        $id = (int)$id;

        try {
            $stmt = $this->db->prepare("DELETE FROM participantes WHERE id_participante = ?");
            $stmt->execute([$id]);
            SessionHelper::setFlash('success', 'Participante eliminado correctamente.');
        } catch (\PDOException $e) {
            SessionHelper::setFlash('error', 'No se puede eliminar: el participante tiene inscripciones asociadas.');
        }

        header('Location: ' . BASE_URL . '/participantes');
        exit;
    }
}
