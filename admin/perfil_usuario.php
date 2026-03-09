<?php
include 'assets/php/Conexion_DB.php';

session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime' => 7200,
]);

/*  verificacion login  */
if(!isset($_SESSION['logeado'])){
	header('Location: index.php');
}

/* datos de usuario Logeado */

$id = $_SESSION['id_Correo'];
$sql = "SELECT usuarios.ID, usuarios.Nombre, usuarios.Correo, usuarios.Foto_Usuario, funciones_usuario.Funcion FROM usuarios INNER JOIN funciones_usuario ON usuarios.Funcion = funciones_usuario.ID WHERE usuarios.ID = '$id' ";
$resultado = mysqli_query($conexion, $sql);
$datos = mysqli_fetch_array($resultado);

$name_user = $datos['Nombre'];
$mail_user = $datos['Correo'];
$foto_user = $datos['Foto_Usuario'];
$funcion_user = $datos['Funcion'];

/* datos Perfil de Usuario */

if (isset($_POST['actualizar_user'])) { 

    if(is_uploaded_file($_FILES['foto_usuario']['tmp_name'])) { 
     
      // creamos las variables para subir a la db
        $ruta = "assets/img_users/"; 
        $nombrefinal= trim ($_FILES['foto_usuario']['name']); //Eliminamos los espacios en blanco
		$nombrefinal= preg_replace('[\s+]', ' ', $nombrefinal);
        
        $upload= $ruta . $nombrefinal;

        if(move_uploaded_file($_FILES['foto_usuario']['tmp_name'], $upload)) { //movemos el archivo a su ubicacion 
			$ID_user = $_POST["ID"]; 
        	$Nombre  = $_POST["Nombre"]; 
            $Correo  = $_POST["Correo"];
			$funcion  = $_POST["funcion"];
			$Contrasena  = $_POST["Contrasena"];
			$Contrasena = md5($Contrasena);
			
			$actualizacion = "UPDATE usuarios SET Nombre = '$Nombre', Correo = '$Correo', Foto_Usuario = '$upload', Funcion = '$funcion', Password = '$Contrasena' WHERE usuarios.ID = $ID_user"; 
			
			mysqli_query($conexion, $actualizacion); 
			
			header("Location: perfil_usuario.php");
        }  	
    } 
	else{
		$ID_user = $_POST["ID"];
		$Nombre  = $_POST["Nombre"]; 
		$Correo  = $_POST["Correo"];
		$funcion  = $_POST["funcion"];
		$Contrasena  = $_POST["Contrasena"];
		$Contrasena = md5($Contrasena);
		
		$actualizacion = "UPDATE usuarios SET Nombre = '$Nombre', Correo = '$Correo', Funcion = '$funcion', Password = '$Contrasena' WHERE usuarios.ID = $ID_user"; 
		
		mysqli_query($conexion, $actualizacion);
		
		header("Location: perfil_usuario.php");
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
  <title>Datos de usuario - IDT App by Electronika</title>
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
    <!--Inicio Migas de pan-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
			<h4 class="page-title">Perfil de usuario</h4>
	   </div>
     </div>
    <!-- Fin Migas de pan-->

      <div class="row">

        <div class="col-lg-12">
           <div class="card">
            <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
                <li class="nav-item">
                    <a href="javascript:void();" data-target="#profile" data-toggle="pill" class="nav-link active"><i class="icon-user"></i> <span class="hidden-xs">Perfil de usuario</span></a>
                </li>

                <li class="nav-item">
                    <a href="javascript:void();" data-target="#edit" data-toggle="pill" class="nav-link"><i class="icon-note"></i> <span class="hidden-xs">Editar perfil</span></a>
                </li>
            </ul>
            <div class="tab-content p-3">
                <div class="tab-pane active" id="profile">
                    <h5 class="mb-3">Perfil de usuario</h5>
					<!-- Row -->
				
					<div class="col-12 col-lg-4">
						<div class="card">
							<img src="<?php echo $foto_user; ?>" class="card-img-top" alt="Card image cap">
								<div class="card-body">
								<h4 class="card-title">Perfil de usuario</h4>
								<h6>Nombre:</h6>
								<p><?php echo $name_user; ?></p>
								<hr>
								<h6>Función:</h6>
								<p><?php echo $funcion_user; ?></p>
							</div>
						</div>
					</div>
				
                    <!--/row-->
				
                </div>
			   
                <div class="tab-pane" id="edit">
					
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" >
						<input type="hidden" name="ID" value="<?php echo $id; ?>" >
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Nombre</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" maxlength="50" name="Nombre" value="<?php echo $name_user; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Correo</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="email" name="Correo" value="<?php echo $mail_user; ?>">
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
									<?php 
									if($funcion_user == 'Administrador'){
										echo '
											<option value="1" selected>Administrador</option>
							 				<option value="2">Colaborador</option>
										';
									}
									if($funcion_user == 'Colaborador'){
										echo '
											<option value="1">Administrador</option>
							 				<option value="2" selected>Colaborador</option>
										';
									}
									?>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label">Contraseña</label>
                            <div class="col-lg-9">
                                <input class="form-control" name="Contrasena" type="password" >
                            </div>
                        </div>
						<?php
						  if($funcion_user == 'Administrador'){
							  echo '
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label form-control-label"></label>
                            <div class="col-lg-9">
                                <input type="reset" class="btn btn-secondary" value="Cancelar">
                                <input type="submit" name="actualizar_user" class="btn btn-primary" value="Guardar Cambios">
                            </div>
                        </div>
						';
						  }
						?>
                    </form>
					
                </div>
            </div>
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
           Desarrollado por Electronika © 2020 | Todos los derechos reservados.
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
