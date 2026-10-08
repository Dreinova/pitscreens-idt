<?php
/**
 * ordenar_contenido.php — Módulo Administrador
 *
 * Guarda el nuevo orden de reproducción de una lista después de arrastrar
 * los elementos en el admin (assets/js/publicaciones.js). Responde JSON.
 *
 * POST:
 *   lista  — ID de `lista-reproduccion`
 *   ids[]  — IDs de `contenido` en el orden nuevo
 */

date_default_timezone_set('America/Bogota');

session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime'  => 7200,
]);
include('Conexion_DB.php');
include('contenido_helpers.php');

header('Content-Type: application/json');

if(!isset($_SESSION['logeado'])){
	http_response_code(401);
	echo json_encode(['ok' => false]);
	exit();
}

$id_lista = (int) ($_POST['lista'] ?? 0);
$ids      = isset($_POST['ids']) && is_array($_POST['ids']) ? $_POST['ids'] : [];

if (!$id_lista || !$ids) {
	http_response_code(400);
	echo json_encode(['ok' => false]);
	exit();
}

// Solo se reordenan elementos que de verdad pertenecen a esa lista.
$posicion = 1;
foreach ($ids as $id) {
	$id = (int) $id;
	mysqli_query($conexion, "UPDATE `contenido` SET `Orden` = '$posicion' WHERE `ID` = $id AND `Lista-Reproduccion` = $id_lista");
	$posicion++;
}

marcar_lista_modificada($conexion, $id_lista);

echo json_encode(['ok' => true]);
