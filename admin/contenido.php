<?php
/**
 * contenido.php — Módulo Administrador
 *
 * Gestión del contenido multimedia del sistema (imágenes y videos).
 * Permite a los administradores y colaboradores:
 *   - Subir nuevos archivos (PNG, JPEG, MP4) al servidor.
 *   - Asignar cada archivo a una lista de reproducción.
 *   - Definir el orden de reproducción y el estado (Activo/Inactivo).
 *   - Ver la galería completa de imágenes y videos.
 *   - Editar o eliminar elementos existentes.
 *
 * Formatos aceptados: .png, .jpg/.jpeg, .mp4 (máximo 80 MB).
 * Los archivos se guardan físicamente en: admin/assets/galeria/
 * Los registros se almacenan en la tabla `contenido` de la BD.
 *
 * Campos de la tabla `contenido`:
 *   URL              — Nombre del archivo en disco
 *   Tipo             — 'image' o 'video'
 *   Estado           — 1 (Activo) / 0 (Inactivo)
 *   Orden            — Número de posición en la reproducción
 *   Lista-Reproduccion — FK a la tabla `lista-reproduccion`
 *   Fecha_Modificación — Fecha y hora de la última subida/edición
 *   Usuario          — Nombre del usuario que realizó la acción
 */

date_default_timezone_set('America/Bogota');
include 'assets/php/Conexion_DB.php';

// Sesión administrativa con 2 horas de duración
session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime'  => 7200,
]);

/* Proteger la página: redirigir al login si no hay sesión activa */
if(!isset($_SESSION['logeado'])):
	header('Location: index.php');
endif;

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

/* --- Obtener todas las listas de reproducción para el selector del formulario --- */
$consulta_programacion = mysqli_query($conexion, "SELECT * FROM `lista-reproduccion`");

/* --- Archivos ya subidos (sin repetir), para reutilizarlos sin duplicar
       el archivo físico en disco --- */
$consulta_existentes = mysqli_query($conexion, "SELECT DISTINCT URL, Tipo FROM contenido ORDER BY URL ASC");

/* --- Procesamiento del formulario de subida de contenido --- */
if(isset($_POST['enviar'])){

	// Datos del formulario
	$Estado             = $_REQUEST["Estado"];
	$Lista_reporduccion = $_REQUEST["Lista"];
	$Orden              = $_REQUEST["orden"];
	$fecha              = date("Y-m-d H:i:s");
	$origen             = $_REQUEST["origen"] ?? 'nuevo';

	if ($origen === 'existente' && !empty($_REQUEST['archivo_existente'])) {

		// Reutilizar un archivo que ya está en assets/galeria/: mismo
		// URL/Tipo, nueva fila en `contenido` — sin volver a subir ni
		// duplicar el archivo físico.
		list($tipo_existente, $foto) = explode('|', $_REQUEST['archivo_existente'], 2);
		$foto           = mysqli_real_escape_string($conexion, $foto);
		$tipo_existente = mysqli_real_escape_string($conexion, $tipo_existente);

		$sub = mysqli_query($conexion,
            "INSERT INTO contenido (URL, Tipo, Estado, Orden, `Lista-Reproduccion`, Fecha_Modificación, Usuario)
             VALUES ('$foto', '$tipo_existente', '$Estado', '$Orden', '$Lista_reporduccion', '$fecha', '$name_user')"
        );
		if ($sub){
			// Marca la lista padre como modificada, para que el mecanismo de
			// sincronización en vivo del kiosco (app/tiempo.php) detecte el cambio.
			mysqli_query($conexion, "UPDATE `lista-reproduccion` SET `Fecha-Modificacion` = '$fecha' WHERE `ID` = '$Lista_reporduccion'");
			header("Location: contenido.php"); exit();
		}
		else{ $errores = "<p>No se ha podido asignar el archivo seleccionado.</p>"; }
	}
	else {

		// Datos del archivo subido
		$foto      = $_FILES["foto"]["name"];
		$foto_type = $_FILES["foto"]["type"];
		$ruta      = $_FILES["foto"]["tmp_name"];  // Ruta temporal en el servidor
		$destino   = "assets/galeria/" . $foto;     // Ruta final donde se guardará

		// Copiar el archivo desde la ubicación temporal a la galería
		copy($ruta, $destino);

		// Insertar registro según el tipo de archivo detectado por MIME type
		if ($foto_type == 'image/png' || $foto_type == 'image/jpeg') {
			$sub_img = mysqli_query($conexion,
                "INSERT INTO contenido (URL, Tipo, Estado, Orden, `Lista-Reproduccion`, Fecha_Modificación, Usuario)
                 VALUES ('$foto', 'image', '$Estado', '$Orden', '$Lista_reporduccion', '$fecha', '$name_user')"
            );
			if ($sub_img){
				mysqli_query($conexion, "UPDATE `lista-reproduccion` SET `Fecha-Modificacion` = '$fecha' WHERE `ID` = '$Lista_reporduccion'");
				header("Location: contenido.php"); exit();
			}
			else{ $errores = "<p>No se ha subido correctamente la imagen.</p>"; }
		}
		elseif ($foto_type == 'video/mp4') {
			$sub_video = mysqli_query($conexion,
                "INSERT INTO contenido (URL, Tipo, Estado, Orden, `Lista-Reproduccion`, Fecha_Modificación, Usuario)
                 VALUES ('$foto', 'video', '$Estado', '$Orden', '$Lista_reporduccion', '$fecha', '$name_user')"
            );
			if ($sub_video){
				mysqli_query($conexion, "UPDATE `lista-reproduccion` SET `Fecha-Modificacion` = '$fecha' WHERE `ID` = '$Lista_reporduccion'");
				header("Location: contenido.php"); exit();
			}
			else{ $errores = "<p>No se ha subido correctamente el video MP4.</p>"; }
		}
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
  <!-- Dropzone css -->
  <link href="assets/plugins/dropzone/css/dropzone.css" rel="stylesheet" type="text/css">
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
			<h4 class="page-title">Inicio</h4>
	   </div>
     </div>
    <!-- Fin Migas de pan-->
		
	<!-- Inicio Secciones -->
		
<div class="row">
        <div class="col-lg-12">
          <div class="card">
			  <div class="card-header text-uppercase"><i class="fa fa-file-image-o"></i> Seleccione una imagen o video para subir al sistema</div>
            <div class="card-body">
              
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
			
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
								<label for="origen_existente">Usar uno ya subido</label>
							</div>
							<br><small class="text-muted">Si el video o imagen ya está en la galería, reutilízalo en vez de subirlo otra vez — evita archivos duplicados.</small>
						</div>
					</div>
				</div>

				<div class="col-12 col-lg-6 col-xl-6" id="bloque_origen_nuevo">
					<div class="form-group row">
						<label for="input-2" class="col-lg-12 col-form-label form-control-label" >Seleccione una imagen o video:</label>
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
								<option value="<?php echo $ex['Tipo']; ?>|<?php echo htmlspecialchars($ex['URL']); ?>"><?php echo htmlspecialchars($ex['URL']); ?> (<?php echo $ex['Tipo'] == 'video' ? 'video' : 'imagen'; ?>)</option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>

				<div class="col-12 col-lg-6 col-xl-6">
					<div class="form-group row">
						<label class="col-lg-12 col-form-label form-control-label">Estado de la imagen o video</label>
						<div class="col-lg-10">
							<select class="form-control" id="default-select" name="Estado">
								<option value="" selected disabled hidden>Seleccione una opción</option>
								<option value="1">Activo</option>
								<option value="0">Inactivo</option>
							</select>
						</div>
					</div>
				</div>
				
				<div class="col-12 col-lg-6 col-xl-6">
					<div class="form-group row">
						<label class="col-lg-12 col-form-label form-control-label">Numero de pagina</label>
						<div class="col-lg-10">
							<input class="form-control" type="number" name="orden" value="" placeholder="Coloque el numero en el que se visualizara" required>
						</div>
					</div>
				</div>
				
				<div class="col-12 col-lg-6 col-xl-6">
					<div class="form-group row">
						<label class="col-lg-12 col-form-label form-control-label">Seleccione la lista de reproducción asignada</label>
						<div class="col-lg-10">
							<select class="form-control" id="large-select" name="Lista" required>
								<option value="" selected disabled hidden>Seleccione una opción</option>
								<?php
									foreach ($consulta_programacion as $datos_sliders){ 
										$ID_slider = $datos_sliders['ID'];
										$nombre_slider = $datos_sliders['Nombre'];
								?>
								<option value="<?php echo $ID_slider; ?>"><?php echo $nombre_slider; ?></option>
								<?php } ?>
							</select>
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
				<input type="submit" class="btn btn-success" name="enviar" value="Guardar" onclick="return validarOrigenArchivo();">
			</div>



        </form>
				
            </div>
          </div>
        </div>
      </div>


	<div class="row">
			<div class="col-6">
			  <div class="card">
				<div class="card-header text-uppercase"><i class="fa fa-image"></i> Galeria de Imagenes</div>
				<div class="card-body">
				  <div class="row">

					 <?php
        				$consulta_img = mysqli_query($conexion,"SELECT `contenido`.`ID`, `contenido`.`URL`, `contenido`.`Tipo`, `contenido`.`Estado`, `contenido`.`Orden`, `lista-reproduccion`.`Nombre` FROM `contenido` INNER JOIN `lista-reproduccion` ON `contenido`.`Lista-Reproduccion` = `lista-reproduccion`.`ID` WHERE `contenido`.`Tipo` ='image' ORDER BY `contenido`.`ID` ASC");
        				while($res = mysqli_fetch_array($consulta_img)){
							$ID_img = $res["ID"];
							$url_img = $res["URL"];
							$tipo = $res["Tipo"];
							$Estado = $res["Estado"];
								if($Estado == '1'){
									$Estado = 'Activo';
								}
								if($Estado == '0'){
									$Estado = 'Inactivo';
								}
							$Orden = $res["Orden"];
							$nombre_slide = $res["Nombre"];
							
							echo '<div class="col-md-6 col-lg-3 col-xl-3" style="margin: 1% 0%;">';
							
							echo '<a href="assets/galeria/'.$url_img.'" data-fancybox="images" data-caption="'.$url_img.'">';
							echo '<img src="assets/galeria/'.$url_img.'" class="lightbox-thumb img-thumbnail">';
							echo '</a>';
							
							echo '<strong>Nombre: </strong>'.$url_img."<br>";
							echo '<strong>Estado: </strong>'.$Estado."<br>";
							echo '<strong>Lista de reproduccion: </strong>'.$nombre_slide."<br>";
							echo '<strong>Orden: </strong>'.$Orden."<br><br>";
							echo '<a href="editar_contenido.php?id='.$ID_img;
							echo '" title="Editar" >';
							echo '<i class="fa fa-edit"></i> Editar ';
							echo '</a>';
							
							echo '<a href="assets/php/eliminar_img.php?id='.$ID_img.'&user='.$name_user;
							echo '" title="Eliminar" onclick="javascript:return asegurar();" >';
							echo '<i class="fa fa-trash"></i> Eliminar ';
							echo '</a>';
							
							echo '</div>';
        				}
        			?>

				  </div>
				</div>
			  </div>
			</div>

			<div class="col-6">
			  <div class="card">
				<div class="card-header text-uppercase"><i class="fa fa-image"></i> Galeria de Video</div>
				<div class="card-body">
				  <div class="row">
					  
					  <?php
        				$consulta_video = mysqli_query($conexion,"SELECT `contenido`.`ID`, `contenido`.`URL`, `contenido`.`Tipo`, `contenido`.`Estado`, `contenido`.`Orden`, `lista-reproduccion`.`Nombre` FROM `contenido` INNER JOIN `lista-reproduccion` ON `contenido`.`Lista-Reproduccion` = `lista-reproduccion`.`ID` WHERE `contenido`.`Tipo` ='video' ORDER BY `contenido`.`ID` ASC");
        				while($res = mysqli_fetch_array($consulta_video)){
							$ID_img = $res["ID"];
							$url_img = $res["URL"];
							$tipo = $res["Tipo"];
							$Estado = $res["Estado"];
								if($Estado == '1'){
									$Estado = 'Activo';
								}
								if($Estado == '0'){
									$Estado = 'Inactivo';
								}
							$Orden = $res["Orden"];
							$nombre_slide = $res["Nombre"];
							
							echo '<div class="col-md-6 col-lg-3 col-xl-3" style="margin: 1% 0%;">';
							echo '<video width="100%" controls><source src="assets/galeria/'.$url_img.'" type="video/mp4"></video>';
							
							echo '<strong>Nombre: </strong>'.$url_img."<br>";
							echo '<strong>Estado: </strong>'.$Estado."<br>";
							echo '<strong>Lista de reproduccion: </strong>'.$nombre_slide."<br>";
							echo '<strong>Orden: </strong>'.$Orden."<br><br>";
							echo '<a href="editar_contenido.php?id='.$ID_img;
							echo '" title="Editar" >';
							echo '<i class="fa fa-edit"></i> Editar ';
							echo '</a>';
							
							echo '<a href="assets/php/eliminar_img.php?id='.$ID_img.'&user='.$name_user;
							echo '" title="Eliminar" onclick="javascript:return asegurar();" >';
							echo '<i class="fa fa-trash"></i> Eliminar ';
							echo '</a>';
							
							echo '</div>';
        				}
        			?>

				  </div>
				</div>
			  </div>
			</div>
		  </div>


<!--End Row-->
		
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
	
  <!-- Scripts de Galeria -->
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
    //EXTENSIONES Y TAMANO PERMITIDO.
    var extensiones_permitidas = [".png",".jpg",".mp4"];
    var tamano = 80; // EXPRESADO EN MB.
    var rutayarchivo = all.value;
    var ultimo_punto = all.value.lastIndexOf(".");
    var extension = rutayarchivo.slice(ultimo_punto, rutayarchivo.length);
    if(extensiones_permitidas.indexOf(extension) == -1)
    {
        alert("Extensión de archivo no valida");
        document.getElementById(foto).value = "";
		location.reload();
        return; // Si la extension es no válida ya no chequeo lo de abajo.
    }
    if((all.files[0].size / 1048576) > tamano)
    {
        alert("El archivo no puede superar los "+tamano+"MB");
        document.getElementById(foto).value = "";
		location.reload();
        return;
    }
}
</script>
	
  <script>
  	function asegurar (){
		  rc = confirm("¿Está seguro de eliminar este medio?");
		  return rc;
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

  <!--Lightbox-->
  <script src="assets/plugins/fancybox/js/jquery.fancybox.min.js"></script>
	
  <!-- Dropzone JS  -->
    <script src="assets/plugins/dropzone/js/dropzone.js"></script>
	<script src="assets/plugins/summernote/dist/summernote-bs4.min.js"></script>
	<script>
	   $('#summernoteEditor').summernote({
				height: 400,
				tabsize: 2
			});
	 </script>
<!--Select Plugins Js-->
    <script src="assets/plugins/select2/js/select2.min.js"></script>
	
<?php 
	mysqli_close($conexion);
?>

</body>
</html>