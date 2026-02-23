-- ============================================================
--  SISTEMA DE GESTIÓN DE EVENTOS — EventCore
--  Schema + Seed Data — Importar en phpMyAdmin
--  Base de datos: drop
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
USE `drop`;

-- ── Roles ──
CREATE TABLE roles (
    id_rol        TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_rol    VARCHAR(50)  NOT NULL UNIQUE,
    descripcion   VARCHAR(150) NOT NULL
) ENGINE=InnoDB;

INSERT INTO roles (nombre_rol, descripcion) VALUES
('Administrador', 'Acceso total al sistema'),
('Organizador',   'Gestiona eventos propios'),
('Reportes',      'Solo lectura y generación de reportes');

-- ── Categorías de eventos ──
CREATE TABLE categorias_evento (
    id_categoria  SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(80)  NOT NULL UNIQUE,
    descripcion   VARCHAR(200)
) ENGINE=InnoDB;

INSERT INTO categorias_evento (nombre) VALUES
('Conferencia'), ('Taller'), ('Seminario'), ('Concierto'), ('Deportivo'), ('Cultural');

-- ── Estados de evento ──
CREATE TABLE estados_evento (
    id_estado    TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre       VARCHAR(40) NOT NULL UNIQUE,
    descripcion  VARCHAR(150)
) ENGINE=InnoDB;

INSERT INTO estados_evento (nombre) VALUES
('Borrador'), ('Activo'), ('Agotado'), ('Cancelado'), ('Finalizado');

-- ── Estados de inscripción ──
CREATE TABLE estados_inscripcion (
    id_estado_inscripcion TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre                VARCHAR(40) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO estados_inscripcion (nombre) VALUES
('Pendiente'), ('Confirmada'), ('Cancelada'), ('Lista de espera');

-- ── Métodos de pago ──
CREATE TABLE metodos_pago (
    id_metodo   TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO metodos_pago (nombre) VALUES
('Efectivo'), ('Transferencia bancaria'), ('Tarjeta crédito'), ('Tarjeta débito'), ('PayPal'), ('Otro');

-- ── Estados de pago ──
CREATE TABLE estados_pago (
    id_estado_pago TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(40) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO estados_pago (nombre) VALUES
('Pendiente'), ('Confirmado'), ('Reembolsado'), ('Rechazado');

-- ── Tipos de reporte ──
CREATE TABLE tipos_reporte (
    id_tipo_reporte TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO tipos_reporte (nombre) VALUES
('Reporte de Eventos'), ('Reporte de Asistencia'), ('Reporte Financiero'), ('Reporte por Fecha');

-- ── Formatos de exportación ──
CREATE TABLE formatos_exportacion (
    id_formato TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(20) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO formatos_exportacion (nombre) VALUES ('PDF'), ('Excel'), ('CSV');

-- ── Estados de reporte ──
CREATE TABLE estados_reporte (
    id_estado_reporte TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre            VARCHAR(40) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO estados_reporte (nombre) VALUES ('En proceso'), ('Generado'), ('Error');

-- ── Usuarios admin ──
CREATE TABLE usuarios_admin (
    id_usuario   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_rol       TINYINT UNSIGNED NOT NULL,
    nombres      VARCHAR(80)  NOT NULL,
    apellidos    VARCHAR(80)  NOT NULL,
    email        VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    activo       BOOLEAN NOT NULL DEFAULT TRUE,
    creado_en    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ua_rol FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
) ENGINE=InnoDB;

-- ── Participantes ──
CREATE TABLE participantes (
    id_participante INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombres         VARCHAR(80)  NOT NULL,
    apellidos       VARCHAR(80)  NOT NULL,
    email           VARCHAR(120) NOT NULL UNIQUE,
    telefono        VARCHAR(20),
    documento_id    VARCHAR(30),
    creado_en       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── Sedes ──
CREATE TABLE sedes (
    id_sede      SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre       VARCHAR(120) NOT NULL,
    direccion    VARCHAR(200) NOT NULL,
    ciudad       VARCHAR(80)  NOT NULL,
    pais         VARCHAR(80)  NOT NULL DEFAULT 'Bolivia',
    capacidad    SMALLINT UNSIGNED,
    referencia   VARCHAR(200)
) ENGINE=InnoDB;

-- ── Eventos ──
CREATE TABLE eventos (
    id_evento     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_categoria  SMALLINT UNSIGNED NOT NULL,
    id_estado     TINYINT UNSIGNED  NOT NULL,
    id_sede       SMALLINT UNSIGNED NOT NULL,
    id_organizador INT UNSIGNED     NOT NULL,
    codigo_evento  VARCHAR(20)  NOT NULL UNIQUE,
    titulo         VARCHAR(150) NOT NULL,
    descripcion    TEXT,
    fecha_inicio   DATETIME     NOT NULL,
    fecha_fin      DATETIME     NOT NULL,
    cupo_maximo    SMALLINT UNSIGNED NOT NULL,
    precio_entrada DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    es_gratuito    BOOLEAN NOT NULL DEFAULT FALSE,
    imagen_url     VARCHAR(300),
    creado_en      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_ev_categoria  FOREIGN KEY (id_categoria)  REFERENCES categorias_evento(id_categoria),
    CONSTRAINT fk_ev_estado     FOREIGN KEY (id_estado)     REFERENCES estados_evento(id_estado),
    CONSTRAINT fk_ev_sede       FOREIGN KEY (id_sede)       REFERENCES sedes(id_sede),
    CONSTRAINT fk_ev_organizador FOREIGN KEY (id_organizador) REFERENCES usuarios_admin(id_usuario)
) ENGINE=InnoDB;

-- ── Inscripciones ──
CREATE TABLE inscripciones (
    id_inscripcion       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_evento            INT UNSIGNED     NOT NULL,
    id_participante      INT UNSIGNED     NOT NULL,
    id_estado_inscripcion TINYINT UNSIGNED NOT NULL DEFAULT 1,
    codigo_inscripcion   VARCHAR(30)  NOT NULL UNIQUE,
    fecha_inscripcion    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    asistio              BOOLEAN      NOT NULL DEFAULT FALSE,
    fecha_asistencia     DATETIME,
    observaciones        VARCHAR(300),
    CONSTRAINT fk_ins_evento       FOREIGN KEY (id_evento)        REFERENCES eventos(id_evento),
    CONSTRAINT fk_ins_participante FOREIGN KEY (id_participante)  REFERENCES participantes(id_participante),
    CONSTRAINT fk_ins_estado       FOREIGN KEY (id_estado_inscripcion) REFERENCES estados_inscripcion(id_estado_inscripcion),
    UNIQUE KEY uq_evento_participante (id_evento, id_participante)
) ENGINE=InnoDB;

-- ── Pagos ──
CREATE TABLE pagos (
    id_pago         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_inscripcion  INT UNSIGNED     NOT NULL UNIQUE,
    id_metodo       TINYINT UNSIGNED NOT NULL,
    id_estado_pago  TINYINT UNSIGNED NOT NULL DEFAULT 1,
    monto           DECIMAL(10,2)    NOT NULL,
    referencia_pago VARCHAR(100),
    fecha_pago      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_confirmacion DATETIME,
    CONSTRAINT fk_pago_inscripcion FOREIGN KEY (id_inscripcion) REFERENCES inscripciones(id_inscripcion),
    CONSTRAINT fk_pago_metodo      FOREIGN KEY (id_metodo)      REFERENCES metodos_pago(id_metodo),
    CONSTRAINT fk_pago_estado      FOREIGN KEY (id_estado_pago) REFERENCES estados_pago(id_estado_pago)
) ENGINE=InnoDB;

-- ── Reportes generados ──
CREATE TABLE reportes_generados (
    id_reporte        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_tipo_reporte   TINYINT UNSIGNED NOT NULL,
    id_formato        TINYINT UNSIGNED NOT NULL,
    id_estado_reporte TINYINT UNSIGNED NOT NULL DEFAULT 1,
    id_usuario        INT UNSIGNED     NOT NULL,
    nombre_reporte    VARCHAR(150)     NOT NULL,
    fecha_inicio      DATE,
    fecha_fin         DATE,
    archivo_url       VARCHAR(300),
    fecha_generacion  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rg_tipo    FOREIGN KEY (id_tipo_reporte)   REFERENCES tipos_reporte(id_tipo_reporte),
    CONSTRAINT fk_rg_formato FOREIGN KEY (id_formato)        REFERENCES formatos_exportacion(id_formato),
    CONSTRAINT fk_rg_estado  FOREIGN KEY (id_estado_reporte) REFERENCES estados_reporte(id_estado_reporte),
    CONSTRAINT fk_rg_usuario FOREIGN KEY (id_usuario)        REFERENCES usuarios_admin(id_usuario)
) ENGINE=InnoDB;

-- ── Estadísticas por evento ──
CREATE TABLE estadisticas_evento (
    id_estadistica      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_evento           INT UNSIGNED NOT NULL,
    total_inscritos     SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    total_confirmados   SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    total_cancelados    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    total_asistieron    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ingreso_total       DECIMAL(12,2)     NOT NULL DEFAULT 0.00,
    fecha_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_ee_evento FOREIGN KEY (id_evento) REFERENCES eventos(id_evento),
    UNIQUE KEY uq_estadistica_evento (id_evento)
) ENGINE=InnoDB;

-- ── Estadísticas generales ──
CREATE TABLE estadisticas_generales (
    id_estadistica_general INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    total_eventos          SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    eventos_activos        SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    eventos_cancelados     SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    eventos_finalizados    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    total_asistentes       INT UNSIGNED      NOT NULL DEFAULT 0,
    total_ingresos         DECIMAL(14,2)     NOT NULL DEFAULT 0.00,
    fecha_calculo          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── Reportes financieros ──
CREATE TABLE reportes_financieros (
    id_reporte_financiero INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario            INT UNSIGNED  NOT NULL,
    fecha_inicio          DATE          NOT NULL,
    fecha_fin             DATE          NOT NULL,
    total_ingresos        DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_pagos_pendientes DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_reembolsos      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    fecha_generacion      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rf_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios_admin(id_usuario)
) ENGINE=InnoDB;

-- ── Vistas ──
CREATE OR REPLACE VIEW v_reporte_eventos AS
SELECT
    e.codigo_evento, e.titulo, e.fecha_inicio, e.fecha_fin,
    es.nombre AS estado, c.nombre AS categoria, s.nombre AS sede,
    e.cupo_maximo, COUNT(i.id_inscripcion) AS total_inscritos,
    ROUND(COUNT(i.id_inscripcion) * 100.0 / e.cupo_maximo, 2) AS porcentaje_ocupacion
FROM eventos e
JOIN estados_evento es ON e.id_estado = es.id_estado
JOIN categorias_evento c ON e.id_categoria = c.id_categoria
JOIN sedes s ON e.id_sede = s.id_sede
LEFT JOIN inscripciones i ON e.id_evento = i.id_evento
GROUP BY e.id_evento;

CREATE OR REPLACE VIEW v_reporte_asistencia AS
SELECT
    e.titulo AS nombre_evento, e.fecha_inicio,
    COUNT(i.id_inscripcion) AS total_inscritos,
    SUM(i.asistio) AS total_asistentes,
    COUNT(i.id_inscripcion) - SUM(i.asistio) AS total_ausentes,
    ROUND(SUM(i.asistio) * 100.0 / NULLIF(COUNT(i.id_inscripcion), 0), 2) AS porcentaje_asistencia
FROM eventos e
LEFT JOIN inscripciones i ON e.id_evento = i.id_evento
GROUP BY e.id_evento;

CREATE OR REPLACE VIEW v_reporte_financiero AS
SELECT
    e.titulo AS evento, e.fecha_inicio,
    SUM(CASE WHEN ep.nombre = 'Confirmado' THEN p.monto ELSE 0 END) AS total_recaudado,
    SUM(CASE WHEN ep.nombre = 'Pendiente' THEN p.monto ELSE 0 END) AS pagos_pendientes,
    SUM(CASE WHEN ep.nombre = 'Reembolsado' THEN p.monto ELSE 0 END) AS total_reembolsos
FROM eventos e
LEFT JOIN inscripciones i ON e.id_evento = i.id_evento
LEFT JOIN pagos p ON i.id_inscripcion = p.id_inscripcion
LEFT JOIN estados_pago ep ON p.id_estado_pago = ep.id_estado_pago
GROUP BY e.id_evento;

CREATE OR REPLACE VIEW v_reporte_por_fecha AS
SELECT
    e.titulo, e.fecha_inicio, e.fecha_fin,
    es.nombre AS estado,
    COUNT(i.id_inscripcion) AS total_inscritos,
    SUM(CASE WHEN ep.nombre = 'Confirmado' THEN p.monto ELSE 0 END) AS total_ingresos
FROM eventos e
JOIN estados_evento es ON e.id_estado = es.id_estado
LEFT JOIN inscripciones i ON e.id_evento = i.id_evento
LEFT JOIN pagos p ON i.id_inscripcion = p.id_inscripcion
LEFT JOIN estados_pago ep ON p.id_estado_pago = ep.id_estado_pago
GROUP BY e.id_evento;

-- ── Índices ──
CREATE INDEX idx_eventos_estado      ON eventos(id_estado);
CREATE INDEX idx_eventos_categoria   ON eventos(id_categoria);
CREATE INDEX idx_eventos_fecha       ON eventos(fecha_inicio, fecha_fin);
CREATE INDEX idx_inscripciones_evento ON inscripciones(id_evento);
CREATE INDEX idx_pagos_estado        ON pagos(id_estado_pago);
CREATE INDEX idx_pagos_fecha         ON pagos(fecha_pago);
CREATE INDEX idx_reportes_usuario    ON reportes_generados(id_usuario);
CREATE INDEX idx_reportes_fecha      ON reportes_generados(fecha_generacion);

-- ============================================================
--  DATOS DE PRUEBA (SEED)
-- ============================================================

-- Usuarios admin (las contraseñas se actualizan desde setup.php)
-- Usuario principal: ladmin@gmail.com / 12345
INSERT INTO usuarios_admin (id_rol, nombres, apellidos, email, password_hash, activo) VALUES
(1, 'Admin',  'Principal','ladmin@gmail.com',     'TEMP_HASH_UPDATE_VIA_SETUP', 1),
(2, 'María',  'López',   'maria@eventcore.com',   'TEMP_HASH_UPDATE_VIA_SETUP', 1),
(2, 'Roberto','Fernández','roberto@eventcore.com', 'TEMP_HASH_UPDATE_VIA_SETUP', 1),
(3, 'Ana',    'Torres',  'ana@eventcore.com',     'TEMP_HASH_UPDATE_VIA_SETUP', 1);

-- Sedes
INSERT INTO sedes (nombre, direccion, ciudad, pais, capacidad, referencia) VALUES
('Centro de Convenciones Illimani', 'Av. Camacho #1234',       'La Paz',      'Bolivia', 500, 'Frente a la Plaza del Estudiante'),
('Auditorio Gran Mariscal',         'Calle Sucre #567',        'Sucre',       'Bolivia', 300, 'Centro Histórico'),
('Salón Dorado Hotel Presidente',   'Av. Ballivián #890',      'Cochabamba',  'Bolivia', 200, 'Zona Norte'),
('Teatro al Aire Libre',            'Parque Urbano Central',   'Santa Cruz',  'Bolivia', 1000,'Entrada principal del parque'),
('Sala de Conferencias TechHub',    'Calle Comercio #432',     'La Paz',      'Bolivia', 80,  'Edificio Empresarial piso 3'),
('Coliseo Multifuncional',          'Av. América #1100',       'Cochabamba',  'Bolivia', 2000,'Zona sur, cerca del estadio');

-- Eventos
INSERT INTO eventos (id_categoria, id_estado, id_sede, id_organizador, codigo_evento, titulo, descripcion, fecha_inicio, fecha_fin, cupo_maximo, precio_entrada, es_gratuito) VALUES
(1, 2, 1, 2, 'EVT-A1B2C3D4', 'Tech Summit Bolivia 2026',         'Conferencia de tecnología e innovación con speakers internacionales.', '2026-04-15 09:00:00', '2026-04-15 18:00:00', 400, 45.00, 0),
(2, 2, 5, 2, 'EVT-E5F6G7H8', 'Workshop UX/UI Design',            'Taller práctico de diseño de interfaces y experiencia de usuario.',    '2026-04-18 14:00:00', '2026-04-18 18:00:00', 30,  25.00, 0),
(6, 2, 4, 3, 'EVT-I9J0K1L2', 'Festival Cultural de Invierno',     'Música, danza y gastronomía boliviana en el parque central.',          '2026-05-22 10:00:00', '2026-05-23 22:00:00', 800, 0.00,  1),
(4, 5, 4, 3, 'EVT-M3N4O5P6', 'Jazz & Blues Night',                'Noche de jazz y blues con artistas nacionales.',                       '2026-02-10 19:00:00', '2026-02-10 23:00:00', 180, 35.00, 0),
(5, 2, 6, 2, 'EVT-Q7R8S9T0', 'Maratón Solidaria 5K',             'Carrera solidaria a beneficio de niños con cáncer.',                   '2026-06-01 07:00:00', '2026-06-01 12:00:00', 200, 15.00, 0),
(3, 1, 3, 2, 'EVT-U1V2W3X4', 'Seminario Emprendimiento Digital',  'Estrategias digitales para nuevos emprendedores.',                     '2026-07-10 09:00:00', '2026-07-10 17:00:00', 150, 20.00, 0),
(1, 2, 1, 2, 'EVT-Y5Z6A7B8', 'Bootcamp Python 2026',             'Curso intensivo de Python para principiantes e intermedios.',           '2026-05-05 08:00:00', '2026-05-07 17:00:00', 60,  80.00, 0),
(2, 4, 5, 3, 'EVT-C9D0E1F2', 'Taller de Fotografía',             'Aprende fotografía profesional en un solo día.',                       '2026-03-15 10:00:00', '2026-03-15 16:00:00', 25,  30.00, 0);

-- Participantes
INSERT INTO participantes (nombres, apellidos, email, telefono, documento_id) VALUES
('Pedro',    'Quispe',   'pedro.q@email.com',    '71234567', '9876543'),
('Lucía',    'Mamani',   'lucia.m@email.com',    '72345678', '8765432'),
('Diego',    'Vargas',   'diego.v@email.com',    '73456789', '7654321'),
('Camila',   'Rojas',    'camila.r@email.com',   '74567890', '6543210'),
('Andrés',   'Salazar',  'andres.s@email.com',   '75678901', '5432109'),
('Valentina','Flores',   'vale.f@email.com',     '76789012', '4321098'),
('Santiago', 'Herrera',  'santi.h@email.com',    '77890123', '3210987'),
('Isabella', 'Morales',  'isa.m@email.com',      '78901234', '2109876'),
('Mateo',    'Castro',   'mateo.c@email.com',    '79012345', '1098765'),
('Sofía',    'Gutiérrez','sofia.g@email.com',    '70123456', '0987654');

-- Inscripciones
INSERT INTO inscripciones (id_evento, id_participante, id_estado_inscripcion, codigo_inscripcion, asistio) VALUES
(1, 1, 2, 'INS-001A', 0),
(1, 2, 2, 'INS-002B', 0),
(1, 3, 2, 'INS-003C', 0),
(1, 4, 1, 'INS-004D', 0),
(2, 5, 2, 'INS-005E', 0),
(2, 6, 2, 'INS-006F', 0),
(3, 7, 2, 'INS-007G', 0),
(3, 8, 1, 'INS-008H', 0),
(4, 1, 2, 'INS-009I', 1),
(4, 9, 2, 'INS-010J', 1),
(4,10, 2, 'INS-011K', 1),
(5, 2, 2, 'INS-012L', 0),
(5, 3, 1, 'INS-013M', 0),
(7, 4, 2, 'INS-014N', 0),
(7, 5, 2, 'INS-015O', 0);

-- Pagos
INSERT INTO pagos (id_inscripcion, id_metodo, id_estado_pago, monto, referencia_pago) VALUES
(1, 3, 2, 45.00, 'PAY-TXN-001'),
(2, 1, 2, 45.00, 'PAY-TXN-002'),
(3, 2, 1, 45.00, NULL),
(5, 3, 2, 25.00, 'PAY-TXN-004'),
(6, 4, 2, 25.00, 'PAY-TXN-005'),
(9, 1, 2, 35.00, 'PAY-TXN-006'),
(10,1, 2, 35.00, 'PAY-TXN-007'),
(11,3, 2, 35.00, 'PAY-TXN-008'),
(12,2, 1, 15.00, NULL),
(14,5, 2, 80.00, 'PAY-TXN-010'),
(15,3, 2, 80.00, 'PAY-TXN-011');

SET FOREIGN_KEY_CHECKS = 1;
