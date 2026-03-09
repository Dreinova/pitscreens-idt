<?php
date_default_timezone_set('America/Bogota');
include('../admin/assets/php/Conexion_DB.php');

$NumeroID = $_POST["cedula"];


$consulta = mysqli_query($conexion,"SELECT * FROM `visitantes` WHERE No_Documento = $NumeroID");
$conteo = mysqli_num_rows($consulta);
echo $conteo;

if($conteo == 0){
	header("Location: registro.php");
	echo "Nuevo Visistante";
}
else{
	header("Location: frame.php");
	echo "Nueva Visista";
}

?>