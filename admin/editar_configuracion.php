<?php
/**
 * editar_configuracion.php — Módulo Administrador
 *
 * Edita la fila única de `configuracion`: tiempos de inactividad del
 * kiosco y el video/imagen de respaldo del protector de pantalla. La
 * subida de archivo es opcional — si no se selecciona uno nuevo, se
 * conserva el que ya estaba configurado.
 */

date_default_timezone_set('America/Bogota');
include 'assets/php/Conexion_DB.php';

session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime' => 7200,
]);

/*  verificacion login  */
if(!isset($_SESSION['logeado'])):
	header('Location: index.php');
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

/* datos actuales de configuración */

// Esta página edita siempre la configuración GENERAL (la de respaldo para
// pantallas sin protector propio) — filtra explícitamente por Modulo IS
// NULL para no traer por error una fila específica de alguna pantalla.
$consulta_config = mysqli_query($conexion, "SELECT * FROM `configuracion` WHERE `Modulo` IS NULL LIMIT 1");
$datos_config = mysqli_fetch_array($consulta_config);
$ID_config = $datos_config['ID'];
$tiempo_kiosco_actual = $datos_config['Tiempo_Inactividad_Kiosco'];
$tiempo_contenido_actual = $datos_config['Tiempo_Inactividad_Contenido'];
$protector_url_actual = $datos_config['Protector_URL'];
$protector_tipo_actual = $datos_config['Protector_Tipo'];

/* Actualizar configuración */

if(isset($_POST['enviar'])){

	$Tiempo_Kiosco = (int) $_REQUEST["Tiempo_Kiosco"];
	$Tiempo_Contenido = (int) $_REQUEST["Tiempo_Contenido"];
	$hoy = date("Y-m-d H:i:s");

	$protector_url = $protector_url_actual;
	$protector_tipo = $protector_tipo_actual;

	// Si se subió un archivo nuevo, lo copiamos y reemplazamos la referencia;
	// si no, se conserva el protector de pantalla que ya estaba configurado.
	if(isset($_FILES["protector"]) && $_FILES["protector"]["error"] == 0 && !empty($_FILES["protector"]["name"])){
		$foto      = $_FILES["protector"]["name"];
		$foto_type = $_FILES["protector"]["type"];
		$ruta      = $_FILES["protector"]["tmp_name"];
		$destino   = "assets/galeria/" . $foto;

		copy($ruta, $destino);

		$protector_url = $foto;
		$protector_tipo = ($foto_type == 'video/mp4') ? 'video' : 'image';
	}

	$sql_config = "UPDATE `configuracion` SET
	                 `Tiempo_Inactividad_Kiosco` = '$Tiempo_Kiosco',
	                 `Tiempo_Inactividad_Contenido` = '$Tiempo_Contenido',
	                 `Protector_URL` = '$protector_url',
	                 `Protector_Tipo` = '$protector_tipo',
	                 `Fecha-Modificacion` = '$hoy',
	                 `Usuario` = '$name_user'
	               WHERE `ID` = $ID_config";

	$actualizar = mysqli_query($conexion, $sql_config);

	if ($actualizar){
		header("Location: configuracion.php");
	}
	else{
		$errores = "<p>No se ha guardado correctamente la configuración.</p>";
	}
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
  <title>Editar Configuración - IDT App</title>
  <!--favicon-->
  <link rel="icon" href="assets/images/Favicon.png" type="image/x-icon">
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
        <div class="col-sm-9">
			<h4 class="page-title">Editar Configuración del Kiosco</h4>
	   </div>
     </div>
    <!-- Fin Migas de pan-->

	<!-- Inicio Secciones -->

	<div class="row">
		<div class="col-lg-12">
        	<div class="card">
			<div class="card-header"><i class="fa fa-edit"></i> Tiempos de inactividad y protector de pantalla</div>
            	<div class="card-body">
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">

						<div class="row">

							<div class="col-12 col-lg-6 col-xl-6">
								<div class="form-group row">
									<label class="col-lg-12 col-form-label form-control-label">Inactividad en documento/registro (segundos)</label>
									<div class="col-lg-10">
										<input class="form-control" type="number" min="5" name="Tiempo_Kiosco" value="<?php echo $tiempo_kiosco_actual; ?>" required>
										<small class="text-muted">Hoy: <?php echo $tiempo_kiosco_actual; ?> segundos.</small>
									</div>
								</div>
							</div>

							<div class="col-12 col-lg-6 col-xl-6">
								<div class="form-group row">
									<label class="col-lg-12 col-form-label form-control-label">Inactividad en pantalla de contenido (segundos)</label>
									<div class="col-lg-10">
										<input class="form-control" type="number" min="5" name="Tiempo_Contenido" value="<?php echo $tiempo_contenido_actual; ?>" required>
										<small class="text-muted">Hoy: <?php echo $tiempo_contenido_actual; ?> segundos.</small>
									</div>
								</div>
							</div>

							<div class="col-12">
								<div class="form-group row">
									<label for="input-protector" class="col-lg-12 col-form-label form-control-label">Protector de pantalla (opcional — PNG, JPEG o MP4, máximo 80 MB)</label>
									<div class="col-sm-10">
										<input type="file" class="form-control" name="protector" id="input-protector" accept=".png, .jpg, .jpeg, .mp4" onchange="validarFile(this);">
										<small class="text-muted">Actual: <?php echo $protector_url_actual; ?>. Déjalo vacío para conservarlo.</small>
									</div>
								</div>
							</div>

						</div>

						<?php
							if(!empty($errores)){
								echo $errores;
							}
						?>
						<div class="form-footer">
							<a href="configuracion.php" class="btn btn-secondary">Cancelar</a>
							<input type="submit" class="btn btn-success" name="enviar" value="Guardar">
						</div>

                    </form>
			  	</div>
			</div>
		</div>
	</div>

	<!-- Inicio Secciones -->

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
          Instituto Distrital de Turismo — Alcaldía Mayor de Bogotá D.C.
        </div>
      </div>
    </footer>
	<!--End footer-->

  </div><!--End wrapper-->

  <!-- Scripts de validación de archivo -->
<script>
function validarFile(all)
{
    var extensiones_permitidas = [".png",".jpg",".jpeg",".mp4"];
    var tamano = 80; // EXPRESADO EN MB.
    var rutayarchivo = all.value;
    var ultimo_punto = all.value.lastIndexOf(".");
    var extension = rutayarchivo.slice(ultimo_punto, rutayarchivo.length).toLowerCase();
    if(extensiones_permitidas.indexOf(extension) == -1)
    {
        alert("Extensión de archivo no válida");
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

  <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>

  <!-- simplebar js -->
  <script src="assets/plugins/simplebar/js/simplebar.js"></script>
  <!-- sidebar-menu js -->
  <script src="assets/js/sidebar-menu.js"></script>

  <!-- Custom scripts -->
  <script src="assets/js/app-script.js"></script>

</body>
</html>
