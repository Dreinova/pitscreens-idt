-- migracion_v3.sql
-- Agrega: configuración de protector por pantalla específica (mismo patrón
-- general/específico que lista-reproduccion.Modulo), y una tabla de eventos
-- de pantalla para métricas de uso (inicio de reproducción por pantalla).
--
-- Aplicar sobre una base ya migrada con migracion_v2.sql:
--   mysql -u root idt_app < migracion_v3.sql
-- (o, en producción, vía docker exec mariadb mysql ... < migracion_v3.sql)

SET NAMES utf8mb4;

-- --------------------------------------------------------
-- 1. Configuración por pantalla: igual patrón que lista-reproduccion.Modulo.
--    NULL = configuración general (fallback, comportamiento actual).
-- --------------------------------------------------------

ALTER TABLE `configuracion`
  ADD COLUMN `Modulo` VARCHAR(150) NULL DEFAULT NULL AFTER `ID`;

-- --------------------------------------------------------
-- 2. Eventos de pantalla: un registro por cada toque real de "Toca la
--    pantalla para comenzar". Base para el futuro módulo de Informes
--    (inicios por pantalla, totales, fecha/hora de cada evento).
-- --------------------------------------------------------

CREATE TABLE `eventos_pantalla` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Modulo` varchar(150) NOT NULL,
  `Evento` varchar(50) NOT NULL,
  `Fecha_Hora` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `idx_eventos_pantalla_modulo` (`Modulo`),
  KEY `idx_eventos_pantalla_fecha` (`Fecha_Hora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
