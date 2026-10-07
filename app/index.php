<?php
/**
 * index.php — Módulo App (Pantalla)
 *
 * Pantalla principal del kiosco interactivo. Muestra la galería de medios
 * (imágenes y videos) de la programación activa en modo slideshow automático.
 * También contiene el mensaje "Toque la pantalla para comenzar" que invita
 * al visitante a acceder al contenido del punto de información turística.
 *
 * Requiere sesión de módulo activa ($_SESSION['log-modulo']).
 * Si no hay sesión, redirige a log_index.php para seleccionar el kiosco.
 *
 * Lógica de programación:
 *   1. Busca la programación más reciente cuya Fecha-Inicio <= ahora.
 *   2. La activa (Estado = '1') y desactiva todas las demás.
 *   3. Consulta todos los elementos de contenido (imágenes/videos) asignados
 *      a esa lista de reproducción.
 *   4. Los exporta como array JavaScript (loopAssets) para el slideshow.
 *
 * Slideshow JavaScript:
 *   - Imágenes: se muestran durante 10 segundos (setTimeout 10000ms).
 *   - Videos: se reproducen completos; al terminar (evento 'ended') avanza al siguiente.
 *   - Si no hay contenido configurado, reproduce un video por defecto (IDT.mp4).
 *
 * Actualización en tiempo real:
 *   - El div #tiempo se actualiza cada 1 segundo cargando tiempo.php vía AJAX
 *     para detectar cambios de programación sin recargar la página.
 */

error_reporting(0); // Ocultar errores en producción (pantallas públicas)
date_default_timezone_set('America/Bogota');

// Sesión de larga duración para pantallas 24/7
session_cache_expire("31536000");
session_set_cookie_params("31536000");
session_start([
    'cookie_lifetime' => 31536000,
    'gc_maxlifetime'  => 31536000,
]);

// Verificar que el módulo haya sido seleccionado previamente
if(!isset($_SESSION['log-modulo'])){
	header('Location: log_index.php');
}

$modulo = $_SESSION['modulo'];

include '../admin/assets/php/Conexion_DB.php';

$ahora      = date("Y-m-d H:i:s");
// Calcular un minuto adelante (reservado para lógica futura de anticipación)
$NuevaFecha = strtotime('+1 minute', strtotime($ahora));
$NuevaFecha = date('Y-m-d H:i:s', $NuevaFecha);

/* --- Verificación y activación de programación según fecha actual --- */
// Buscar la programación más reciente que ya debería estar activa
$sql_programaciones = mysqli_query($conexion,
    "SELECT * FROM `lista-reproduccion`
     WHERE `Fecha-Inicio` <= NOW()
     ORDER BY `Fecha-Inicio` DESC LIMIT 1"
);
$rowProgramaciones = mysqli_fetch_array($sql_programaciones);
$ID_programaciones = $rowProgramaciones['ID'];
$fecha_inicio      = $rowProgramaciones['Fecha-Inicio'];

if($ahora > $fecha_inicio){
	// Activar la programación más reciente y desactivar el resto
	$activar   = mysqli_query($conexion,
        "UPDATE `lista-reproduccion` SET `Estado` = '1'
         WHERE `lista-reproduccion`.`ID` = $ID_programaciones"
    );
	$desactivar = mysqli_query($conexion,
        "UPDATE `lista-reproduccion` SET `Estado` = '0'
         WHERE NOT `lista-reproduccion`.`ID` = $ID_programaciones"
    );
}

/* --- Obtener la programación activa y su contenido multimedia --- */
$consulta_programacion = mysqli_query($conexion,
    "SELECT * FROM `lista-reproduccion` WHERE Estado = '1' LIMIT 1"
);
$row_programacion  = mysqli_fetch_array($consulta_programacion);
$ID_programacion   = $row_programacion["ID"];
$name_programacion = $row_programacion["Nombre"];

// Obtener todos los elementos de contenido de la lista activa
$consultaContenido = mysqli_query($conexion,
    "SELECT * FROM `contenido` WHERE `lista-reproduccion` = $ID_programacion"
);
$numero_filas = mysqli_num_rows($consultaContenido);

?>

<!doctype html>
<html lang="es">

<head>
<meta charset="utf-8">
<title>IDT App - Bienvenido</title>	
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<meta name="HandheldFriendly" content="true" />
	
	<!-- No guardar Cache -->
	<meta http-equiv="Expires" content="0">
	<meta http-equiv="Last-Modified" content="0">
	<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
	<meta http-equiv="Pragma" content="no-cache">
	
	<!-- Favicon -->
	<link rel="icon" type="image/png" href="assets/img/favicon.png" />
	<!-- CSS de la app -->
	<link href="assets/css/styles.css" rel="stylesheet" type="text/css">
	
<!-- Scripts arranque -->
<script>
function lanzadera(){
		loading();
    }
    window.onload = lanzadera;	
</script>
	
</head>
	
<body oncontextmenu="return false" onselectstart="return false" ondragstart="return false" style="overflow:hidden" >

	<div class="item contenedor-medios"></div>

	<a class="kiosk-idle" href="frame.php" aria-label="Toca la pantalla para comenzar">
		<div class="kiosk-idle-content">
			<div class="kiosk-touch-badge">
				<img class="kiosk-touch-icon" src="assets/img/Mano_Touch.svg" alt="">
			</div>
			<h2 class="kiosk-idle-title">Toca la pantalla para comenzar</h2>
			<span class="kiosk-module-pill"><?php echo $modulo; ?></span>
		</div>
	</a>

	<a href="assets/php/logout.php" class="kiosk-settings-btn" aria-label="Cambiar módulo" title="Cambiar módulo" onclick="return confirm('¿Cambiar el módulo asignado a este punto?');">&#9881;</a>

	<div id="contenedor_carga">
		<div id="carga"></div>
	</div>
	
	<div id="tiempo">
	</div>
	
<!-- Scripts JQuery -->
	<script src="assets/js/jquery.min.js"></script>
<!-- Scripts IDT app -->
	<script type="text/javascript" src="assets/js/IDT_app.js"></script>

	<!-- Actualizar Div Módulos -->
<script>

$(document).ready(function() {
      var refreshId =  setInterval( function(){
    $('#tiempo').load('tiempo.php');//actualizas el div
   }, 1000 );
});

</script>
<!-- Scripts Lista de Reproduccion -->
<script>
// Listado de medios en la programación
var loopAssets = [
	<?php
	if ($numero_filas >= 1){
	while($row = mysqli_fetch_array($consultaContenido)){
		$URLContenido = $row['URL'];
		$tipoContenido = $row['Tipo'];
		$contentType = 'video/mp4';
		
		if($tipoContenido  == 'video'){
			$tipoContenido = 'video';
			$contentType = 'video/mp4';
		}
		if($tipoContenido  == 'image'){
			$tipoContenido = 'image';
			$contentType = 'image/jpg';
		}

	?>
	
	{ contentUrl: "../admin/assets/galeria/<?php echo $URLContenido; ?>", contentType: "<?php echo $contentType; ?>", mediaType: "<?php echo $tipoContenido; ?>" },
	<?php
	}
		}
	else{
		echo '{ contentUrl: "assets/media/IDT.mp4", contentType: "video/mp4", mediaType: "video"},';
	}
	?>

	/*{ contentUrl: "../admin/assets/galeria/V1.mp4", contentType: "video/mp4", mediaType: "video"},
	{ contentUrl: "../admin/assets/galeria/I1.jpg", contentType: "image/jpg", mediaType:"image" },*/

];
var previewContainer = $(".contenedor-medios");
var curIndex = 1;

appendMediaElement(loopAssets[0]);

// Funcion para cambiar de medio
function changeMedia() {
  if(curIndex >= loopAssets.length) {
    curIndex = 0;
  }
  appendMediaElement(loopAssets[curIndex]);
  curIndex++;
};

// Funcion para agregar los tipos de formato Imagen o Video
function appendMediaElement(asset) {
  var mediaEl = "";
	//Si es Imagen
	if(asset.mediaType == "image") {
		mediaEl =  '<img id="lp-preview-image" src="' + asset.contentUrl + '">';
		previewContainer.html(mediaEl);
		// Ajuste de Tiempo para imagenes 
		setTimeout("changeMedia()", 10000);
  }
	// Si es Video
	else if(asset.mediaType == "video") {
		mediaEl = "<video id='lp-preview-video' autoplay muted>";
    	mediaEl += "<source src='"+ asset.contentUrl + "' type='" + asset.contentType + "'>";
    	mediaEl += "</video>";
    	previewContainer.html(mediaEl);
    	// video: el tiempo de los videos es automatico
		document.getElementById("lp-preview-video").addEventListener("ended", function(e) {
		changeMedia();
    });
  }
}
</script>
	
</body>
</html>