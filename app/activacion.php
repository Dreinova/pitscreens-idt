<?php
/**
 * activacion.php — Módulo App (Pantalla)
 *
 * Realiza el cambio de lista de reproducción activa en la base de datos.
 * Es llamado desde tiempo.php cuando detecta que la hora del servidor
 * coincide con la Fecha-Inicio de una programación registrada.
 *
 * Lógica de activación:
 *   1. Busca la programación cuya Fecha-Inicio coincida con NOW().
 *   2. Activa esa programación (Estado = '1').
 *   3. Desactiva todas las demás programaciones (Estado = '0').
 *   4. Si el proceso fue exitoso, recarga index.php para que la galería
 *      comience a reproducir el nuevo contenido programado.
 *
 * Este mecanismo garantiza que solo una lista de reproducción esté
 * activa en cualquier momento dado.
 *
 * Nota: En index.php también existe una lógica de verificación de fechas
 * como respaldo (compara Fecha-Inicio <= NOW()), por lo que la programación
 * correcta se activa aunque tiempo.php no la detecte al segundo exacto.
 */

date_default_timezone_set('America/Bogota');
include '../admin/assets/php/Conexion_DB.php';

$ahora = date("Y-m-d H:i:s");

/* Obtener la programación cuya fecha de inicio coincida con el momento actual */
$sql_programaciones = mysqli_query($conexion,
    "SELECT * FROM `lista-reproduccion`
     WHERE `Fecha-Inicio` = NOW()
     ORDER BY `Fecha-Inicio` DESC LIMIT 1"
);
$rowProgramaciones = mysqli_fetch_array($sql_programaciones);
$ID_programaciones = $rowProgramaciones['ID'];
$fecha_inicio      = $rowProgramaciones['Fecha-Inicio'];

if($ahora == $fecha_inicio){
	// Activar la programación que le corresponde a este momento
	$activar   = mysqli_query($conexion,
        "UPDATE `lista-reproduccion` SET `Estado` = '1'
         WHERE `lista-reproduccion`.`ID` = $ID_programaciones"
    );

	// Desactivar todas las demás programaciones
	$desactivar = mysqli_query($conexion,
        "UPDATE `lista-reproduccion` SET `Estado` = '0'
         WHERE NOT `lista-reproduccion`.`ID` = $ID_programaciones"
    );

	// Recargar la pantalla principal para reproducir el nuevo contenido
	if($desactivar){
		header("location:index.php");
	}
}
?>