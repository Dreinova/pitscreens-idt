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

include '../admin/assets/php/Conexion_DB.php';

/* URL estable por pantalla (aditivo): ?pantalla=<modulos.ID> selecciona esa
   pantalla directamente, igual que si se hubiera elegido en log_index.php.
   Si el ID no existe, se ignora por completo y el flujo normal (sesión
   existente, o redirect a log_index.php) sigue intacto. */
if(isset($_GET['pantalla'])){
	$pantalla_id  = (int) $_GET['pantalla'];
	$sql_pantalla = mysqli_query($conexion, "SELECT nombre_modulo FROM modulos WHERE ID = $pantalla_id");
	$row_pantalla = $sql_pantalla ? mysqli_fetch_array($sql_pantalla) : null;
	if ($row_pantalla) {
		$_SESSION['log-modulo'] = true;
		$_SESSION['modulo']     = $row_pantalla['nombre_modulo'];
		$_SESSION['modulo_id']  = $pantalla_id;
	}
}

// Verificar que el módulo haya sido seleccionado previamente
if(!isset($_SESSION['log-modulo'])){
	header('Location: log_index.php');
}

$modulo = $_SESSION['modulo'];

$ahora      = date("Y-m-d H:i:s");
// Calcular un minuto adelante (reservado para lógica futura de anticipación)
$NuevaFecha = strtotime('+1 minute', strtotime($ahora));
$NuevaFecha = date('Y-m-d H:i:s', $NuevaFecha);

/* --- Contenido específico de este módulo (si existe) --- */
// Si hay una programación atada a ESTE módulo con fecha ya vigente, se usa
// directo — no participa del mecanismo de Estado=1 (ese sigue siendo
// exclusivo del contenido general, más abajo), así que cada módulo puede
// tener la suya sin pisar a los demás.
$modulo_actual   = mysqli_real_escape_string($conexion, $modulo);
$sql_especifica  = mysqli_query($conexion,
    "SELECT * FROM `lista-reproduccion`
     WHERE `Modulo` = '$modulo_actual' AND `Fecha-Inicio` <= NOW()
     ORDER BY `Fecha-Inicio` DESC LIMIT 1"
);
$row_especifica  = mysqli_fetch_array($sql_especifica);

if ($row_especifica) {
	$ID_programacion   = $row_especifica['ID'];
	$name_programacion = $row_especifica['Nombre'];
	$fecha_mod_lista   = $row_especifica['Fecha-Modificacion'];
}
else {
	/* --- Sin programación propia: cae al contenido general --- */
	// Buscar la programación GENERAL (sin módulo) más reciente que ya
	// debería estar activa.
	$sql_programaciones = mysqli_query($conexion,
	    "SELECT * FROM `lista-reproduccion`
	     WHERE (`Modulo` IS NULL OR `Modulo` = '') AND `Fecha-Inicio` <= NOW()
	     ORDER BY `Fecha-Inicio` DESC LIMIT 1"
	);
	$rowProgramaciones = mysqli_fetch_array($sql_programaciones);
	$ID_programaciones = $rowProgramaciones['ID'];
	$fecha_inicio      = $rowProgramaciones['Fecha-Inicio'];

	if($ahora > $fecha_inicio){
		// Activar la programación general más reciente y desactivar el
		// resto de programaciones generales (las de módulo específico no
		// usan Estado, así que se dejan fuera de este switcheo).
		$activar   = mysqli_query($conexion,
	        "UPDATE `lista-reproduccion` SET `Estado` = '1'
	         WHERE `lista-reproduccion`.`ID` = $ID_programaciones"
	    );
		$desactivar = mysqli_query($conexion,
	        "UPDATE `lista-reproduccion` SET `Estado` = '0'
	         WHERE NOT `lista-reproduccion`.`ID` = $ID_programaciones
	         AND (`Modulo` IS NULL OR `Modulo` = '')"
	    );
	}

	/* --- Obtener la programación general activa --- */
	$consulta_programacion = mysqli_query($conexion,
	    "SELECT * FROM `lista-reproduccion` WHERE Estado = '1' AND (`Modulo` IS NULL OR `Modulo` = '') LIMIT 1"
	);
	$row_programacion  = mysqli_fetch_array($consulta_programacion);
	$ID_programacion   = $row_programacion["ID"];
	$name_programacion = $row_programacion["Nombre"];
	$fecha_mod_lista   = $row_programacion["Fecha-Modificacion"];
}

// Elementos de la lista (específica o general) que se deben ver AHORA:
// activos y dentro de su ventana de publicación (Fecha_Inicio/Fecha_Fin,
// NULL = sin límite), en el orden definido desde el admin.
$ID_programacion   = (int) $ID_programacion;
$consultaContenido = mysqli_query($conexion,
    "SELECT * FROM `contenido`
     WHERE `Lista-Reproduccion` = $ID_programacion AND `Estado` = 1
       AND (`Fecha_Inicio` IS NULL OR `Fecha_Inicio` <= '$ahora')
       AND (`Fecha_Fin` IS NULL OR `Fecha_Fin` > '$ahora')
     ORDER BY CAST(`Orden` AS SIGNED) ASC, `ID` ASC"
);
$filasContenido = [];
while ($fila = mysqli_fetch_array($consultaContenido)) {
	$filasContenido[] = $fila;
}
$numero_filas = count($filasContenido);

// IDs visibles ahora, ordenados — parte de la versión: cuando algo entra
// o sale de su ventana, tiempo.php reporta otro conjunto y la pantalla
// recarga sola (publicación/despublicación automática).
$idsVisibles = array_map(function ($f) { return (int) $f['ID']; }, $filasContenido);
sort($idsVisibles);

/* --- Protector de pantalla: configuración específica de este módulo si
       existe, si no la general --- */
$sql_config_especifica = mysqli_query($conexion,
    "SELECT * FROM `configuracion` WHERE `Modulo` = '$modulo_actual' LIMIT 1"
);
$row_config = mysqli_fetch_array($sql_config_especifica);

if (!$row_config) {
	$sql_config_general = mysqli_query($conexion,
	    "SELECT * FROM `configuracion` WHERE `Modulo` IS NULL LIMIT 1"
	);
	$row_config = mysqli_fetch_array($sql_config_general);
}

$protector_url    = $row_config ? $row_config['Protector_URL']  : 'IDT.mp4';
$protector_tipo   = $row_config ? $row_config['Protector_Tipo'] : 'video';
$url_destino      = $row_config ? trim($row_config['URL_Destino']) : '';
$id_config        = $row_config ? $row_config['ID'] : null;
$fecha_mod_config = $row_config ? $row_config['Fecha-Modificacion'] : null;

/* --- Versión del contenido/configuración vigente, para que el polling de
       abajo detecte cambios hechos desde el admin y recargue la pantalla. */
$contenidoVersion = ($ID_programacion ?: '') . '-' . $fecha_mod_lista . '|' . $id_config . '-' . $fecha_mod_config . '|' . implode(',', $idsVisibles);

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

	<!-- Invitación a tocar: se muestra siempre, encima del protector y del
	     contenido propio (que ya se reproducen solos, mezclados, desde que
	     carga la página). Solo es clickeable si hay una URL de destino
	     configurada para esta pantalla — si no, se oculta por JS. -->
	<div class="kiosk-signage-overlay" id="overlayProtector" onclick="irAUrlDestino();">
		<div class="kiosk-idle-content">
			<div class="kiosk-touch-badge">
				<img class="kiosk-touch-icon" src="assets/img/Mano_Touch.svg" alt="">
			</div>
			<h2 class="kiosk-idle-title">Toca la pantalla para comenzar</h2>
			<span class="kiosk-module-pill"><?php echo $modulo; ?></span>
		</div>
	</div>

	<a href="assets/php/logout.php" class="kiosk-settings-btn" aria-label="Cambiar pantalla" title="Cambiar pantalla" onclick="event.stopPropagation(); return confirm('¿Cambiar la pantalla asignada a este punto?');">&#9881;</a>

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
    $('#tiempo').load('tiempo.php', function(responseText){
		// tiempo.php responde "ahora|idLista-fechaLista|idConfig-fechaConfig|idsVisibles"
		// — si la parte de versión cambió respecto a la que se cargó con
		// esta página, el admin editó algo: recargar de verdad.
		var partes = String(responseText).split('|');
		var versionServidor = partes.slice(1).join('|');
		if (versionServidor && versionServidor !== contenidoVersion) {
			location.reload();
		}
	});//actualizas el div
   }, 1000 );
});

</script>
<!-- Scripts Lista de Reproduccion -->
<script>
// Contenido real configurado para este módulo/general (puede quedar vacío
// si todavía no hay nada programado).
var contenidoAssets = [
	<?php
	foreach($filasContenido as $row){
		$URLContenido = rawurlencode($row['URL']);
		$tipoContenido = $row['Tipo'];

		if($tipoContenido == 'video'){
			$contentType = 'video/mp4';
		}
		else {
			$tipoContenido = 'image';
			$contentType = 'image/jpg';
		}
	?>
	{ contentUrl: "../admin/assets/galeria/<?php echo $URLContenido; ?>", contentType: "<?php echo $contentType; ?>", mediaType: "<?php echo $tipoContenido; ?>" },
	<?php
	}
	?>
];

// Protector de pantalla configurado en el admin (admin/configuracion.php) —
// siempre hay uno, es lo que se ve antes de tocar la pantalla.
var protectorAsset = {
	contentUrl: "../admin/assets/galeria/<?php echo $protector_url; ?>",
	contentType: "<?php echo $protector_tipo === 'image' ? 'image/jpg' : 'video/mp4'; ?>",
	mediaType: "<?php echo $protector_tipo; ?>"
};

// URL a la que navega el kiosco cuando tocan la pantalla (general o propia
// de esta pantalla, configurable desde el admin). Vacía = sin configurar
// todavía: el ícono de "toca para comenzar" ni se muestra.
var urlDestino = "<?php echo $url_destino; ?>";

// Versión con la que se cargó esta página — el polling de arriba la compara
// contra la versión que reporte el servidor en cada "tick".
var contenidoVersion = "<?php echo $contenidoVersion; ?>";

var previewContainer = $(".contenedor-medios");
var overlayProtector  = document.getElementById('overlayProtector');
var curIndex = 0;

// Protector + contenido propio de esta pantalla, todo junto en un solo
// bucle continuo que arranca solo al cargar la página — ya no hay nada
// que "revelar" al tocar, el toque ahora navega a una URL (ver abajo).
var playlist = [protectorAsset].concat(contenidoAssets);

// Funcion para agregar los tipos de formato Imagen o Video. "onEnded" decide
// qué pasa cuando termina ese elemento (avanza al siguiente de la playlist).
function appendMediaElement(asset, onEnded) {
  var mediaEl = "";
	if(asset.mediaType == "image") {
		mediaEl =  '<img id="lp-preview-image" src="' + asset.contentUrl + '">';
		previewContainer.html(mediaEl);
		setTimeout(onEnded, 10000); // Tiempo fijo para imágenes
  }
	else if(asset.mediaType == "video") {
		mediaEl = "<video id='lp-preview-video' autoplay muted>";
    	mediaEl += "<source src='"+ asset.contentUrl + "' type='" + asset.contentType + "'>";
    	mediaEl += "</video>";
    	previewContainer.html(mediaEl);
		document.getElementById("lp-preview-video").addEventListener("ended", onEnded);
  }
}

function reproducirPlaylist() {
	if (curIndex >= playlist.length) {
		curIndex = 0;
	}
	appendMediaElement(playlist[curIndex], reproducirPlaylist);
	curIndex++;
}

// Al tocar la pantalla: navega a la URL configurada (frame.php resuelve
// cuál — general o de esta pantalla) y registra la métrica de uso. No
// bloquea la navegación esperando esa petición.
function irAUrlDestino() {
	if (!urlDestino) { return; }
	$.post('registrar_evento.php', { evento: 'SCREEN_STARTED' });
	window.location.href = 'frame.php';
}

// Arranque: la playlist combinada se reproduce sola desde el principio. El
// ícono de "toca para comenzar" solo tiene sentido (y solo se muestra) si
// hay una URL configurada a la que ir.
overlayProtector.style.display = urlDestino ? 'flex' : 'none';
reproducirPlaylist();
</script>
	
</body>
</html>