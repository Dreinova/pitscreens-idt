<?php
/**
 * editar_modulo.php — Módulo Administrador
 *
 * Centro de gestión de una Pantalla: datos básicos, el contenido que
 * reproduce (imágenes/video) y su protector de pantalla propio (o, si no
 * tiene uno, indica que usa el general). El contenido se guarda en una
 * `lista-reproduccion` dedicada a esta pantalla (`Modulo` = su nombre),
 * creada automáticamente la primera vez que se sube algo — nunca al solo
 * abrir esta página, para no generar listas duplicadas en cada visita.
 * Lo que se sube aquí también queda en la biblioteca (contenido.php), y
 * cada publicación puede tener fecha de inicio/fin y reordenarse
 * arrastrando (assets/php/lista_publicaciones.php).
 */

date_default_timezone_set('America/Bogota');
include 'assets/php/Conexion_DB.php';
include 'assets/php/contenido_helpers.php';

session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime' => 7200,
]);

/*  verificacion login  */
if(!isset($_SESSION['logeado'])):
	header('Location: index.php');
	exit();
endif;

/* datos de usuario */

$id = $_SESSION['id_Correo'];
$sql = "SELECT usuarios.ID, usuarios.Nombre, usuarios.Correo, usuarios.Foto_Usuario, funciones_usuario.Funcion FROM usuarios INNER JOIN funciones_usuario ON usuarios.Funcion = funciones_usuario.ID WHERE usuarios.ID = '$id' ";
$resultado = mysqli_query($conexion, $sql);
$datos = mysqli_fetch_array($resultado);

$name_user = $datos['Nombre'];
$mail_user = $datos['Correo'];
$foto_user = $datos['Foto_Usuario'];
$funcion_user = $datos['Funcion'];

/* datos de la pantalla (tabla modulos) */

$edit_id = (int) $_GET['id'];
$sql_pantalla = mysqli_query($conexion, "SELECT * FROM modulos WHERE ID = $edit_id");
$datos_modulo = mysqli_fetch_array($sql_pantalla);

if (!$datos_modulo) {
	header('Location: modulos.php');
	exit();
}

$nombre_modulo          = $datos_modulo['nombre_modulo'];
$ubicacion_modulo       = $datos_modulo['ubicacion'];
$nombre_modulo_escaped  = mysqli_real_escape_string($conexion, $nombre_modulo);

/* --- Actualizar datos básicos de la pantalla --- */

if(isset($_POST['actualizar_modulo'])) {

	$nombre_modulo_anterior = $nombre_modulo;
	$nuevo_nombre_modulo    = $_POST["modulo"];
	$ubicacion              = $_POST["ubicacion"];
	$fecha                  = date("Y-m-d H:i:s");

	$nuevo_nombre_escaped = mysqli_real_escape_string($conexion, $nuevo_nombre_modulo);

	$actualizar_modulo = mysqli_query($conexion, "UPDATE `modulos` SET `nombre_modulo` = '$nuevo_nombre_escaped', `ubicacion` = '$ubicacion', `fecha_modificacion` = '$fecha', `Usuario` = '$name_user' WHERE `modulos`.`ID` = $edit_id");

	if($actualizar_modulo){
		// Si el nombre cambió, re-atar su contenido y protector propios
		// (si los tenía) al nuevo nombre — si no, quedarían huérfanos.
		if ($nuevo_nombre_modulo !== $nombre_modulo_anterior) {
			$nombre_anterior_escaped = mysqli_real_escape_string($conexion, $nombre_modulo_anterior);
			mysqli_query($conexion, "UPDATE `lista-reproduccion` SET `Modulo` = '$nuevo_nombre_escaped' WHERE `Modulo` = '$nombre_anterior_escaped'");
			mysqli_query($conexion, "UPDATE `configuracion` SET `Modulo` = '$nuevo_nombre_escaped' WHERE `Modulo` = '$nombre_anterior_escaped'");
		}
		header("Location: editar_modulo.php?id=$edit_id");
		exit();
	}
	else{
		$mensaje[] = 'No se ha guardado la pantalla, intentelo mas tarde.';
	}
}

/* --- Resolver (solo lectura) la lista de reproducción dedicada de esta
       pantalla — misma resolución específica que usa app/index.php. No se
       crea aquí: solo se crea al subir el primer contenido. --- */
$ID_lista_pantalla = lista_de_pantalla($conexion, $nombre_modulo);

/* --- Subir/asignar contenido a la pantalla --- */

if(isset($_POST['subir_contenido'])){

	$origen       = $_REQUEST["origen"] ?? 'nuevo';
	$fecha_inicio = fecha_sql($_POST["fecha_inicio"] ?? '');
	$fecha_fin    = fecha_sql($_POST["fecha_fin"] ?? '');
	$archivo      = null;

	if ($fecha_inicio !== 'NULL' && $fecha_fin !== 'NULL' && $fecha_fin <= $fecha_inicio) {
		$errores_contenido = "<p>La fecha de fin debe ser posterior a la de inicio.</p>";
	}
	elseif ($origen === 'existente' && !empty($_REQUEST['archivo_existente'])) {
		$id_biblioteca = (int) $_REQUEST['archivo_existente'];
		$sql_archivo   = mysqli_query($conexion, "SELECT `URL`, `Tipo` FROM `biblioteca` WHERE `ID` = $id_biblioteca");
		$archivo       = mysqli_fetch_array($sql_archivo);
	}
	else {
		$archivo = guardar_subida($conexion, $_FILES["foto"] ?? [], $name_user);
		if (!is_array($archivo)) {
			$errores_contenido = "<p>" . htmlspecialchars($archivo) . "</p>";
			$archivo = null;
		}
	}

	if ($archivo) {
		// Crear la lista dedicada de esta pantalla solo si todavía no existe —
		// un solo check-then-create, nunca en el GET de esta página.
		$ID_lista_pantalla = lista_de_pantalla($conexion, $nombre_modulo, $name_user);
		if (publicar_en_lista($conexion, $ID_lista_pantalla, $archivo['URL'], $archivo['Tipo'], $fecha_inicio, $fecha_fin, $name_user)) {
			header("Location: editar_modulo.php?id=$edit_id");
			exit();
		}
		$errores_contenido = "<p>No se ha podido guardar el contenido.</p>";
	}
}

/* --- Protector de pantalla propio de esta pantalla --- */

if(isset($_POST['actualizar_protector'])){

	$Tiempo_Kiosco    = (int) $_REQUEST["Tiempo_Kiosco"];
	$Tiempo_Contenido = (int) $_REQUEST["Tiempo_Contenido"];
	$URL_Destino      = mysqli_real_escape_string($conexion, trim($_REQUEST["URL_Destino"] ?? ''));
	$hoy              = date("Y-m-d H:i:s");

	$sql_config_check = mysqli_query($conexion, "SELECT * FROM `configuracion` WHERE `Modulo` = '$nombre_modulo_escaped' LIMIT 1");
	$config_check     = mysqli_fetch_array($sql_config_check);

	$protector_url  = $config_check ? $config_check['Protector_URL']  : null;
	$protector_tipo = $config_check ? $config_check['Protector_Tipo'] : null;

	if(isset($_FILES["protector"]) && $_FILES["protector"]["error"] == 0 && !empty($_FILES["protector"]["name"])){
		$foto      = $_FILES["protector"]["name"];
		$foto_type = $_FILES["protector"]["type"];
		$ruta      = $_FILES["protector"]["tmp_name"];
		$destino   = "assets/galeria/" . $foto;

		copy($ruta, $destino);

		$protector_url  = $foto;
		$protector_tipo = ($foto_type == 'video/mp4') ? 'video' : 'image';
	}

	if ($config_check) {
		$actualizar_protector = mysqli_query($conexion, "UPDATE `configuracion` SET
		                 `Tiempo_Inactividad_Kiosco` = '$Tiempo_Kiosco',
		                 `Tiempo_Inactividad_Contenido` = '$Tiempo_Contenido',
		                 `Protector_URL` = '$protector_url',
		                 `Protector_Tipo` = '$protector_tipo',
		                 `URL_Destino` = '$URL_Destino',
		                 `Fecha-Modificacion` = '$hoy',
		                 `Usuario` = '$name_user'
		               WHERE `ID` = " . (int) $config_check['ID']);
	}
	else {
		// Primera vez que esta pantalla tiene configuración propia: si no
		// subió protector nuevo, o si dejó la URL en blanco, parte de lo
		// que hoy muestra la configuración general — así crear el override
		// no apaga por accidente algo que ya funcionaba a nivel general.
		if (!$protector_url || $URL_Destino === '') {
			$sql_general = mysqli_query($conexion, "SELECT `Protector_URL`, `Protector_Tipo`, `URL_Destino` FROM `configuracion` WHERE `Modulo` IS NULL LIMIT 1");
			$general     = mysqli_fetch_array($sql_general);
			if (!$protector_url) {
				$protector_url  = $general ? $general['Protector_URL']  : 'IDT.mp4';
				$protector_tipo = $general ? $general['Protector_Tipo'] : 'video';
			}
			if ($URL_Destino === '') {
				$URL_Destino = $general ? $general['URL_Destino'] : '';
			}
		}
		$actualizar_protector = mysqli_query($conexion, "INSERT INTO `configuracion`
		                 (`Modulo`, `Tiempo_Inactividad_Kiosco`, `Tiempo_Inactividad_Contenido`, `Protector_URL`, `Protector_Tipo`, `URL_Destino`, `Fecha-Modificacion`, `Usuario`)
		               VALUES
		                 ('$nombre_modulo_escaped', '$Tiempo_Kiosco', '$Tiempo_Contenido', '$protector_url', '$protector_tipo', '$URL_Destino', '$hoy', '$name_user')");
	}

	if ($actualizar_protector){
		header("Location: editar_modulo.php?id=$edit_id");
		exit();
	}
	else {
		$errores_protector = "<p>No se ha podido guardar el protector de pantalla.</p>";
	}
}

/* --- Contenido y protector vigentes, para mostrarlos en la página --- */

$consulta_existentes = mysqli_query($conexion, "SELECT `ID`, `URL`, `Tipo` FROM `biblioteca` ORDER BY `URL` ASC");

$sql_config_pantalla = mysqli_query($conexion, "SELECT * FROM `configuracion` WHERE `Modulo` = '$nombre_modulo_escaped' LIMIT 1");
$config_pantalla     = mysqli_fetch_array($sql_config_pantalla);
$tiene_protector_propio = (bool) $config_pantalla;

if (!$config_pantalla) {
	$sql_config_general = mysqli_query($conexion, "SELECT * FROM `configuracion` WHERE `Modulo` IS NULL LIMIT 1");
	$config_pantalla    = mysqli_fetch_array($sql_config_general);
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
  <title>Editar Pantalla - IDT App</title>
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
   <div id="pageloader-overlay" class="visible incoming"><div class="loader-wrapper-outer"><div class="loader-wrapper-inner" ><div class="loader"></div></div></div></div>
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

	<div class="row pt-2 pb-2">
        <div class="col-sm-9">
			<h4 class="page-title">Editar pantalla</h4>
	   </div>
     </div>

	<div class="row">
		<div class="col-lg-12">
        	<div class="card">
        		<div class="card-header text-uppercase"><i class="fa fa-tv"></i> Datos de la pantalla</div>
            	<div class="card-body">
					<form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $edit_id; ?>" method="POST">
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Nombre de la pantalla</label>
                            <div class="col-lg-9">
								<input class="form-control" type="text" maxlength="50" name="modulo" value="<?php echo htmlspecialchars($nombre_modulo); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Ubicación</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" name="ubicacion" value="<?php echo htmlspecialchars($ubicacion_modulo); ?>" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">URL de esta pantalla</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" readonly value="<?php echo '../app/index.php?pantalla=' . $edit_id; ?>">
                                <small class="text-muted">Abre esta pantalla directamente en el kiosco, sin pasar por el selector manual.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"></label>
                            <div class="col-lg-9">
								<a href="modulos.php" class="btn btn-secondary">Cancelar</a>
                                <input type="submit" name="actualizar_modulo" class="btn btn-primary" value="Actualizar pantalla">
                            </div>
                        </div>

						<?php if(!empty($mensaje)): foreach($mensaje as $erro): ?>
						<div class="form-group row">
							<div class="col-lg-9 offset-lg-3"><?php echo $erro; ?></div>
						</div>
						<?php endforeach; endif; ?>
                    </form>
			  	</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
          <div class="card">
			  <div class="card-header text-uppercase"><i class="fa fa-file-image-o"></i> Contenido de esta pantalla</div>
            <div class="card-body">

				<?php if (!$ID_lista_pantalla): ?>
				<p class="text-muted">Esta pantalla todavía no tiene contenido propio — está usando el contenido general. Sube un archivo para asignarle contenido exclusivo.</p>
				<?php endif; ?>

				<form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $edit_id; ?>" method="POST" enctype="multipart/form-data">

					<div class="row">
						<div class="col-12">
							<div class="form-group row">
								<label class="col-lg-12 col-form-label form-control-label">Origen del archivo</label>
								<div class="col-lg-10">
									<div class="icheck-material-white d-inline-block mr-4">
										<input type="radio" id="origen_nuevo" name="origen" value="nuevo" checked onchange="toggleOrigenArchivo();">
										<label for="origen_nuevo">Subir archivo nuevo</label>
									</div>
									<div class="icheck-material-white d-inline-block">
										<input type="radio" id="origen_existente" name="origen" value="existente" onchange="toggleOrigenArchivo();">
										<label for="origen_existente">Usar contenido existente</label>
									</div>
								</div>
							</div>
						</div>

						<div class="col-12 col-lg-6 col-xl-6" id="bloque_origen_nuevo">
							<div class="form-group row">
								<label class="col-lg-12 col-form-label form-control-label">Seleccione una imagen o video:</label>
								<div class="col-sm-10">
									<input type="file" class="form-control" name="foto" id="foto" accept=".png, .jpg, .jpeg, .mp4" onchange="validarFile(this);">
								</div>
							</div>
						</div>

						<div class="col-12 col-lg-6 col-xl-6" id="bloque_origen_existente" style="display:none;">
							<div class="form-group row">
								<label class="col-lg-12 col-form-label form-control-label">Archivo ya subido:</label>
								<div class="col-lg-10">
									<select class="form-control" name="archivo_existente" id="archivo_existente">
										<option value="" selected disabled hidden>Seleccione un archivo</option>
										<?php foreach ($consulta_existentes as $ex){ ?>
										<option value="<?php echo (int) $ex['ID']; ?>"><?php echo htmlspecialchars($ex['URL']); ?> (<?php echo $ex['Tipo'] == 'video' ? 'video' : 'imagen'; ?>)</option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>

						<div class="col-12 col-lg-6 col-xl-3">
							<div class="form-group row">
								<label class="col-lg-12 col-form-label form-control-label">Desde</label>
								<div class="col-lg-12">
									<input class="form-control" type="datetime-local" name="fecha_inicio">
									<small class="text-muted">Vacío = desde ya</small>
								</div>
							</div>
						</div>

						<div class="col-12 col-lg-6 col-xl-3">
							<div class="form-group row">
								<label class="col-lg-12 col-form-label form-control-label">Hasta</label>
								<div class="col-lg-12">
									<input class="form-control" type="datetime-local" name="fecha_fin">
									<small class="text-muted">Vacío = sin fin</small>
								</div>
							</div>
						</div>
					</div>

					<?php if(!empty($errores_contenido)){ echo $errores_contenido; } ?>

					<div class="form-footer">
						<input type="submit" class="btn btn-success" name="subir_contenido" value="Agregar contenido" onclick="return validarOrigenArchivo();">
					</div>

				</form>

				<hr>

				<?php
					$lista_render    = $ID_lista_pantalla;
					$texto_sin_lista = ''; // ya lo dice el aviso de arriba
					include 'assets/php/lista_publicaciones.php';
				?>

            </div>
          </div>
        </div>
      </div>

	<div class="row">
		<div class="col-lg-12">
          <div class="card">
			  <div class="card-header text-uppercase"><i class="fa fa-clock-o"></i> Protector y toque de esta pantalla</div>
            <div class="card-body">

				<?php if (!$tiene_protector_propio): ?>
				<p class="text-muted">Esta pantalla está usando el protector y la URL de toque <a href="configuracion.php">generales</a>. Guarda estos valores para darle los suyos propios.</p>
				<?php endif; ?>

				<form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $edit_id; ?>" method="POST" enctype="multipart/form-data">
					<div class="row">
						<div class="col-12 col-lg-4">
							<div class="form-group row">
								<label class="col-lg-12 col-form-label form-control-label">Inactividad del kiosco (segundos)</label>
								<div class="col-lg-10">
									<input class="form-control" type="number" min="1" name="Tiempo_Kiosco" value="<?php echo (int) $config_pantalla['Tiempo_Inactividad_Kiosco']; ?>" required>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-4">
							<div class="form-group row">
								<label class="col-lg-12 col-form-label form-control-label">Inactividad del contenido (segundos)</label>
								<div class="col-lg-10">
									<input class="form-control" type="number" min="1" name="Tiempo_Contenido" value="<?php echo (int) $config_pantalla['Tiempo_Inactividad_Contenido']; ?>" required>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-4">
							<div class="form-group row">
								<label class="col-lg-12 col-form-label form-control-label">Nuevo protector (opcional)</label>
								<div class="col-lg-10">
									<input type="file" class="form-control" name="protector" accept=".png, .jpg, .jpeg, .mp4">
									<small class="text-muted">Actual: <?php echo htmlspecialchars($config_pantalla['Protector_URL']); ?></small>
								</div>
							</div>
						</div>
						<div class="col-12">
							<div class="form-group row">
								<label class="col-lg-12 col-form-label form-control-label">URL al tocar esta pantalla</label>
								<div class="col-lg-10">
									<input class="form-control" type="url" name="URL_Destino" value="<?php echo htmlspecialchars($config_pantalla['URL_Destino']); ?>" placeholder="https://ejemplo.com">
									<small class="text-muted">A dónde navega esta pantalla cuando el visitante la toca. Vacío = usar la URL general.</small>
								</div>
							</div>
						</div>
					</div>

					<?php if(!empty($errores_protector)){ echo $errores_protector; } ?>

					<div class="form-footer">
						<input type="submit" class="btn btn-success" name="actualizar_protector" value="Guardar protector de esta pantalla">
					</div>
				</form>

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
  <!-- Confirmación de eliminación -->
  <script src="assets/js/confirm-delete.js"></script>
  <!-- Reordenar el contenido de esta pantalla (arrastrar) -->
  <script src="assets/js/publicaciones.js"></script>

  <!--Lightbox-->
  <script src="assets/plugins/fancybox/js/jquery.fancybox.min.js"></script>

  <script>
	function toggleOrigenArchivo(){
		var esNuevo = document.getElementById('origen_nuevo').checked;
		document.getElementById('bloque_origen_nuevo').style.display = esNuevo ? '' : 'none';
		document.getElementById('bloque_origen_existente').style.display = esNuevo ? 'none' : '';
		if (esNuevo) {
			document.getElementById('archivo_existente').value = '';
		} else {
			document.getElementById('foto').value = '';
		}
	}

	function validarOrigenArchivo(){
		var esNuevo = document.getElementById('origen_nuevo').checked;
		if (esNuevo && !document.getElementById('foto').value) {
			alert('Selecciona una imagen o video para subir.');
			return false;
		}
		if (!esNuevo && !document.getElementById('archivo_existente').value) {
			alert('Selecciona un archivo ya subido de la lista.');
			return false;
		}
		return true;
	}

	function validarFile(all)
	{
	    var extensiones_permitidas = [".png",".jpg",".jpeg",".mp4"];
	    var tamano = 80; // EXPRESADO EN MB.
	    var rutayarchivo = all.value;
	    var ultimo_punto = all.value.lastIndexOf(".");
	    var extension = rutayarchivo.slice(ultimo_punto, rutayarchivo.length);
	    if(extensiones_permitidas.indexOf(extension.toLowerCase()) == -1)
	    {
	        alert("Extensión de archivo no valida");
	        all.value = "";
	        return;
	    }
	    if((all.files[0].size / 1048576) > tamano)
	    {
	        alert("El archivo no puede superar los "+tamano+"MB");
	        all.value = "";
	        return;
	    }
	}

  </script>

</body>
</html>
