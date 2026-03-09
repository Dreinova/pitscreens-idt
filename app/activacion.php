<?php

date_default_timezone_set('America/Bogota');
include '../admin/assets/php/Conexion_DB.php';

$ahora = date("Y-m-d H:i:s");

/* Consulta programaciones y verificacion de fechas*/
$sql_programaciones = mysqli_query($conexion, "SELECT * FROM `lista-reproduccion` WHERE `Fecha-Inicio` =NOW() ORDER BY `Fecha-Inicio` DESC LIMIT 1");
$rowProgramaciones = mysqli_fetch_array($sql_programaciones);
$ID_programaciones = $rowProgramaciones['ID'];
$fecha_inicio = $rowProgramaciones['Fecha-Inicio'];

if($ahora == $fecha_inicio){
	$activar = mysqli_query($conexion,"UPDATE `lista-reproduccion` SET `Estado` = '1' WHERE `lista-reproduccion`.`ID` = $ID_programaciones");
	$desactivar = mysqli_query($conexion, "UPDATE `lista-reproduccion` SET `Estado` = '0' WHERE NOT `lista-reproduccion`.`ID` = $ID_programaciones");
	if($desactivar){
		header("location:index.php");
	}
}

?>