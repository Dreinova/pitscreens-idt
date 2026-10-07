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

/* consulta de frame */

$consulta_categoria = mysqli_query($conexion, "SELECT * FROM `frame`");
$raw_Frame = mysqli_fetch_array($consulta_categoria);

$ID_Frame = $raw_Frame['ID'];
$URL_Frame = $raw_Frame['URL'];

/* Actualizar imagen */

if(isset($_POST['Actualizar'])){
	
	$IDFrame = $_POST['ID'];
	$URLFrame = $_POST["URL"];
	$hoy = date("Y-m-d H:i:s");
	
	$actualizar_img = mysqli_query($conexion,"UPDATE `frame` SET `URL` = '$URLFrame', `Fecha-Modificacion` = '$hoy', `Usuario` = '$name_user' WHERE `frame`.`ID` = 1");
	
		if ($actualizar_img){
			echo "<p>Se han guardado los cambios correctamente.</p>";
			header("Location: frame.php");
		}

		else{
			$errores = "<p>No se han guardado los cambios, intentelo mas tarde.</p>";
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
  <title>Edición Frame de Contenido - IDT App</title>
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
		<div class="col-lg-6">
        	<div class="card">
			<div class="card-header"><i class="fa fa-edit"></i>Actualizar URL Frame</div>
            	<div class="card-body">
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
						
						<input type="hidden" name="ID" value="<?php echo $ID_Frame;?>">
						
						<div class="row">
							
							<div class="col-12 col-lg-8 col-xl-8">
								<div class="form-group row">
									<label class="col-lg-12 col-form-label form-control-label">Nueva URL</label>
									<div class="col-lg-12">
										<input class="form-control" type="url" maxlength="150" name="URL" value="<?php echo $URL_Frame; ?>" placeholder="Ingrese una URL o enlace" required>
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
							<a href="frame.php" class="btn btn-secondary">Cancelar</a>
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
          © Instituto Distrital de Turismo
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