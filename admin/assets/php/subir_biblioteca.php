<?php
/**
 * subir_biblioteca.php — Módulo Administrador
 *
 * Sube uno o varios archivos (PNG/JPG/MP4) a la biblioteca de contenido
 * sin publicarlos en ninguna pantalla. Se publican después desde
 * contenido.php con el ícono de publicar de cada archivo.
 *
 * POST: archivos[] (multipart)
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

$errores = [];
$subidos = 0;

if (!empty($_FILES['archivos']['name']) && is_array($_FILES['archivos']['name'])) {
	foreach ($_FILES['archivos']['name'] as $i => $nombre) {
		if ($nombre === '') { continue; }
		$archivo = [
			'name'     => $nombre,
			'type'     => $_FILES['archivos']['type'][$i],
			'tmp_name' => $_FILES['archivos']['tmp_name'][$i],
			'error'    => $_FILES['archivos']['error'][$i],
			'size'     => $_FILES['archivos']['size'][$i],
		];
		$resultado = guardar_subida($conexion, $archivo, $usuario, '../galeria/');
		if (is_array($resultado)) {
			$subidos++;
		} else {
			$errores[] = $resultado;
		}
	}
} else {
	$errores[] = 'No se recibió ningún archivo (puede superar el tamaño máximo del servidor).';
}

$_SESSION['flash_contenido'] = [
	'tipo'    => $errores ? 'warning' : 'success',
	'mensaje' => ($subidos ? "Se subieron $subidos archivo(s) a la biblioteca. " : '') . implode(' ', $errores),
];

header('Location: ../../contenido.php');
exit();
