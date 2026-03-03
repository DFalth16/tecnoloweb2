<?php

namespace EventCore\Controllers;

use EventCore\Config\Database;
use EventCore\Helpers\SessionHelper;

/**
 * Clase DashboardController
 * Genera la vista principal con estadísticas en tiempo real.
 */
class DashboardController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        SessionHelper::requireLogin();

        // Stats reales
        $totalEventos      = $this->db->query("SELECT COUNT(*) FROM eventos")->fetchColumn();
        $eventosActivos    = $this->db->query("SELECT COUNT(*) FROM eventos WHERE id_estado = 2")->fetchColumn();
        $eventosCancelados = $this->db->query("SELECT COUNT(*) FROM eventos WHERE id_estado = 4")->fetchColumn();
        $eventosFinalizados = $this->db->query("SELECT COUNT(*) FROM eventos WHERE id_estado = 5")->fetchColumn();
        $totalAsistentes   = $this->db->query("SELECT COUNT(*) FROM participantes")->fetchColumn();

        $stmtIngresos = $this->db->query("
            SELECT COALESCE(SUM(p.monto), 0) 
            FROM pagos p 
            JOIN estados_pago ep ON p.id_estado_pago = ep.id_estado_pago 
            WHERE ep.nombre = 'Confirmado'
        ");
        $totalIngresos = $stmtIngresos->fetchColumn();

        $nuevosHoy = $this->db->query("
            SELECT COUNT(*) 
            FROM inscripciones 
            WHERE DATE(fecha_inscripcion) = CURDATE()
        ")->fetchColumn();

        $stats = [
            'total_eventos'        => $totalEventos,
            'eventos_activos'      => $eventosActivos,
            'total_asistentes'     => $totalAsistentes,
            'total_ingresos'       => $totalIngresos,
            'eventos_cancelados'   => $eventosCancelados,
            'eventos_finalizados'  => $eventosFinalizados,
            'nuevos_inscritos_hoy' => $nuevosHoy,
            'total_pagos_pendientes' => $this->db->query("SELECT COUNT(*) FROM pagos WHERE id_estado_pago = 1")->fetchColumn()
        ];

        // Eventos recientes
        $eventos = $this->db->query("
            SELECT e.titulo AS nombre, e.fecha_inicio AS fecha, 
                   (SELECT COUNT(*) FROM inscripciones i WHERE i.id_evento = e.id_evento) AS inscritos,
                   e.cupo_maximo AS cupo,
                   ee.nombre AS estado,
                   ce.nombre AS cat
            FROM eventos e
            JOIN estados_evento ee ON e.id_estado = ee.id_estado
            JOIN categorias_evento ce ON e.id_categoria = ce.id_categoria
            ORDER BY e.fecha_inicio DESC
            LIMIT 5
        ")->fetchAll();

        // Actividad reciente
        $actividad = $this->db->query("
            SELECT 
                CASE 
                    WHEN p2.id_pago IS NOT NULL AND ep.nombre = 'Confirmado' THEN 'Pago confirmado'
                    WHEN ei.nombre = 'Cancelada' THEN 'Cancelación'
                    ELSE 'Nueva inscripción'
                END AS accion,
                CONCAT(pa.nombres, ' ', pa.apellidos, ' → ', ev.titulo) AS det,
                i.fecha_inscripcion AS fecha,
                CASE 
                    WHEN p2.id_pago IS NOT NULL AND ep.nombre = 'Confirmado' THEN 'pay'
                    WHEN ei.nombre = 'Cancelada' THEN 'can'
                    ELSE 'ins'
                END AS tipo
            FROM inscripciones i
            JOIN participantes pa ON i.id_participante = pa.id_participante
            JOIN eventos ev ON i.id_evento = ev.id_evento
            JOIN estados_inscripcion ei ON i.id_estado_inscripcion = ei.id_estado_inscripcion
            LEFT JOIN pagos p2 ON i.id_inscripcion = p2.id_inscripcion
            LEFT JOIN estados_pago ep ON p2.id_estado_pago = ep.id_estado_pago
            ORDER BY i.fecha_inscripcion DESC
            LIMIT 6
        ")->fetchAll();

        require ROOT_PATH . '/src/Views/dashboard.php';
    }
}
