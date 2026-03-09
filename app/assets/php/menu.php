<?php
error_reporting(0);

//Conexión a la Base de datos ----------------------------------------
include 'Conexion_DB.php';

session_start();

$_SESSION["Usuario"] = "Anonimo";



$user = utf8_encode($_POST["modulo"]);
$user_ID = 0;

$PQRS = utf8_encode($_POST["PQRS"]);
$conceptos_juridicos = utf8_encode($_POST["conceptos_juridicos"]);
$certificados = utf8_encode($_POST["certificados"]);
$info = utf8_encode($_POST["info"]);
$caja = utf8_encode($_POST["caja"]);
$tramite = utf8_encode($_POST["tramite"]);
$kids = utf8_encode($_POST["kids"]);
$fecha_inicio = utf8_encode(date("Y-m-d H:i:s"));

			 
	//Escritura en base de datos--------------------------------------------

if($PQRS){
	$guardar_calificador = mysqli_query($conexion,"INSERT INTO `recorrido_app` ( `usuario`, `id_usuario`, `recorrido`, `fecha`) VALUES ('$user', '$user_ID', '$PQRS', '$fecha_inicio')");
	if ($guardar_calificador) {
    	header('Location: ../../pqrs.php');
    }
	else {
		echo "Error: " . $sql . "" . mysqli_error($conexion);
    }
}

if($conceptos_juridicos){
	$guardar_calificador = mysqli_query($conexion,"INSERT INTO `recorrido_app` ( `usuario`, `id_usuario`, `recorrido`, `fecha`) VALUES ('$user', '$user_ID', '$conceptos_juridicos', '$fecha_inicio')");
	if ($guardar_calificador) {
    	header('Location: ../../concepto_juridico.php');
    }
	else {
		echo "Error: " . $sql . "" . mysqli_error($conexion);
    }
}

if($certificados){
	$guardar_calificador = mysqli_query($conexion,"INSERT INTO `recorrido_app` ( `usuario`, `id_usuario`, `recorrido`, `fecha`) VALUES ('$user', '$user_ID', '$certificados', '$fecha_inicio')");
	if ($guardar_calificador) {
    	header('Location: ../../certificados.php');
    }
	else {
		echo "Error: " . $sql . "" . mysqli_error($conexion);
    }
}

if($info){
	$guardar_calificador = mysqli_query($conexion,"INSERT INTO `recorrido_app` ( `usuario`, `id_usuario`, `recorrido`, `fecha`) VALUES ('$user', '$user_ID', '$info', '$fecha_inicio')");
	if ($guardar_calificador) {
    	header('Location: ../../informacion_supersubsidio.php');
    }
	else {
		echo "Error: " . $sql . "" . mysqli_error($conexion);
    }
}

if($caja){
	$guardar_calificador = mysqli_query($conexion,"INSERT INTO `recorrido_app` ( `usuario`, `id_usuario`, `recorrido`, `fecha`) VALUES ('$user', '$user_ID', '$caja', '$fecha_inicio')");
	if ($guardar_calificador) {
    	header('Location: ../../menu.php');
    }
	else {
		echo "Error: " . $sql . "" . mysqli_error($conexion);
    }
}

if($tramite){
	$guardar_calificador = mysqli_query($conexion,"INSERT INTO `recorrido_app` ( `usuario`, `id_usuario`, `recorrido`, `fecha`) VALUES ('$user', '$user_ID', '$tramite', '$fecha_inicio')");
	if ($guardar_calificador) {
    	header('Location: ../../consulta.php');
    }
	else {
		echo "Error: " . $sql . "" . mysqli_error($conexion);
    }
}

if($kids){
	$guardar_calificador = mysqli_query($conexion,"INSERT INTO `recorrido_app` ( `usuario`, `id_usuario`, `recorrido`, `fecha`) VALUES ('$user', '$user_ID', '$kids', '$fecha_inicio')");
	if ($guardar_calificador) {
    	header('Location: ../../ninos.php');
    }
	else {
		echo "Error: " . $sql . "" . mysqli_error($conexion);
    }
}

$conexion->close();

?>