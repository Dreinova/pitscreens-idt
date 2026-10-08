<?php
/**
 * publicar_contenido.php — Módulo Administrador
 *
 * Publica un archivo de la biblioteca en una o varias pantallas a la vez
 * (y/o en la lista General), con una ventana opcional de publicación. Si
 * el archivo ya estaba en esa pantalla, en vez de duplicarlo le actualiza
 * la ventana y lo reactiva.
 *
 * POST:
 *   biblioteca_id  — ID en `biblioteca`
 *   destinos[]     — 'general' y/o IDs de `modulos`
 *   fecha_inicio   — datetime-local, vacío = desde ya
 *   fecha_fin      — datetime-local, vacío = sin fin
 */

date_default_timezone_set('America/Bogota');

session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime'  => 7200,
]);
include('Conexion_DB.php');
include('contenido_helpers.php');

if(!isset($_SESSION['logeado'])){
	header('Location: ../../index.php');
	exit();
}

$id_usuario = (int) $_SESSION['id_Correo'];
$sql_usuario = mysqli_query($conexion, "SELECT Nombre FROM usuarios WHERE ID = $id_usuario");
$usuario     = ($u = mysqli_fetch_array($sql_usuario)) ? $u['Nombre'] : '';

$redirect = '../../contenido.php';

$id_biblioteca = (int) ($_POST['biblioteca_id'] ?? 0);
$sql_archivo   = mysqli_query($conexion, "SELECT `URL`, `Tipo` FROM `biblioteca` WHERE `ID` = $id_biblioteca");
$archivo       = $sql_archivo ? mysqli_fetch_array($sql_archivo) : null;
$destinos      = isset($_POST['destinos']) && is_array($_POST['destinos']) ? $_POST['destinos'] : [];

$fecha_inicio = fecha_sql($_POST['fecha_inicio'] ?? '');
$fecha_fin    = fecha_sql($_POST['fecha_fin'] ?? '');

if (!$archivo || !$destinos) {
	$_SESSION['flash_contenido'] = ['tipo' => 'warning', 'mensaje' => 'Selecciona al menos una pantalla donde publicar.'];
	header('Location: ' . $redirect);
	exit();
}
if ($fecha_inicio !== 'NULL' && $fecha_fin !== 'NULL' && $fecha_fin <= $fecha_inicio) {
	$_SESSION['flash_contenido'] = ['tipo' => 'warning', 'mensaje' => 'La fecha de fin debe ser posterior a la de inicio.'];
	header('Location: ' . $redirect);
	exit();
}

$publicados = [];
$fallidos   = [];

foreach ($destinos as $destino) {
	if ($destino === 'general') {
		$id_lista = lista_general($conexion, $usuario);
		$nombre   = 'General';
	} else {
		$id_modulo  = (int) $destino;
		$sql_modulo = mysqli_query($conexion, "SELECT `nombre_modulo` FROM `modulos` WHERE `ID` = $id_modulo");
		$modulo     = $sql_modulo ? mysqli_fetch_array($sql_modulo) : null;
		if (!$modulo) { continue; }
		$id_lista = lista_de_pantalla($conexion, $modulo['nombre_modulo'], $usuario);
		$nombre   = $modulo['nombre_modulo'];
	}

	if ($id_lista && publicar_en_lista($conexion, $id_lista, $archivo['URL'], $archivo['Tipo'], $fecha_inicio, $fecha_fin, $usuario)) {
		$publicados[] = $nombre;
	} else {
		$fallidos[] = $nombre;
	}
}

$mensaje = '';
if ($publicados) {
	$mensaje .= '"' . $archivo['URL'] . '" publicado en: ' . implode(', ', $publicados) . '. ';
}
if ($fallidos) {
	$mensaje .= 'No se pudo publicar en: ' . implode(', ', $fallidos) . '.';
}
$_SESSION['flash_contenido'] = ['tipo' => $fallidos ? 'warning' : 'success', 'mensaje' => trim($mensaje)];

header('Location: ' . $redirect);
exit();
