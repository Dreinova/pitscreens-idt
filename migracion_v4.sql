-- migracion_v4.sql
-- Agrega la URL de destino a la que navega el kiosco cuando el visitante
-- toca la pantalla — mismo patrón general/específico que ya usa
-- Protector_URL (NULL en Modulo = general, con nombre = override de esa
-- pantalla). Vacío ('') = sin configurar todavía, el toque no hace nada.
--
-- Aplicar sobre una base ya migrada con migracion_v3.sql:
--   mysql -u root idt_app < migracion_v4.sql

SET NAMES utf8mb4;

ALTER TABLE `configuracion`
  ADD COLUMN `URL_Destino` VARCHAR(255) NOT NULL DEFAULT '' AFTER `Protector_Tipo`;
