<?php
session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime'  => 7200,
]);
include('Conexion_DB.php');

if(!isset($_SESSION['logeado'])){
	header('Location: ../../index.php');
	exit();
}

$redirect = $_SERVER['HTTP_REFERER'] ?? '../../modulos.php';

if (isset($_GET['id'])){
	$id = (int) $_GET['id'];

	// La relación con lista-reproduccion/configuracion es por nombre (no
	// hay FK), así que hay que limpiar lo propio de esta pantalla antes de
	// que el nombre se pierda — si no, queda contenido/protector huérfano
	// inalcanzable para siempre.
	$consulta_modulo = mysqli_query($conexion, "SELECT nombre_modulo FROM modulos WHERE ID = $id");
	$datos_modulo    = $consulta_modulo ? mysqli_fetch_array($consulta_modulo) : null;

	if ($datos_modulo && !empty($datos_modulo['nombre_modulo'])) {
		$nombre_escaped = mysqli_real_escape_string($conexion, $datos_modulo['nombre_modulo']);

		$listas = mysqli_query($conexion, "SELECT ID FROM `lista-reproduccion` WHERE `Modulo` = '$nombre_escaped'");
		if ($listas) {
			while ($lista = mysqli_fetch_array($listas)) {
				$lista_id = (int) $lista['ID'];
				mysqli_query($conexion, "DELETE FROM contenido WHERE `Lista-Reproduccion` = $lista_id");
			}
		}
		mysqli_query($conexion, "DELETE FROM `lista-reproduccion` WHERE `Modulo` = '$nombre_escaped'");
		mysqli_query($conexion, "DELETE FROM `configuracion` WHERE `Modulo` = '$nombre_escaped'");
	}

	mysqli_query($conexion, "DELETE FROM modulos WHERE ID = $id");
}

header("Location: " . $redirect);
exit();
?>