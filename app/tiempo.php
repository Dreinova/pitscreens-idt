<?php
error_reporting(0);
date_default_timezone_set('America/Bogota');
include '../admin/assets/php/Conexion_DB.php';

$ahora = date("Y-m-d H:i:s"); 

/* Consulta programaciones y verificacion de fechas*/
$sql_hora = mysqli_query($conexion, "SELECT `Fecha-Inicio` FROM `lista-reproduccion` WHERE `Fecha-Inicio` = NOW() ORDER BY `Fecha-Inicio` DESC LIMIT 1");
$rowProgramaciones = mysqli_fetch_array($sql_hora);
$fecha_inicio = $rowProgramaciones['Fecha-Inicio'];

echo $ahora;

if($ahora == $fecha_inicio){
	header("location:activacion.php");
	
}

?>