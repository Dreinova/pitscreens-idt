<?php
date_default_timezone_set('America/Bogota');
include('../admin/assets/php/Conexion_DB.php');

$Modulo = $_POST["modulo"];
$TipoID = $_POST["tipodocumento"];
$NumeroID = $_POST["cedula"];
$Nombres = $_POST["nombres"];
$Apellidos = $_POST["apellidos"];
$Ahora = date("Y-m-d H:i:s");

$consulta = mysqli_query($conexion,"INSERT INTO `visitantes` (`Tipo_Documento`, `No_Documento`, `Nombres`, `Apellidos`, `modulo`, `Fecha_Ingreso`) VALUES ('$TipoID', '$NumeroID', '$Nombres', '$Apellidos','$Modulo', '$Ahora')");

if($consulta){
header("Location: frame.php");
}
else{
	echo "No se ha guadado la info del visitante";
}

?>