-- migracion_v5.sql
-- Agrega: biblioteca de archivos (subir sin publicar todavía) y ventana de
-- publicación por contenido (Fecha_Inicio / Fecha_Fin) para que el kiosco
-- publique y despublique automáticamente.
--
-- Aplicar sobre una base ya migrada con migracion_v4.sql:
--   mysql -u root idt_app < migracion_v5.sql
-- (o, en producción, vía docker exec mariadb mysql ... < migracion_v5.sql)

SET NAMES utf8mb4;

-- --------------------------------------------------------
-- 1. Ventana de publicación de cada contenido. NULL = sin límite por ese
--    lado (comportamiento actual: siempre visible mientras Estado = 1).
--    De paso se amplía URL: 50 caracteres cortaba nombres de archivo
--    largos y dejaba la referencia rota.
-- --------------------------------------------------------

ALTER TABLE `contenido`
  MODIFY `URL` VARCHAR(255) NOT NULL,
  ADD COLUMN `Fecha_Inicio` DATETIME NULL DEFAULT NULL AFTER `Orden`,
  ADD COLUMN `Fecha_Fin` DATETIME NULL DEFAULT NULL AFTER `Fecha_Inicio`;

-- --------------------------------------------------------
-- 2. Biblioteca: un registro por archivo subido a admin/assets/galeria/,
--    publicado o no. `contenido` sigue referenciándolo por nombre (URL).
-- --------------------------------------------------------

CREATE TABLE `biblioteca` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `URL` varchar(255) NOT NULL,
  `Tipo` varchar(10) NOT NULL,
  `Fecha_Subida` datetime NOT NULL,
  `Usuario` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `uq_biblioteca_url` (`URL`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Poblarla con los archivos que ya están publicados en alguna lista.
INSERT IGNORE INTO `biblioteca` (`URL`, `Tipo`, `Fecha_Subida`, `Usuario`)
SELECT `URL`, MIN(`Tipo`), MIN(`Fecha_Modificación`), MIN(`Usuario`)
FROM `contenido`
GROUP BY `URL`;
