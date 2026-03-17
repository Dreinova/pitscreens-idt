<?php
/**
 * programacion.php — Módulo Administrador
 *
 * Gestión de listas de reproducción / programaciones del sistema.
 * Permite crear programaciones con nombre y fecha/hora de inicio.
 * El sistema activa automáticamente la lista cuya Fecha-Inicio sea la
 * más reciente menor o igual a la hora actual (lógica en index.php y activacion.php).
 *
 * Funcionalidades:
 *   - Crear nueva programación con nombre y fecha/hora de activación.
 *   - Listar todas las programaciones con su estado actual.
 *   - Editar programaciones existentes (editar_programacion.php).
 *   - Eliminar programaciones (eliminar_programacion.php).
 *
 * Tabla `lista-reproduccion`:
 *   Nombre            — Nombre descriptivo de la programación
 *   Fecha-Inicio      — Fecha y hora en que debe activarse automáticamente
 *   Estado            — 1 (Activo) / 0 (Inactivo) — gestionado por el sistema
 *   Fecha-Modificacion — Fecha de la última modificación manual
 *   Usuario           — Administrador que creó o editó la programación
 *
 * Nota: El campo "Estado" lo gestiona automáticamente el sistema en index.php
 * y activacion.php; no debe editarse manualmente en condiciones normales.
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

/* --- Consultar todas las programaciones para mostrar en la tabla --- */
$consulta_programacion = mysqli_query($conexion, "SELECT * FROM `lista-reproduccion`");

/* --- Procesamiento del formulario para crear nueva programación --- */
if(isset($_POST['enviar'])){

	$Nombre = $_REQUEST["Nombre"];

	// Combinar la fecha y hora del campo datetime-local del formulario
	$fecha_inicio = $_REQUEST["Fecha"];
	$hora_inicio  = $_REQUEST["Hora"];
	$fecha_inicio = $fecha_inicio . ' ' . $hora_inicio;

	$Estado = $_REQUEST["Estado"];
	$hoy    = date("Y-m-d H:i:s"); // Fecha actual como marca de modificación

	// Insertar la nueva programación en la base de datos
	$sql_programacion = "INSERT INTO `lista-reproduccion`
                         (Nombre, `Fecha-Inicio`, Estado, `Fecha-Modificacion`, Usuario)
                         VALUES ('$Nombre', '$fecha_inicio', '$Estado', '$hoy', '$name_user')";

	$nueva_programacion = mysqli_query($conexion, $sql_programacion);

	if ($nueva_programacion){
		header("Location: programacion.php");
	}
	else{
		$errores = "<p>No se ha guardado correctamente la programación.</p>";
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
  <title>Edición de Inicio - IDT App by Electronika</title>
  <!--favicon-->
  <link rel="icon" href="assets/images/Favicon.png" type="image/x-icon">
  <!-- jquery steps CSS-->
  <link rel="stylesheet" type="text/css" href="assets/plugins/jquery.steps/css/jquery.steps.css">
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
			<h4 class="page-title">Edición de Inicio de la App</h4>
	   </div>
     </div>
    <!-- Fin Migas de pan-->
		
	<!-- Inicio Secciones -->
		
	<div class="row">
		<div class="col-lg-12">
        	<div class="card">
			<div class="card-header"><i class="fa fa-edit"></i>Nueva Programación</div>
            	<div class="card-body">
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
						
						<div class="row">
							
							<div class="col-12 col-lg-6 col-xl-6">
								<div class="form-group row">
									<label class="col-lg-12 col-form-label form-control-label">Nombre del la programación</label>
									<div class="col-lg-12">
										<input class="form-control" type="text" maxlength="50" name="Nombre" value="" placeholder="Ingrese el nombre de la programación" required>
									</div>
								</div>
							</div>
							
							<div class="col-12 col-lg-6 col-xl-6">
								<div class="form-group row">
									<label class="col-lg-12 col-form-label form-control-label">Fecha de programación</label>
									<div class="col-lg-10">
										<input type="datetime-local" name="Fecha" class="form-control" value="2020-06-01T12:00">
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
							<input type="submit" class="btn btn-success" name="enviar" value="Guardar">
						</div>
						
                    </form>
			  	</div>
			</div>
		</div>
	</div>
		
	<div class="row">
		 <div class="col-lg-12">
          <div class="card">
            <div class="card-header"><i class="fa fa-edit"></i> Configuración Actual</div>
            <div class="card-body">
              <div class="table-responsive">
              <table class="table">
                  <thead>
                    <tr>
                      <th scope="col">Nombre de programación</th>
					  <th scope="col">Fecha de Programación</th>
                      <th scope="col">Estado</th>
					  <th scope="col">Fecha de Modificación</th>
					  <th scope="col">Usuario que modifico</th>
					  <th scope="col">Edición</th>
                    </tr>
                  </thead>
				  
				   <tbody>
				<?php
					foreach ($consulta_programacion as $row){
						$nombre_prog = $row['Nombre'];
						$fecha_inicio = $row['Fecha-Inicio'];
						$fecha_inicio = date_format (new DateTime($fecha_inicio), 'd-m-Y h:i A');
						$estado = $row['Estado'];
							if($estado == '1'){
								$estado = 'Activo';
							}
							if($estado == '0'){
								$estado = 'Inactivo';
							}
						$ultima_mod = $row['Fecha-Modificacion'];
						$ultima_mod2 = date_format (new DateTime($ultima_mod), 'd-m-Y h:i A');
						$user = $row['Usuario'];
				?>
                  <tr>
					<td><?php echo $nombre_prog; ?></td>
					<td><?php echo $fecha_inicio; ?></td>
					<td><?php echo $estado; ?></td>
					<td><?php echo $ultima_mod2; ?></td>
					<td><?php echo $user; ?></td>

                    <?php
						$id = $row['ID'];
						echo "<td><a href='editar_programacion.php?id=$id'><i class='fa fa-edit'></i></a>
						<a href='assets/php/eliminar_programacion.php?id=$id' onclick='javascript:return asegurar();''><i class='fa fa-trash'></i></a>";
						echo "</tr>"; 
					?>
                  </tr>
                 <?php }?>
					
               
                </table>
            </div>
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
          Desarrollado por Electronika © 2020 | Todos los derechos reservados.
        </div>
      </div>
    </footer>
	<!--End footer-->
	
  </div><!--End wrapper-->
  
  <script>
  	function asegurar (){
		  rc = confirm("¿Está seguro de eliminar la programación?");
		  return rc;
	  }
  </script>
  <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
	

  <!-- sidebar-menu js -->
  <script src="assets/js/sidebar-menu.js"></script>
  
  <!-- Custom scripts -->
  <script src="assets/js/app-script.js"></script>
	
	  <!--Data Tables js-->
  <script src="assets/plugins/bootstrap-datatable/js/jquery.dataTables.min.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/dataTables.bootstrap4.min.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/dataTables.buttons.min.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/buttons.bootstrap4.min.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/jszip.min.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/pdfmake.min.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/vfs_fonts.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/buttons.html5.min.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/buttons.print.min.js"></script>
  <script src="assets/plugins/bootstrap-datatable/js/buttons.colVis.min.js"></script>

</body>
</html>