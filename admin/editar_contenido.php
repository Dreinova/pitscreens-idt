<?php
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

/* consulta de Programación */

$consulta_programacion = mysqli_query($conexion,"SELECT * FROM `lista-reproduccion` ");

/* Consulta imagen */

$id = $_GET['id'];
$consulta_imagen = mysqli_query($conexion,"SELECT * FROM `contenido` WHERE ID = $id");
$datos_img = mysqli_fetch_array($consulta_imagen);

$ID_img = $datos_img["ID"];
$url_img = $datos_img["URL"];
$tipo = $datos_img["Tipo"];
$estado = $datos_img["Estado"];
$orden = $datos_img["Orden"];

/* Actualizar imagen */

if(isset($_POST['Actualizar'])){
	
	$id_form = $_POST["ID"];
	$Estado = $_POST["Estado"];
	$orden = $_POST["orden"];
	$lista_reproduccion = $_POST["Lista"];
	$fecha = $hoy = date("Y-m-d H:i:s");
	
	$actualizar_img = mysqli_query($conexion,"UPDATE `contenido` SET `Estado` = '$Estado', `Orden` = '$orden', `Lista-Reproduccion` = '$lista_reproduccion', `Usuario` = '$name_user' WHERE `ID` = $id_form");
	
		if ($actualizar_img){
			echo "<p>Se han guardado los cambios correctamente.</p>";
			header("Location: contenido.php");
		}

		else{
			$errores = "<p>No se han guardado los cambios, intentelo mas tarde.</p>";
			echo "<p>No se han guardado los cambios, intentelo mas tarde.</p>";
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
  <title>Galeria - IDT App by Electronika</title>
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
  
</head>


<body class="bg-theme bg-theme9">

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
			  <div class="card-header text-uppercase"><i class="fa fa-file-image-o"></i> Actualice los datos de la imagen</div>
            <div class="card-body">
              
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
			
			<div class="row">
				<input type="hidden" name="ID" value="<?php echo $id; ?>">
				
				<div class="col-12 col-lg-3 col-xl-3">
					<div class="form-group row">
						<div class="col-md-12 col-lg-12 col-xl-12">
							<?php
							if($tipo == 'image'){
								echo'
								<a href="assets/galeria/'; echo $url_img; echo'" data-fancybox="images" data-caption="'; echo $url_img; echo'">
								<img src="assets/galeria/'; echo $url_img; echo'" class="lightbox-thumb img-thumbnail">
								</a>
								';
							}
							if($tipo == 'video'){
								echo '<video width="100%" controls><source src="assets/galeria/'.$url_img.'" type="video/mp4"></video>';
							}
							?>
						</div>
					</div>
				</div>
				
				<div class="col-12 col-lg-3 col-xl-3">
					<div class="form-group row">
						<label class="col-lg-12 col-form-label form-control-label">Estado de la imagen o video</label>
						<div class="col-lg-12">
							<select class="form-control" id="default-select" name="Estado">
								<?php
									if($estado == '1'){
										echo'
										<option value="1" selected >Activo</option>
										<option value="0">Inactivo</option>
										';
									}
									if($estado == '0'){
										echo'
										<option value="1" >Activo</option>
										<option value="0" selected >Inactivo</option>
										';
									}
								?>
							</select>
						</div>
					</div>
				</div>
				
				<div class="col-12 col-lg-3 col-xl-3">
					<div class="form-group row">
						<label class="col-lg-12 col-form-label form-control-label">Numero de pagina</label>
						<div class="col-lg-12">
							<input class="form-control" type="number" name="orden" value="<?php echo $orden; ?>" placeholder="Coloque el numero en el que se visualizara" required>
						</div>
					</div>
				</div>
				
				<div class="col-12 col-lg-3 col-xl-3">
					<div class="form-group row">
						<label class="col-lg-12 col-form-label form-control-label">Seleccione la lista de reproducción asignada</label>
						<div class="col-lg-12">
							<select class="form-control" id="default-select" name="Lista" required>
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
				if(!empty($errores)):
					foreach($errores as $erro):
						echo $erro;
					endforeach;
				endif;
			?>
			<div class="form-footer">
				<a href="contenido.php" class="btn btn-secondary">Cancelar</a>
				<input type="submit" class="btn btn-success" name="Actualizar" value="Actualizar">
			</div>
			
			
            
        </form>  
				
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
          Desarrollado por Electronika © 2020 | Todos los derechos reservados.
        </div>
      </div>
    </footer>
	<!--End footer-->
	
  </div><!--End wrapper-->
	
  <!-- Scripts de Galeria -->
<script>
function validarFile(all)
{
    //EXTENSIONES Y TAMANO PERMITIDO.
    var extensiones_permitidas = [".jpg",".png"];
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
	
<?php 
	mysqli_close($conexion);
?>

</body>
</html>