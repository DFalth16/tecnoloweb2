<?php

namespace EventCore\Controllers;

use EventCore\Services\InscripcionService;
use EventCore\Repositories\InscripcionRepository;
use EventCore\Services\Observers\EmailObserver;
use EventCore\Services\Observers\LogObserver;
use EventCore\Services\Observers\QuotaObserver;
use EventCore\Config\Database;
use EventCore\Helpers\SessionHelper;

/**
 * Clase InscripcionController
 * Recibe peticiones de inscripciones.
 */
class InscripcionController
{
    private $service;
    private $db;

    public function __construct()
    {
        $this->db   = Database::getInstance()->getConnection();
        $repository = new InscripcionRepository($this->db);
        $this->service = new InscripcionService($repository);

        // Adjuntar observadores
        $this->service->attach(new EmailObserver());
        $this->service->attach(new LogObserver());
        $this->service->attach(new QuotaObserver());
    }

    public function store(array $requestData)
    {
        try {
            $inscripcion = $this->service->createInscripcion($requestData);
            return [
                'status'  => 'success',
                'message' => 'Inscripción creada exitosamente',
                'data'    => $inscripcion
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * US3 + US4: Ver y gestionar inscritos de un evento (admin).
     * GET  → muestra la lista de inscritos + formulario de inscripción manual.
     * POST → inscribe manualmente a un participante buscado por email.
     */
    public function inscritos($id_evento)
    {
        SessionHelper::requireLogin();
        $id_evento = (int)$id_evento;

        // Cargar evento
        $stmt = $this->db->prepare(
            "SELECT e.*, s.nombre AS sede, ee.nombre AS estado,
                    (SELECT COUNT(*) FROM inscripciones WHERE id_evento = e.id_evento) AS total_inscritos
             FROM eventos e
             JOIN sedes s ON e.id_sede = s.id_sede
             JOIN estados_evento ee ON e.id_estado = ee.id_estado
             WHERE e.id_evento = ?"
        );
        $stmt->execute([$id_evento]);
        $evento = $stmt->fetch();

        if (!$evento) {
            SessionHelper::setFlash('error', 'Evento no encontrado.');
            header('Location: ' . BASE_URL . '/eventos');
            exit;
        }

        $errors = [];

        // POST → inscripción manual
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email_participante'] ?? '');

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Ingrese un email válido del participante.';
            } else {
                // Buscar participante
                $pStmt = $this->db->prepare("SELECT * FROM participantes WHERE email = ?");
                $pStmt->execute([$email]);
                $participante = $pStmt->fetch();

                if (!$participante) {
                    $errors[] = "No existe un participante con el email «{$email}». Primero regístrelo en el módulo de Participantes.";
                } else {
                    // Verificar aforo
                    $stmt2 = $this->db->prepare("SELECT COUNT(*) AS total FROM inscripciones WHERE id_evento = ?");
                    $stmt2->execute([$id_evento]);
                    $total = (int)$stmt2->fetch()['total'];

                    if ($total >= $evento['cupo_maximo']) {
                        $errors[] = 'El evento ha alcanzado su aforo máximo.';
                    } else {
                        // Verificar si ya está inscrito
                        $chk = $this->db->prepare(
                            "SELECT id_inscripcion FROM inscripciones WHERE id_evento=? AND id_participante=?"
                        );
                        $chk->execute([$id_evento, $participante['id_participante']]);
                        if ($chk->fetch()) {
                            $errors[] = "«{$participante['nombres']} {$participante['apellidos']}» ya está inscrito en este evento.";
                        }
                    }
                }
            }

            if (empty($errors)) {
                $codigo = 'INS-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
                $ins = $this->db->prepare(
                    "INSERT INTO inscripciones (id_evento, id_participante, id_estado_inscripcion, codigo_inscripcion)
                     VALUES (?, ?, 1, ?)"
                );
                $ins->execute([$id_evento, $participante['id_participante'], $codigo]);

                SessionHelper::setFlash('success',
                    "«{$participante['nombres']} {$participante['apellidos']}» inscrito correctamente. Código: {$codigo}");
                header('Location: ' . BASE_URL . '/eventos?action=inscritos&id=' . $id_evento);
                exit;
            }
        }

        // Cargar lista de inscritos
        $insStmt = $this->db->prepare(
            "SELECT i.*, p.nombres, p.apellidos, p.email, p.telefono,
                    ei.nombre AS estado_inscripcion
             FROM inscripciones i
             JOIN participantes p ON i.id_participante = p.id_participante
             JOIN estados_inscripcion ei ON i.id_estado_inscripcion = ei.id_estado_inscripcion
             WHERE i.id_evento = ?
             ORDER BY i.fecha_inscripcion DESC"
        );
        $insStmt->execute([$id_evento]);
        $inscritos = $insStmt->fetchAll();

        require ROOT_PATH . '/src/Views/eventos/inscritos.php';
    }
}
