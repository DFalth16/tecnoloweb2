<?php

namespace EventCore\Controllers;

use EventCore\Config\Database;
use EventCore\Helpers\SessionHelper;
use EventCore\Helpers\Validator;

class EventoController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        SessionHelper::requireLogin();

        // Filtros
        $search    = trim($_GET['q'] ?? '');
        $catFilter = $_GET['cat'] ?? '';
        $estFilter = $_GET['estado'] ?? '';

        $sql = "SELECT e.*, ee.nombre AS estado, ce.nombre AS categoria, s.nombre AS sede,
                ua.nombres AS org_nombres, ua.apellidos AS org_apellidos,
                (SELECT COUNT(*) FROM inscripciones i WHERE i.id_evento = e.id_evento) AS inscritos
                FROM eventos e
                JOIN estados_evento ee ON e.id_estado = ee.id_estado
                JOIN categorias_evento ce ON e.id_categoria = ce.id_categoria
                JOIN sedes s ON e.id_sede = s.id_sede
                JOIN usuarios_admin ua ON e.id_organizador = ua.id_usuario
                WHERE 1=1";
        $params = [];

        if ($search) {
            $sql .= " AND (e.titulo LIKE ? OR e.codigo_evento LIKE ? OR s.nombre LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($catFilter) {
            $sql .= " AND e.id_categoria = ?";
            $params[] = $catFilter;
        }
        if ($estFilter) {
            $sql .= " AND e.id_estado = ?";
            $params[] = $estFilter;
        }

        $sql .= " ORDER BY e.fecha_inicio DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $eventos = $stmt->fetchAll();

        $categorias = $this->db->query("SELECT * FROM categorias_evento ORDER BY nombre")->fetchAll();
        $estados    = $this->db->query("SELECT * FROM estados_evento ORDER BY id_estado")->fetchAll();

        require ROOT_PATH . '/src/Views/eventos/index.php';
    }

    public function create() {
        SessionHelper::requireLogin();
        
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
            
            if (empty($titulo))      $errors[] = 'El título es obligatorio.';
            if ($id_categoria < 1)   $errors[] = 'Seleccione una categoría.';
            if ($id_sede < 1)        $errors[] = 'Seleccione una sede.';
            if (empty($fecha_inicio)) $errors[] = 'La fecha de inicio es obligatoria.';
            if (empty($fecha_fin))   $errors[] = 'La fecha de fin es obligatoria.';
            if ($cupo_maximo < 1)    $errors[] = 'El cupo máximo debe ser mayor a 0.';
            
            if (empty($errors) && $fecha_fin <= $fecha_inicio) {
                $errors[] = 'La fecha de fin debe ser posterior a la de inicio.';
            }

            if ($es_gratuito) $precio = 0.00;
            
            if (empty($errors)) {
                $codigo = 'EVT-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
                $stmt = $this->db->prepare("
                    INSERT INTO eventos (id_categoria, id_estado, id_sede, id_organizador, codigo_evento, titulo, descripcion, fecha_inicio, fecha_fin, cupo_maximo, precio_entrada, es_gratuito)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $id_categoria, $id_estado, $id_sede, SessionHelper::get('user_id'),
                    $codigo, $titulo, $descripcion,
                    $fecha_inicio, $fecha_fin,
                    $cupo_maximo, $precio, $es_gratuito
                ]);
                
                SessionHelper::setFlash('success', "Evento «{$titulo}» creado exitosamente.");
                header('Location: ' . BASE_URL . '/eventos');
                exit;
            }
        }

        $categorias = $this->db->query("SELECT * FROM categorias_evento ORDER BY nombre")->fetchAll();
        $estados    = $this->db->query("SELECT * FROM estados_evento ORDER BY id_estado")->fetchAll();
        $sedes      = $this->db->query("SELECT * FROM sedes ORDER BY nombre")->fetchAll();

        require ROOT_PATH . '/src/Views/eventos/crear.php';
    }

    public function edit($id) {
        SessionHelper::requireLogin();
        $id = (int)$id;

        $stmt = $this->db->prepare("SELECT * FROM eventos WHERE id_evento = ?");
        $stmt->execute([$id]);
        $evento = $stmt->fetch();

        if (!$evento) {
            SessionHelper::setFlash('error', 'Evento no encontrado.');
            header('Location: ' . BASE_URL . '/eventos');
            exit;
        }

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
            
            if (empty($titulo))      $errors[] = 'El título es obligatorio.';
            if ($id_categoria < 1)   $errors[] = 'Seleccione una categoría.';
            
            if (empty($errors)) {
                $stmt = $this->db->prepare("
                    UPDATE eventos SET titulo=?, descripcion=?, id_categoria=?, id_estado=?, id_sede=?,
                    fecha_inicio=?, fecha_fin=?, cupo_maximo=?, precio_entrada=?, es_gratuito=?
                    WHERE id_evento=?
                ");
                $stmt->execute([
                    $titulo, $descripcion, $id_categoria, $id_estado, $id_sede,
                    $fecha_inicio, $fecha_fin, $cupo_maximo, $precio, $es_gratuito, $id
                ]);
                
                SessionHelper::setFlash('success', "Evento «{$titulo}» actualizado exitosamente.");
                header('Location: ' . BASE_URL . '/eventos');
                exit;
            }
        } else {
            $_POST = $evento;
            $_POST['fecha_inicio'] = date('Y-m-d\TH:i', strtotime($evento['fecha_inicio']));
            $_POST['fecha_fin']    = date('Y-m-d\TH:i', strtotime($evento['fecha_fin']));
        }

        $categorias = $this->db->query("SELECT * FROM categorias_evento ORDER BY nombre")->fetchAll();
        $estados    = $this->db->query("SELECT * FROM estados_evento ORDER BY id_estado")->fetchAll();
        $sedes      = $this->db->query("SELECT * FROM sedes ORDER BY nombre")->fetchAll();

        require ROOT_PATH . '/src/Views/eventos/editar.php';
    }

    public function delete($id) {
        SessionHelper::requireLogin();
        $id = (int)$id;
        
        $stmt = $this->db->prepare("UPDATE eventos SET id_estado = 4 WHERE id_evento = ?");
        $stmt->execute([$id]);
        
        SessionHelper::setFlash('success', "Evento cancelado correctamente.");
        header('Location: ' . BASE_URL . '/eventos');
        exit;
    }
}
