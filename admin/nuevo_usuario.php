<?php
/**
 * nuevo_usuario.php — Módulo Administrador
 *
 * Creación de un usuario nuevo del panel. Formulario separado del
 * listado (usuarios.php).
 */

include 'assets/php/Conexion_DB.php';

session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime' => 7200,
]);

if(!isset($_SESSION['logeado'])){
	header('Location: index.php');
	exit();
}

$id = $_SESSION['id_Correo'];
$sql = "SELECT usuarios.ID, usuarios.Nombre, usuarios.Correo, usuarios.Foto_Usuario, funciones_usuario.Funcion FROM usuarios INNER JOIN funciones_usuario ON usuarios.Funcion = funciones_usuario.ID WHERE usuarios.ID = '$id' ";
$resultado = mysqli_query($conexion, $sql);
$datos = mysqli_fetch_array($resultado);

$name_user = $datos['Nombre'];
$mail_user = $datos['Correo'];
$foto_user = $datos['Foto_Usuario'];
$funcion_user = $datos['Funcion'];

/* Nuevo perfil de Usuario */

if (isset($_POST['nuevo_user'])) {

    if(is_uploaded_file($_FILES['foto_usuario']['tmp_name'])) {

        $ruta = "assets/img_users/";
        $nombrefinal= trim ($_FILES['foto_usuario']['name']);
		$nombrefinal= preg_replace('[\s+]', ' ', $nombrefinal);

        $upload= $ruta . $nombrefinal;

        if(move_uploaded_file($_FILES['foto_usuario']['tmp_name'], $upload)) {
            $Nombre  = $_POST["Nombre"];
            $Correo  = $_POST["Correo"];
			$funcion  = $_POST["funcion"];
			$Contrasena  = $_POST["Contrasena"];
			$Contrasena = md5($Contrasena);

			$nuevo_user = "INSERT INTO `usuarios` (Nombre, Correo, Foto_Usuario, Funcion, Password) VALUES ('$Nombre', '$Correo', '$upload', '$funcion', '$Contrasena')";

			mysqli_query($conexion, $nuevo_user);

			header("Location: usuarios.php");
			exit();
        }
    }
	else{
		$Nombre  = $_POST["Nombre"];
		$Correo  = $_POST["Correo"];
		$funcion  = $_POST["funcion"];
		$Contrasena  = $_POST["Contrasena"];
		$Contrasena = md5($Contrasena);

		$nuevo_user = "INSERT INTO `usuarios` (Nombre, Correo, Funcion, Password) VALUES ('$Nombre', '$Correo', '$funcion', '$Contrasena')";

		mysqli_query($conexion, $nuevo_user);

		header("Location: usuarios.php");
		exit();
	}
}

mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <title>Nuevo Usuario - IDT App</title>
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
			<h4 class="page-title">Nuevo usuario</h4>
	   </div>
     </div>

	<div class="row">
		<div class="col-lg-12">
        	<div class="card">
            	<div class="card-body">
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" >
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Nombre</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" maxlength="50" name="Nombre" value="" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Correo</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="email" name="Correo" value="" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Foto de perfil</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="file" name="foto_usuario" size="150" maxlength="150" accept="image/*">
                            </div>
                        </div>

						<div class="form-group row">
							<label for="basic-select" class="col-sm-3 col-form-label">Función</label>
							<div class="col-sm-9">
								<select class="form-control" id="default-select" name="funcion" required>
									<option value="" selected="" disabled="" hidden="">Seleccione una opción</option>
									<option value="1">Administrador</option>
							 		<option value="2">Colaborador</option>
								</select>
							</div>
						</div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Contraseña</label>
                            <div class="col-lg-9">
                                <input class="form-control" name="Contrasena" type="password" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"></label>
                            <div class="col-lg-9">
                                <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
                                <input type="submit" name="nuevo_user" class="btn btn-primary" value="Crear usuario">
                            </div>
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
