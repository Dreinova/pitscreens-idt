<?php
/**
 * comprobacion.php — Módulo App (Pantalla)
 *
 * Verifica si un visitante ya está registrado en el sistema.
 * Recibe el número de documento ingresado en login.php y consulta
 * la tabla `visitantes`. Redirige según el resultado:
 *
 *   - Visitante nuevo (no encontrado) → registro.php para capturar sus datos.
 *   - Visitante conocido (ya registrado) → frame.php para mostrar el contenido.
 *
 * Parámetros POST:
 *   cedula (string) — Número de documento del visitante
 *
 * Nota: No requiere sesión de usuario; solo requiere sesión de módulo activa
 * (controlada desde login.php que valida $_SESSION['log-modulo']).
 */

date_default_timezone_set('America/Bogota');
include('../admin/assets/php/Conexion_DB.php');

$NumeroID = $_POST["cedula"];

// Buscar el número de documento en el historial de visitantes
$consulta = mysqli_query($conexion, "SELECT * FROM `visitantes` WHERE No_Documento = $NumeroID");
$conteo   = mysqli_num_rows($consulta);

if($conteo == 0){
	// Primer acceso: redirigir al formulario de registro
	header("Location: registro.php");
}
else{
	// Visitante conocido: ir directamente al contenido
	header("Location: frame.php");
}
?>