-- ============================================================
-- COLCAMPOS v3.0 - Actualización de base de datos
-- ============================================================
USE sistema_avicola;

-- Eliminar tablas viejas
DROP TABLE IF EXISTS pagos;
DROP TABLE IF EXISTS almacen;

-- ── INVENTARIO (reemplaza almacén) ────────────────────────
CREATE TABLE IF NOT EXISTS inventario (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    id_granja   VARCHAR(30) NOT NULL,
    fecha       DATE        NOT NULL,
    articulo    VARCHAR(120) NOT NULL,
    cantidad    DECIMAL(10,2) DEFAULT 0,
    descripcion VARCHAR(200) DEFAULT '',
    ingreso     DECIMAL(12,2) DEFAULT 0
);

-- ── COSTOS GENERALES (por galpón y granja) ────────────────
CREATE TABLE IF NOT EXISTS costos_generales (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    id_granja   VARCHAR(30) NOT NULL,
    num_galpon  TINYINT     NOT NULL,
    fecha       DATE        NOT NULL,
    concepto    VARCHAR(150) NOT NULL,
    cantidad    DECIMAL(10,2) DEFAULT 0,
    valor       DECIMAL(12,2) DEFAULT 0,
    observaciones VARCHAR(250) DEFAULT ''
);

-- ── CAFÉ - LABORES POR LOTE ───────────────────────────────
CREATE TABLE IF NOT EXISTS cafe_labores (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    num_lote        TINYINT      NOT NULL,
    fecha           DATE         NOT NULL,
    labor_realizada VARCHAR(150) NOT NULL,
    insumo          VARCHAR(120) DEFAULT '',
    valor_insumo    DECIMAL(12,2) DEFAULT 0,
    valor_mano_obra DECIMAL(12,2) DEFAULT 0,
    total           DECIMAL(12,2) DEFAULT 0,
    observaciones   VARCHAR(250) DEFAULT ''
);

-- ── ACTUALIZAR PRODUCCIÓN: agregar columnas cartón ────────
ALTER TABLE registros_produccion
    ADD COLUMN IF NOT EXISTS carton_c   INT DEFAULT 0 AFTER semana_aves,
    ADD COLUMN IF NOT EXISTS carton_b   INT DEFAULT 0 AFTER carton_c,
    ADD COLUMN IF NOT EXISTS carton_a   INT DEFAULT 0 AFTER carton_b,
    ADD COLUMN IF NOT EXISTS carton_aa  INT DEFAULT 0 AFTER carton_a,
    ADD COLUMN IF NOT EXISTS carton_aaa INT DEFAULT 0 AFTER carton_aa,
    ADD COLUMN IF NOT EXISTS carton_jumbo INT DEFAULT 0 AFTER carton_aaa;

SELECT 'Base de datos v3.0 actualizada correctamente' AS resultado;
