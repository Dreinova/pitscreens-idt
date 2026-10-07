<?php
/**
 * programacion.php — Módulo Administrador
 *
 * Listado de programaciones (tabla `lista-reproduccion`): define CUÁNDO
 * se activa un conjunto de contenido y si aplica a una pantalla específica
 * o a todas (general). La creación vive en nueva_programacion.php y la
 * edición en editar_programacion.php — esta página solo lista.
 *
 * El campo "Estado" lo gestiona automáticamente el sistema en app/index.php
 * y app/activacion.php; no se edita manualmente en condiciones normales.
 */

date_default_timezone_set('America/Bogota');
include 'assets/php/Conexion_DB.php';

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

/* --- Consultar todas las programaciones para mostrar en la tabla --- */
$consulta_programacion = mysqli_query($conexion, "SELECT * FROM `lista-reproduccion` ORDER BY `Fecha-Inicio` DESC");
$total_programaciones   = mysqli_num_rows($consulta_programacion);

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
  <title>Programación - IDT App</title>
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
			<h4 class="page-title">Programación</h4>
			<p class="text-muted mb-0">Define cuándo se activa un conjunto de contenido (fecha/hora) y si aplica a todas las pantallas o a una en particular. Para el contenido del día a día de una pantalla puntual, usa directamente <a href="modulos.php">Pantallas</a> → Editar.</p>
	   </div>
	   <div class="col-sm-3 text-right">
		   <a href="nueva_programacion.php" class="btn btn-primary"><i class="fa fa-plus"></i> Nueva programación</a>
	   </div>
     </div>
    <!-- Fin Migas de pan-->

	<div class="row">
		 <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <div class="table-responsive">
              <table class="table">
                  <thead>
                    <tr>
                      <th scope="col">Nombre de programación</th>
					  <th scope="col">Pantalla</th>
					  <th scope="col">Fecha de Programación</th>
                      <th scope="col">Estado</th>
					  <th scope="col">Fecha de Modificación</th>
					  <th scope="col">Usuario que modifico</th>
					  <th scope="col" class="text-center">Acciones</th>
                    </tr>
                  </thead>

				   <tbody>
				<?php if ($total_programaciones === 0): ?>
				  <tr>
					<td colspan="7" class="text-center text-muted">No hay programaciones registradas todavía.</td>
				  </tr>
				<?php else: foreach ($consulta_programacion as $row){
						$nombre_prog = htmlspecialchars($row['Nombre']);
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
						$user = htmlspecialchars($row['Usuario']);
						$modulo_prog = !empty($row['Modulo']) ? htmlspecialchars($row['Modulo']) : '<span class="badge badge-primary">General</span>';
						$id_fila = $row['ID'];
				?>
                  <tr>
					<td><?php echo $nombre_prog; ?></td>
					<td><?php echo $modulo_prog; ?></td>
					<td><?php echo $fecha_inicio; ?></td>
					<td><?php echo $estado; ?></td>
					<td><?php echo $ultima_mod2; ?></td>
					<td><?php echo $user; ?></td>
					<td class="text-center">
						<div class="btn-group btn-group-sm" role="group">
							<a href="editar_programacion.php?id=<?php echo $id_fila; ?>" class="btn btn-outline-secondary" title="Editar">
								<i class="fa fa-edit"></i>
							</a>
							<a href="assets/php/eliminar_programacion.php?id=<?php echo $id_fila; ?>"
							   class="btn btn-outline-danger js-confirm-delete" title="Eliminar"
							   data-title="¿Eliminar programación?"
							   data-body="Esta acción eliminará la programación &quot;<?php echo $nombre_prog; ?>&quot; y el contenido que tenga asignado. Esta acción no se puede deshacer.">
								<i class="fa fa-trash"></i>
							</a>
						</div>
					</td>
                  </tr>
                 <?php } endif; ?>

                </table>
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
          © Instituto Distrital de Turismo
        </div>
      </div>
    </footer>
	<!--End footer-->

  </div><!--End wrapper-->

  <?php include 'assets/php/confirm_delete_modal.php'; ?>

  <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>


  <!-- sidebar-menu js -->
  <script src="assets/js/sidebar-menu.js"></script>

  <!-- Custom scripts -->
  <script src="assets/js/app-script.js"></script>
  <!-- Confirmación de eliminación -->
  <script src="assets/js/confirm-delete.js"></script>

</body>
</html>
