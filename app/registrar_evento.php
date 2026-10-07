<?php
/**
 * registrar_evento.php — Módulo App (Pantalla)
 *
 * Endpoint de métricas: registra un evento de uso de la pantalla actual en
 * `eventos_pantalla`. Pensado para llamarse por POST en segundo plano
 * (fire-and-forget) desde app/index.php al tocar el protector de pantalla —
 * nunca bloquea ni retrasa la reproducción del contenido.
 *
 * Parámetros POST:
 *   evento (string) — nombre del evento, p.ej. "SCREEN_STARTED"
 *
 * Requiere sesión de módulo activa ($_SESSION['modulo']); si no existe, no
 * hay pantalla a la que atribuir el evento y no se registra nada.
 */

error_reporting(0);
date_default_timezone_set('America/Bogota');
session_start();

if (!isset($_SESSION['modulo']) || empty($_SESSION['modulo']) || empty($_POST['evento'])) {
	exit();
}

include '../admin/assets/php/Conexion_DB.php';

$modulo = mysqli_real_escape_string($conexion, $_SESSION['modulo']);
$evento = mysqli_real_escape_string($conexion, $_POST['evento']);
$ahora  = date("Y-m-d H:i:s");

mysqli_query($conexion,
    "INSERT INTO eventos_pantalla (Modulo, Evento, Fecha_Hora) VALUES ('$modulo', '$evento', '$ahora')"
);

mysqli_close($conexion);
?>
