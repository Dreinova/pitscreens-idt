-- migracion_v6.sql
-- Agrega el "latido" de cada pantalla: la última vez que su kiosco avisó al
-- servidor que sigue encendido y con conexión (desde app/tiempo.php, sondeado
-- en bucle tanto en index.php como en frame.php). admin/modulos.php lo usa
-- para mostrar un indicador verde/rojo de actividad por pantalla.
--
-- Aplicar sobre una base ya migrada con migracion_v5.sql:
--   mysql -u root idt_app < migracion_v6.sql

SET NAMES utf8mb4;

ALTER TABLE `modulos`
  ADD COLUMN `Ultima_Conexion` DATETIME NULL DEFAULT NULL AFTER `fecha_modificacion`;
