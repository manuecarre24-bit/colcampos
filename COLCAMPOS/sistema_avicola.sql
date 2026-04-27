-- ============================================================
-- COLCAMPOS v2.0 - Base de datos: sistema_avicola
-- ============================================================

CREATE DATABASE IF NOT EXISTS sistema_avicola CHARACTER SET utf8 COLLATE utf8_general_ci;
USE sistema_avicola;

-- ── USUARIOS ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS usuarios (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    usuario  VARCHAR(60) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Usuario por defecto (cambiar contraseña en producción)
INSERT IGNORE INTO usuarios (usuario, password) VALUES ('admin', '1234');

-- ── REGISTROS DE PRODUCCIÓN ───────────────────────────────
CREATE TABLE IF NOT EXISTS registros_produccion (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    id_granja   VARCHAR(30)  NOT NULL,
    num_galpon  TINYINT      NOT NULL,
    fecha       DATE         NOT NULL,
    semana_aves INT          DEFAULT 0,
    c           INT          DEFAULT 0,
    b           INT          DEFAULT 0,
    a           INT          DEFAULT 0,
    aa          INT          DEFAULT 0,
    aaa         INT          DEFAULT 0,
    jumbo       INT          DEFAULT 0,
    averias     INT          DEFAULT 0,
    total_huevos INT         DEFAULT 0,
    mortalidad  INT          DEFAULT 0,
    saldo_aves  INT          DEFAULT 0,
    UNIQUE KEY uk_prod (id_granja, num_galpon, fecha)
);

-- ── REGISTROS DE ALIMENTACIÓN ─────────────────────────────
CREATE TABLE IF NOT EXISTS registros_alimentacion (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    id_granja         VARCHAR(30)    NOT NULL,
    num_galpon        TINYINT        NOT NULL,
    fecha             DATE           NOT NULL,
    cantidad_alimento DECIMAL(10,2)  DEFAULT 0,
    gramos_por_ave    DECIMAL(10,2)  DEFAULT 0,
    valor_unitario    DECIMAL(12,2)  DEFAULT 0,
    valor_total       DECIMAL(14,2)  DEFAULT 0
);

-- ── ALMACÉN ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS almacen (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    id_granja           VARCHAR(30)   NOT NULL,
    categoria           VARCHAR(50)   NOT NULL,
    nombre_articulo     VARCHAR(120)  NOT NULL,
    cantidad_actual     DECIMAL(10,2) DEFAULT 0,
    unidad_medida       VARCHAR(30)   DEFAULT '',
    stock_minimo        DECIMAL(10,2) DEFAULT 0,
    fecha_actualizacion DATETIME      DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_almacen (id_granja, nombre_articulo)
);

-- ── PAGOS / NÓMINA ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS pagos (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    id_granja        VARCHAR(30)    NOT NULL,
    nombre_empleado  VARCHAR(120)   NOT NULL,
    periodo_pago     VARCHAR(50)    DEFAULT '',
    sueldo_base      DECIMAL(12,2)  DEFAULT 0,
    bonos            DECIMAL(12,2)  DEFAULT 0,
    descuentos       DECIMAL(12,2)  DEFAULT 0,
    total_neto       DECIMAL(12,2)  DEFAULT 0,
    fecha_registro   DATETIME       DEFAULT CURRENT_TIMESTAMP
);
