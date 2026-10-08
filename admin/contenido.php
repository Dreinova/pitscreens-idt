<?php
/**
 * contenido.php — Módulo Administrador
 *
 * Biblioteca de contenido multimedia (imágenes y videos) y su publicación
 * en las pantallas:
 *   1. Subir archivos a la biblioteca (sin publicarlos todavía) →
 *      assets/php/subir_biblioteca.php.
 *   2. Cuadrícula de la biblioteca: el ícono sobre cada preview abre el
 *      modal "Publicar", que publica ese archivo en varias pantallas a la
 *      vez (y/o en la lista General) con fecha de inicio y de fin
 *      opcionales → assets/php/publicar_contenido.php. El kiosco lo
 *      muestra y lo retira solo según esa ventana.
 *   3. Orden y programación por pantalla: lista ordenable (arrastrar) de
 *      lo publicado en la pantalla elegida → assets/php/lista_publicaciones.php.
 *
 * Tablas: `biblioteca` (archivos), `contenido` (publicaciones: archivo +
 * lista + Orden + Estado + Fecha_Inicio/Fecha_Fin).
 */

date_default_timezone_set('America/Bogota');
include 'assets/php/Conexion_DB.php';
include 'assets/php/contenido_helpers.php';

// Sesión administrativa con 2 horas de duración
session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime'  => 7200,
]);

/* Proteger la página: redirigir al login si no hay sesión activa */
if(!isset($_SESSION['logeado'])){
	header('Location: index.php');
	exit();
}

/* --- Obtener datos del usuario autenticado --- */
$id     = $_SESSION['id_Correo'];
$sql    = "SELECT usuarios.ID, usuarios.Nombre, usuarios.Correo, usuarios.Foto_Usuario,
                  funciones_usuario.Funcion
           FROM usuarios
           INNER JOIN funciones_usuario ON usuarios.Funcion = funciones_usuario.ID
           WHERE usuarios.ID = '$id'";
$resultado    = mysqli_query($conexion, $sql);
$datos        = mysqli_fetch_array($resultado);
$name_user    = $datos['Nombre'];
$mail_user    = $datos['Correo'];
$foto_user    = $datos['Foto_Usuario'];
$funcion_user = $datos['Funcion'];

/* Mensaje de la última acción (subir/publicar), una sola vez. */
$flash = $_SESSION['flash_contenido'] ?? null;
unset($_SESSION['flash_contenido']);

/* --- Destinos posibles: General + cada pantalla, con su lista vigente
       (misma resolución que el kiosco; aquí nunca se crean listas). --- */
$destinos = [[
	'clave'    => 'general',
	'nombre'   => 'General',
	'detalle'  => 'pantallas sin contenido propio',
	'lista'    => lista_general($conexion),
]];
$sql_modulos = mysqli_query($conexion, "SELECT `ID`, `nombre_modulo`, `ubicacion` FROM `modulos` ORDER BY `nombre_modulo` ASC");
while ($m = mysqli_fetch_array($sql_modulos)) {
	$destinos[] = [
		'clave'   => (string) $m['ID'],
		'nombre'  => $m['nombre_modulo'],
		'detalle' => $m['ubicacion'],
		'lista'   => lista_de_pantalla($conexion, $m['nombre_modulo']),
	];
}

$destino_por_lista = [];
foreach ($destinos as $d) {
	if ($d['lista']) { $destino_por_lista[$d['lista']] = $d; }
}

/* --- Dónde está publicado cada archivo (solo en listas vigentes). --- */
$publicaciones_por_url = [];
if ($destino_por_lista) {
	$ids_listas = implode(',', array_map('intval', array_keys($destino_por_lista)));
	$sql_pub = mysqli_query($conexion, "SELECT * FROM `contenido` WHERE `Lista-Reproduccion` IN ($ids_listas)");
	while ($p = mysqli_fetch_array($sql_pub)) {
		$publicaciones_por_url[$p['URL']][] = $p;
	}
}

$sql_biblioteca = mysqli_query($conexion, "SELECT * FROM `biblioteca` ORDER BY `Fecha_Subida` DESC, `ID` DESC");

/* --- Pantalla elegida en "Orden y programación". --- */
$destino_sel = $_GET['destino'] ?? 'general';
$destino_actual = $destinos[0];
foreach ($destinos as $d) {
	if ($d['clave'] === $destino_sel) { $destino_actual = $d; }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <title>Contenido - IDT App</title>
  <!--favicon-->
  <link rel="icon" href="assets/images/Favicon.png" type="image/x-icon">
  <!--Lightbox Css-->
  <link href="assets/plugins/fancybox/css/jquery.fancybox.min.css" rel="stylesheet" type="text/css"/>
  <!-- simplebar CSS-->
  <link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet"/>
  <!-- Bootstrap core CSS-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- animate CSS-->
  <link href="assets/css/animate.css" rel="stylesheet" type="text/css"/>
  <!-- Icons CSS-->
  <link href="assets/css/icons.css" rel="stylesheet" type="text/css"/>
  <!-- Sidebar CSS-->
  <link href="assets/css/sidebar-menu.css" rel="stylesheet"/>
  <!-- Custom Style-->
  <link href="assets/css/app-style.css" rel="stylesheet"/>
  <link href="assets/css/contenido.css" rel="stylesheet"/>

</head>


<body class="bg-theme bg-theme2">

<!-- start loader -->
   <div id="pageloader-overlay" class="visible incoming">
	   <div class="loader-wrapper-outer">
		   <div class="loader-wrapper-inner" >
			   <div class="loader"></div>
		   </div>
	   </div>
	</div>
   <!-- end loader -->

<!-- Start wrapper-->
 <div id="wrapper">

<!-- Menu lateral -->
	 <?php include 'assets/php/menu_lateral.php' ?>

<!-- Menu lateral -->
	 <?php include 'assets/php/menu_superior.php' ?>

<div class="clearfix"></div>

  <div class="content-wrapper">
    <div class="container-fluid">

    <!--Inicio Migas de pan-->
     <div class="row pt-2 pb-2">
        <div class="col-12">
			<h4 class="page-title">Contenido</h4>
			<p class="text-muted mb-0">Biblioteca de imágenes y videos. Sube archivos, publícalos en una o varias pantallas con el ícono <i class="fa fa-paper-plane"></i> (con fecha de inicio y de fin opcionales) y ordena lo que reproduce cada pantalla.</p>
	   </div>
     </div>
    <!-- Fin Migas de pan-->

	<?php if ($flash && $flash['mensaje'] !== ''): ?>
	<div class="alert alert-<?php echo $flash['tipo'] === 'success' ? 'success' : 'warning'; ?> alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<div class="alert-message p-2"><?php echo htmlspecialchars($flash['mensaje']); ?></div>
	</div>
	<?php endif; ?>

	<!-- Subir a la biblioteca -->
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header text-uppercase"><i class="fa fa-upload"></i> Subir a la biblioteca</div>
				<div class="card-body">
					<form action="assets/php/subir_biblioteca.php" method="POST" enctype="multipart/form-data" class="form-inline">
						<label class="btn btn-outline-primary mb-2 mr-2" for="archivosBiblioteca">
							<i class="fa fa-folder-open"></i> <span id="archivosBibliotecaLabel">Elegir archivos…</span>
						</label>
						<input type="file" id="archivosBiblioteca" name="archivos[]" accept=".png, .jpg, .jpeg, .mp4" multiple hidden>
						<button type="submit" id="btnSubirBiblioteca" class="btn btn-primary mb-2" disabled><i class="fa fa-cloud-upload"></i> Subir</button>
					</form>
					<small class="text-muted">PNG, JPG o MP4, hasta <?php echo CONTENIDO_MAX_MB; ?> MB cada uno. Puedes elegir varios a la vez. Subir no publica: después usa <i class="fa fa-paper-plane"></i> en cada archivo.</small>
				</div>
			</div>
		</div>
	</div>

	<!-- Biblioteca -->
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header text-uppercase d-flex flex-wrap align-items-center justify-content-between">
					<span><i class="fa fa-th"></i> Biblioteca</span>
					<span class="btn-group btn-group-sm" role="group">
						<button type="button" class="btn btn-outline-primary js-filtro-biblioteca active" data-tipo="todos">Todos</button>
						<button type="button" class="btn btn-outline-primary js-filtro-biblioteca" data-tipo="image">Imágenes</button>
						<button type="button" class="btn btn-outline-primary js-filtro-biblioteca" data-tipo="video">Videos</button>
					</span>
				</div>
				<div class="card-body">
					<div class="row">
					<?php if (mysqli_num_rows($sql_biblioteca) === 0): ?>
						<div class="col-12 text-muted">La biblioteca está vacía. Sube tu primer archivo arriba.</div>
					<?php endif; ?>
					<?php while ($archivo = mysqli_fetch_array($sql_biblioteca)):
						$url      = $archivo['URL'];
						$src      = 'assets/galeria/' . rawurlencode($url);
						$pubs     = $publicaciones_por_url[$url] ?? [];
						$claves   = [];
						foreach ($pubs as $p) { $claves[] = $destino_por_lista[$p['Lista-Reproduccion']]['clave']; }
					?>
						<div class="col-12 col-sm-6 col-lg-4 col-xl-3 mb-4 biblioteca-item" data-tipo="<?php echo $archivo['Tipo']; ?>">
							<div class="biblioteca-card">
								<div class="biblioteca-preview">
									<?php if ($archivo['Tipo'] == 'video'): ?>
										<a href="<?php echo $src; ?>" data-fancybox="biblioteca" data-caption="<?php echo htmlspecialchars($url); ?>">
											<video src="<?php echo $src; ?>#t=0.5" preload="metadata" muted></video>
										</a>
										<span class="biblioteca-tipo"><i class="fa fa-video-camera"></i> Video</span>
									<?php else: ?>
										<a href="<?php echo $src; ?>" data-fancybox="biblioteca" data-caption="<?php echo htmlspecialchars($url); ?>">
											<img src="<?php echo $src; ?>" alt="" loading="lazy">
										</a>
										<span class="biblioteca-tipo"><i class="fa fa-image"></i> Imagen</span>
									<?php endif; ?>
									<button type="button" class="biblioteca-publicar js-publicar" title="Publicar en pantallas"
									        data-id="<?php echo (int) $archivo['ID']; ?>"
									        data-nombre="<?php echo htmlspecialchars($url); ?>"
									        data-publicado="<?php echo htmlspecialchars(implode(',', $claves)); ?>">
										<i class="fa fa-paper-plane"></i>
									</button>
								</div>
								<div class="biblioteca-body">
									<div class="biblioteca-nombre"><?php echo htmlspecialchars($url); ?></div>
									<div class="biblioteca-destinos">
										<?php if (!$pubs): ?>
											<small class="text-muted">Sin publicar</small>
										<?php endif; ?>
										<?php foreach ($pubs as $p):
											list(, $etq, $clase) = estado_publicacion($p);
											$dest = $destino_por_lista[$p['Lista-Reproduccion']];
										?>
											<span class="badge <?php echo $clase; ?>" title="<?php echo $etq . ' · ' . ventana_publicacion($p); ?>"><?php echo htmlspecialchars($dest['nombre']); ?></span>
										<?php endforeach; ?>
									</div>
									<div class="biblioteca-acciones">
										<button type="button" class="btn btn-sm btn-primary js-publicar"
										        data-id="<?php echo (int) $archivo['ID']; ?>"
										        data-nombre="<?php echo htmlspecialchars($url); ?>"
										        data-publicado="<?php echo htmlspecialchars(implode(',', $claves)); ?>"><i class="fa fa-paper-plane"></i> Publicar</button>
										<a href="assets/php/eliminar_biblioteca.php?id=<?php echo (int) $archivo['ID']; ?>"
										   class="btn btn-sm btn-outline-danger js-confirm-delete" title="Eliminar de la biblioteca"
										   data-title="¿Eliminar este archivo de la biblioteca?"
										   data-body="Se eliminará &quot;<?php echo htmlspecialchars($url); ?>&quot; y se quitará de todas las pantallas donde esté publicado<?php echo $pubs ? ' (' . count($pubs) . ')' : ''; ?>. Esta acción no se puede deshacer."><i class="fa fa-trash"></i></a>
									</div>
								</div>
							</div>
						</div>
					<?php endwhile; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Orden y programación por pantalla -->
	<div class="row" id="orden">
		<div class="col-12">
			<div class="card">
				<div class="card-header text-uppercase d-flex flex-wrap align-items-center justify-content-between">
					<span><i class="fa fa-sort-amount-asc"></i> Orden y programación por pantalla</span>
					<form method="GET" action="contenido.php#orden" class="form-inline">
						<select name="destino" class="form-control form-control-sm" onchange="this.form.submit();">
							<?php foreach ($destinos as $d): ?>
							<option value="<?php echo htmlspecialchars($d['clave']); ?>" <?php echo $d['clave'] === $destino_actual['clave'] ? 'selected' : ''; ?>>
								<?php echo htmlspecialchars($d['nombre']); ?><?php echo $d['clave'] !== 'general' && !$d['lista'] ? ' (usa General)' : ''; ?>
							</option>
							<?php endforeach; ?>
						</select>
					</form>
				</div>
				<div class="card-body">
					<?php
						$lista_render    = $destino_actual['lista'];
						$texto_sin_lista = $destino_actual['clave'] === 'general'
							? 'Todavía no hay contenido General publicado.'
							: 'Esta pantalla no tiene contenido propio: está mostrando la lista <a href="contenido.php?destino=general#orden">General</a>. Publica algo en ella para darle su propia lista.';
						include 'assets/php/lista_publicaciones.php';
					?>
				</div>
			</div>
		</div>
	</div>

    </div>
    <!-- End container-fluid-->

   </div><!--End content-wrapper-->

   <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->

	<!--Start footer-->
	<footer class="footer">
      <div class="container">
        <div class="text-center">
          © Instituto Distrital de Turismo
        </div>
      </div>
    </footer>
	<!--End footer-->

  </div><!--End wrapper-->

  <!-- Modal publicar -->
  <div class="modal fade" id="modalPublicar" tabindex="-1" role="dialog" aria-labelledby="modalPublicarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form class="modal-content" id="formPublicar" action="assets/php/publicar_contenido.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="modalPublicarLabel"><i class="fa fa-paper-plane"></i> Publicar <small class="js-publicar-nombre publicar-subtitulo"></small></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="biblioteca_id" value="">

          <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="mb-0">Pantallas</label>
            <button type="button" class="btn btn-link btn-sm p-0 js-marcar-todas">Marcar / desmarcar todas</button>
          </div>
          <div class="publicar-destinos mb-1">
            <?php foreach ($destinos as $d): ?>
            <label class="js-destino">
              <input type="checkbox" name="destinos[]" value="<?php echo htmlspecialchars($d['clave']); ?>">
              <strong><?php echo htmlspecialchars($d['nombre']); ?></strong>
              <?php if ($d['detalle']): ?><small class="text-muted">— <?php echo htmlspecialchars($d['detalle']); ?></small><?php endif; ?>
              <span class="badge badge-success js-ya-publicado" style="display:none;">ya publicado</span>
            </label>
            <?php endforeach; ?>
          </div>
          <small class="text-muted d-block mb-3">Una pantalla que recibe su primer contenido propio deja de mostrar la lista General. Si ya estaba publicado ahí, se reprograma con las fechas nuevas.</small>

          <div class="form-row">
            <div class="form-group col-sm-6">
              <label for="pubFechaInicio">Desde</label>
              <input type="datetime-local" class="form-control" id="pubFechaInicio" name="fecha_inicio">
              <small class="text-muted">Vacío = desde ya</small>
            </div>
            <div class="form-group col-sm-6">
              <label for="pubFechaFin">Hasta</label>
              <input type="datetime-local" class="form-control" id="pubFechaFin" name="fecha_fin">
              <small class="text-muted">Vacío = sin fecha de fin</small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success"><i class="fa fa-paper-plane"></i> Publicar</button>
        </div>
      </form>
    </div>
  </div>

  <?php include 'assets/php/confirm_delete_modal.php'; ?>

  <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <!-- jQuery UI (sortable) antes de Bootstrap, para que sus .button()/.tooltip() no pisen los de Bootstrap -->
  <script src="assets/plugins/jquery-ui/jquery-ui.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>

  <!-- simplebar js -->
  <script src="assets/plugins/simplebar/js/simplebar.js"></script>
  <!-- sidebar-menu js -->
  <script src="assets/js/sidebar-menu.js"></script>

  <!-- Custom scripts -->
  <script src="assets/js/app-script.js"></script>

  <!--Lightbox-->
  <script src="assets/plugins/fancybox/js/jquery.fancybox.min.js"></script>
  <!-- Confirmación de eliminación -->
  <script src="assets/js/confirm-delete.js"></script>
  <!-- Reordenar (arrastrar) + modal de publicar -->
  <script src="assets/js/publicaciones.js"></script>

<?php
	mysqli_close($conexion);
?>

</body>
</html>
