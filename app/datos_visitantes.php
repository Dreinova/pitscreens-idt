<?php
/**
 * datos_visitantes.php — Módulo App (Pantalla)
 *
 * Guarda los datos del nuevo visitante en la base de datos y lo
 * redirige al contenido del punto de información turística.
 *
 * Recibe los datos del formulario de registro (registro.php) vía POST
 * e inserta un nuevo registro en la tabla `visitantes`.
 *
 * Parámetros POST:
 *   modulo        (string) — Nombre del módulo/kiosco donde se registra
 *   tipodocumento (string) — Tipo de documento (Cédula, Pasaporte, etc.)
 *   cedula        (string) — Número de documento del visitante
 *   nombres       (string) — Nombres del visitante
 *   apellidos     (string) — Apellidos del visitante
 *
 * Campos adicionales que guarda automáticamente:
 *   Fecha_Ingreso — Fecha y hora exacta de la visita (zona horaria Bogotá)
 *
 * Al guardar exitosamente, redirige a frame.php para mostrar el contenido.
 */

date_default_timezone_set('America/Bogota');
include('../admin/assets/php/Conexion_DB.php');

// Recoger datos del formulario de registro
$Modulo    = $_POST["modulo"];
$TipoID    = $_POST["tipodocumento"];
$NumeroID  = $_POST["cedula"];
$Nombres   = $_POST["nombres"];
$Apellidos = $_POST["apellidos"];
$Ahora     = date("Y-m-d H:i:s"); // Marca de tiempo del ingreso

// Insertar el nuevo visitante en la base de datos
$consulta = mysqli_query($conexion,
    "INSERT INTO `visitantes` (`Tipo_Documento`, `No_Documento`, `Nombres`, `Apellidos`, `modulo`, `Fecha_Ingreso`)
     VALUES ('$TipoID', '$NumeroID', '$Nombres', '$Apellidos', '$Modulo', '$Ahora')"
);

if($consulta){
	// Registro exitoso: mostrar el contenido al visitante
	header("Location: frame.php");
}
else{
	echo "No se ha guardado la información del visitante";
}
?>