-- migracion_v2.sql
-- Agrega: contenido programado por módulo específico, y configuración del
-- protector de pantalla (tiempos de inactividad + contenido de respaldo).
--
-- Aplicar sobre una base ya creada con idt_app.sql:
--   mysql -u root idt_app < migracion_v2.sql
-- (o, en producción, vía docker exec mariadb mysql ... < migracion_v2.sql)

SET NAMES utf8mb4;

-- --------------------------------------------------------
-- 1. Contenido por módulo: lista-reproduccion puede atarse a un módulo
--    específico (por nombre, igual que visitantes.modulo/reco.modulo).
--    NULL = general (comportamiento actual, sin cambios).
-- --------------------------------------------------------

ALTER TABLE `lista-reproduccion`
  ADD COLUMN `Modulo` VARCHAR(150) NULL DEFAULT NULL AFTER `Nombre`;

-- --------------------------------------------------------
-- 2. Configuración del protector de pantalla (fila única, mismo patrón
--    que la tabla `frame`).
-- --------------------------------------------------------

CREATE TABLE `configuracion` (
  `ID` int(11) NOT NULL,
  `Tiempo_Inactividad_Kiosco` int(11) NOT NULL DEFAULT 60,
  `Tiempo_Inactividad_Contenido` int(11) NOT NULL DEFAULT 900,
  `Protector_URL` varchar(150) NOT NULL DEFAULT 'IDT.mp4',
  `Protector_Tipo` varchar(10) NOT NULL DEFAULT 'video',
  `Fecha-Modificacion` datetime NOT NULL,
  `Usuario` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `configuracion`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

INSERT INTO `configuracion`
  (`ID`, `Tiempo_Inactividad_Kiosco`, `Tiempo_Inactividad_Contenido`, `Protector_URL`, `Protector_Tipo`, `Fecha-Modificacion`, `Usuario`)
VALUES
  (1, 60, 900, 'IDT.mp4', 'video', NOW(), 'Admin');
