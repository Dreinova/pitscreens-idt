<?php
/**
 * frame.php — Módulo App (Pantalla)
 *
 * Pantalla de contenido interactivo del kiosco. Muestra, dentro de un
 * iframe, la URL de destino configurada para esta pantalla (o la general
 * si no tiene una propia) en `configuracion.URL_Destino` — es a donde
 * navega app/index.php cuando el visitante toca la pantalla.
 *
 * La URL externa se carga a través de proxy.php para evadir restricciones
 * de X-Frame-Options que impiden la incrustación directa de sitios externos.
 *
 * Requisitos:
 *   - Sesión de módulo activa ($_SESSION['log-modulo']).
 *   - `URL_Destino` configurada (general o de esta pantalla) en `configuracion`.
 *
 * Comportamiento de inactividad:
 *   - El script Inactividad2.js detecta cuando el usuario lleva un tiempo
 *     sin interactuar y redirige automáticamente a index.php.
 *
 * Navegación:
 *   - Botón "Regresar" lleva de vuelta a index.php (pantalla principal).
 */

error_reporting(0); // Suprimir errores en pantallas públicas
date_default_timezone_set('America/Bogota');

// Sesión de larga duración para pantallas 24/7
session_cache_expire("31536000");
session_set_cookie_params("31536000");
session_start([
    'cookie_lifetime' => 31536000,
    'gc_maxlifetime'  => 31536000,
]);

// Verificar sesión de módulo; si no existe, redirigir a selección de kiosco
if(!isset($_SESSION['log-modulo'])){
    header('Location: log_index.php');
    exit();
}

$modulo = $_SESSION['modulo'];

include '../admin/assets/php/Conexion_DB.php';

/* URL de destino y tiempo de inactividad — configuración específica de
   este módulo si existe, si no la general (misma resolución que usa
   app/index.php para el protector). */
$modulo_esc = mysqli_real_escape_string($conexion, $modulo);
$consulta_config = mysqli_query($conexion, "SELECT `URL_Destino`, `Tiempo_Inactividad_Contenido` FROM `configuracion` WHERE `Modulo` = '$modulo_esc' LIMIT 1");
$row_config = mysqli_fetch_array($consulta_config);
if (!$row_config) {
	$consulta_config = mysqli_query($conexion, "SELECT `URL_Destino`, `Tiempo_Inactividad_Contenido` FROM `configuracion` WHERE `Modulo` IS NULL LIMIT 1");
	$row_config = mysqli_fetch_array($consulta_config);
}
$URL                   = $row_config ? trim($row_config['URL_Destino']) : '';
$tiempo_inactividad_ms = $row_config ? ((int) $row_config['Tiempo_Inactividad_Contenido'] * 1000) : 900000;

// Sin URL configurada: no hay nada que mostrar, volver al kiosco.
if ($URL === '') {
	header('Location: index.php');
	exit();
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>IDT App - Contenido</title>	

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="HandheldFriendly" content="true" />

<!-- No guardar Cache -->
<meta http-equiv="Expires" content="0">
<meta http-equiv="Last-Modified" content="0">
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">

<!-- Favicon -->
<link rel="icon" type="image/png" href="assets/img/favicon.png" />
<!-- CSS de la app -->
<link href="assets/css/styles.css" rel="stylesheet" type="text/css">

<script>
function lanzadera(){
    loading();    
    inicio(); 
}
window.onload = lanzadera;	
</script>
	
</head>

<body oncontextmenu="return false" onselectstart="return false" ondragstart="return false" 
      style="overflow:hidden" onkeypress="parar()" onclick="parar()" >
	
<section class="section_contenido">
    <div class="container"> 
        <a class="regresar" href="index.php">Regresar</a>
        
        <!-- Aquí va el proxy -->
        <iframe class="responsive-iframe" 
                src="proxy.php?url=<?php echo urlencode($URL); ?>" 
                frameborder="0" 
                width="100%" 
                height="100%">
        </iframe>
        
    </div>
</section>

<div id="contenedor_carga">
    <div id="carga"></div>
</div>

<!-- Scripts JQuery -->
<script src="assets/js/jquery.min.js"></script>
<!-- Tiempo de inactividad configurable desde el admin -->
<script>var IDT_TIMEOUT_MS = <?php echo $tiempo_inactividad_ms; ?>;</script>
<!-- Scripts Inactividad -->
<script type="text/javascript" src="assets/js/Inactividad2.js"></script>
<!-- Scripts IDT app -->
<script type="text/javascript" src="assets/js/IDT_app.js"></script>

</body>
</html>
