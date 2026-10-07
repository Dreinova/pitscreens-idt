<?php
/**
 * modulos.php — Módulo Administrador
 *
 * Listado de Pantallas. La creación vive en nuevo_modulo.php y la edición
 * (datos básicos + contenido propio + protector propio) en editar_modulo.php
 * — esta página solo lista y ofrece las acciones por fila.
 */

date_default_timezone_set('America/Bogota');
include 'assets/php/Conexion_DB.php';

session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime' => 7200,
]);

/*  verificacion login  */
if(!isset($_SESSION['logeado'])){
	header('Location: index.php');
	exit();
}

/* datos de usuario */

$id = $_SESSION['id_Correo'];
$sql = "SELECT usuarios.ID, usuarios.Nombre, usuarios.Correo, usuarios.Foto_Usuario, funciones_usuario.Funcion FROM usuarios INNER JOIN funciones_usuario ON usuarios.Funcion = funciones_usuario.ID WHERE usuarios.ID = '$id' ";
$resultado = mysqli_query($conexion, $sql);
$datos = mysqli_fetch_array($resultado);

$name_user = $datos['Nombre'];
$mail_user = $datos['Correo'];
$foto_user = $datos['Foto_Usuario'];
$funcion_user = $datos['Funcion'];


/* datos tabla de módulos */

$sql_modulos = "SELECT * FROM modulos ORDER BY ID ASC";
$consulta_modulos = mysqli_query($conexion, $sql_modulos);
$total_modulos = mysqli_num_rows($consulta_modulos);

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
  <title>Pantallas - IDT App</title>
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
			<h4 class="page-title">Pantallas</h4>
			<p class="text-muted mb-0">Cada pantalla administra su propio contenido y protector desde "Editar". Usa <a href="contenido.php">Contenido</a> para el contenido general compartido entre pantallas.</p>
	   </div>
	   <div class="col-sm-3 text-right">
		   <a href="nuevo_modulo.php" class="btn btn-primary"><i class="fa fa-plus"></i> Nueva pantalla</a>
	   </div>
     </div>

      <div class="row">

        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
			  <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Ubicación</th>
                    <th scope="col">Fecha de Modificación</th>
					<th scope="col" class="text-center">Acciones</th>
                  </tr>
                </thead>

                <tbody>
				<?php if ($total_modulos === 0): ?>
				  <tr>
					<td colspan="5" class="text-center text-muted">No hay pantallas registradas todavía.</td>
				  </tr>
				<?php else: foreach ($consulta_modulos as $datos_modulos){
						$id_fila = $datos_modulos['ID'];
						$nombre_fila = htmlspecialchars($datos_modulos['nombre_modulo']);
				?>
                  <tr>
					<td><?php echo $id_fila; ?></td>
                    <td><?php echo $nombre_fila; ?></td>
                    <td><?php echo htmlspecialchars($datos_modulos['ubicacion']); ?></td>
					<td><?php echo $datos_modulos['fecha_modificacion']; ?></td>
					<td class="text-center">
						<div class="btn-group btn-group-sm" role="group">
							<a href="../app/index.php?pantalla=<?php echo $id_fila; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary" title="Ver pantalla" data-toggle="tooltip">
								<i class="fa fa-external-link"></i>
							</a>
							<a href="editar_modulo.php?id=<?php echo $id_fila; ?>" class="btn btn-outline-secondary" title="Editar">
								<i class="fa fa-edit"></i>
							</a>
							<a href="assets/php/eliminar_modulo.php?id=<?php echo $id_fila; ?>"
							   class="btn btn-outline-danger js-confirm-delete" title="Eliminar"
							   data-title="¿Eliminar pantalla?"
							   data-body="Esta acción eliminará la pantalla &quot;<?php echo $nombre_fila; ?>&quot; junto con su contenido y protector propios. Esta acción no se puede deshacer.">
								<i class="fa fa-trash"></i>
							</a>
						</div>
					</td>
                  </tr>
                 <?php } endif; ?>

                </tbody>
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

  <!-- simplebar js -->
  <script src="assets/plugins/simplebar/js/simplebar.js"></script>
  <!-- sidebar-menu js -->
  <script src="assets/js/sidebar-menu.js"></script>

  <!-- Custom scripts -->
  <script src="assets/js/app-script.js"></script>
  <!-- Confirmación de eliminación -->
  <script src="assets/js/confirm-delete.js"></script>

</body>
</html>
