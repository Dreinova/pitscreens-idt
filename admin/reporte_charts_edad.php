<?php
date_default_timezone_set('America/Bogota');
include 'assets/php/Conexion_DB.php';

session_start([
    'cookie_lifetime' => 7200,
    'gc_maxlifetime' => 7200,
]);

setlocale(LC_TIME, 'es_co');

/*  verificacion login  */
if(!isset($_SESSION['logeado'])):
	header('Location: index.php');
	exit();
endif;

/* datos de usuario Logeado */

$id = $_SESSION['id_Correo'];
$sql = "SELECT usuarios.ID, usuarios.Nombre, usuarios.Correo, usuarios.Foto_Usuario, funciones_usuario.Funcion FROM usuarios INNER JOIN funciones_usuario ON usuarios.Funcion = funciones_usuario.ID WHERE usuarios.ID = '$id' ";
$resultado = mysqli_query($conexion, $sql);
$datos = mysqli_fetch_array($resultado);

$name_user = $datos['Nombre'];
$mail_user = $datos['Correo'];
$foto_user = $datos['Foto_Usuario'];
$funcion_user = $datos['Funcion'];

$ahora = date('d-m-Y');

?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <title>Reportes Graficos - IDT App</title>
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
  <!-- PDF export -->
  <script src="assets/js/jspdf.min.js"></script>
	<!-- Chart JS -->
  <script src="assets/plugins/Chart.js/Chart.min.js"></script>
  <script src="assets/js/chartjs-plugin-labels.js"></script>
	
  
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
			<h4 class="page-title">Estadisticas de uso por edad</h4>
	   </div>
     </div>
    <!-- Fin Migas de pan-->
		
	<!-- Inicio Secciones -->

    
     <div class="row">
		 
        <div class="col-lg-6 col-xl-6">
          <div class="card">
            <div class="card-header text-uppercase">Uso de la app por genero</div>
            <div class="card-body">
				
			<form name="frmSearch" method="post" action="">  
				<div class="form-group row">
					<label class="col-lg-2 col-form-label form-control-label">Desde</label>
					<div class="col-lg-3">
						<input type="date" name="desde" class="form-control" value="">
					</div>

					<label class="col-lg-2 col-form-label form-control-label">Hasta</label>
					<div class="col-lg-3">
						<input type="date" name="hasta" class="form-control" value="">
					</div>

					 <div class="col-lg-2">
						<input class="btn btn-primary" type="submit" value="Buscar" >
					</div>    
				</div>
			</form>
				
<?php
	$edad1 = '(0-2)';
	$edad2 = '(4-6)';
	$edad3 = '(15-20)';
	$edad4 = '(25-32)';
	$edad5 = '(38-43)';
	$edad6 = '(48-53)';
	$edad7 = '(60-100)';

	$queryCondition1 = "";
	$queryCondition2 = "";
	 
	$post_at = date('Y/m/d');
	 $post_at_to_date = strtotime ( '-1 year' , strtotime ( $post_at ) ) ;
	 $post_at_to_date = date ( 'Y/m/d' , $post_at_to_date );

	if(!empty($_POST["desde"])){
		$post_at = date('Y/m/d', strtotime($_POST["desde"]));	
		
		if(!empty($_POST["hasta"])){
			$post_at_to_date = date('Y/m/d', strtotime($_POST["hasta"]));
			
			$queryCondition1 = "Hora BETWEEN '".$post_at." 00:00:00' AND '".$post_at_to_date." 23:59:59' AND";
		}
		else{
			$post_at_to_date = $post_at;
			$queryCondition1 = "Hora BETWEEN '".$post_at." 00:00:00' AND '".$post_at_to_date." 23:59:59' AND";
		}			
	}

	$sql_recorrido1 = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition1 Edad = '$edad1' ";
	$consulta_mes_F1 = mysqli_query($conexion, $sql_recorrido1);
	$conteoF1 = mysqli_fetch_array($consulta_mes_F1);
	$conteoF1 = $conteoF1['total'];
	 
	$sql_recorrido2 = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition1 Edad = '$edad2' ";
	$consulta_mes_F2 = mysqli_query($conexion, $sql_recorrido2);
	$conteoF2 = mysqli_fetch_array($consulta_mes_F2);
	$conteoF2 = $conteoF2['total'];
	 
	$sql_recorrido3 = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition1 Edad = '$edad3' ";
	$consulta_mes_F3 = mysqli_query($conexion, $sql_recorrido3);
	$conteoF3 = mysqli_fetch_array($consulta_mes_F3);
	$conteoF3 = $conteoF3['total'];
	 
	$sql_recorrido4 = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition1 Edad = '$edad4' ";
	$consulta_mes_F4 = mysqli_query($conexion, $sql_recorrido4);
	$conteoF4 = mysqli_fetch_array($consulta_mes_F4);
	$conteoF4 = $conteoF4['total'];
	 
	$sql_recorrido5 = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition1 Edad = '$edad5' ";
	$consulta_mes_F5 = mysqli_query($conexion, $sql_recorrido5);
	$conteoF5 = mysqli_fetch_array($consulta_mes_F5);
	$conteoF5 = $conteoF5['total'];
	 
	$sql_recorrido6 = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition1 Edad = '$edad6' ";
	$consulta_mes_F6 = mysqli_query($conexion, $sql_recorrido6);
	$conteoF6 = mysqli_fetch_array($consulta_mes_F6);
	$conteoF6 = $conteoF6['total'];
	 
	$sql_recorrido7 = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition1 Edad = '$edad7' ";
	$consulta_mes_F7 = mysqli_query($conexion, $sql_recorrido7);
	$conteoF7 = mysqli_fetch_array($consulta_mes_F7);
	$conteoF7 = $conteoF7['total'];
	

?>
			
<div class="form-group row">
		<div class="col-lg-12">
			<canvas id="chart1" ></canvas>
			<button class="btn btn-primary" id="download2">Guardar gráfico</button>
		</div>  
	</div>
	
<script>
var ctx = document.getElementById("chart1");
var data = {
        labels: ['<?php echo 'Desde:'.$post_at.' a '.$post_at_to_date; ?>'],
        datasets: [
						{
						label: '0 a 2',
						data: ['<?php echo $conteoF1; ?>'],
						backgroundColor: "rgb(0, 198, 160, 1)"
						}, 
						{
						label: '4 a 6',
						data: ['<?php echo $conteoF2; ?>'],
						backgroundColor: "rgba(0, 175, 207, 1)"
						},
						{
						label: '15 a 20',
						data: ['<?php echo $conteoF3; ?>'],
						backgroundColor: "rgba(0, 155, 204, 1)"
						},
						{
						label: '25 a 32',
						data: ['<?php echo $conteoF4; ?>'],
						backgroundColor: "rgba(0, 135, 195, 1)"
						},
						{
						label: '38 a 43',
						data: ['<?php echo $conteoF5; ?>'],
						backgroundColor: "rgba(0, 116, 191, 1)"
						},
						{
						label: '48 a 53',
						data: ['<?php echo $conteoF6; ?>'],
						backgroundColor: "rgba(0, 64, 186, 1)"
						},
						{
						label: '60 a 100',
						data: ['<?php echo $conteoF7; ?>'],
						backgroundColor: "rgba(77, 34, 179, 1)"
						}
					]
    };
var options = {
        	legend: {
				  display: true,
				  labels: {
					fontColor: '#1B4F8C', 
					boxWidth:50
				  },
			},
		
			plugins: {
				  labels: {
					render: 'value',
					fontColor: '#1B4F8C' 
				  }
			},

			tooltips: {
				  enabled:true,
				},
	
			scales: {
				  xAxes: [{
					  barPercentage: .6,
					ticks: {
						beginAtZero:true,
						fontColor: '#1B4F8C'
					},
					gridLines: {
					  display: true ,
					  color: "#B1B1B1"
					},
				  }],
				
				  yAxes: [{
					ticks: {
						beginAtZero:false,
						fontColor: '#1B4F8C'
					},
					gridLines: {
					  display: false ,
					  color: "#B1B1B1"
					},
				  }]
				  
			}
	
    };
var chart1 = new Chart(ctx, {
    type: 'bar',
    data: data,
    options: options
});

var fecha = new Date();
	
download2.addEventListener("click", function () {
        var imgData = document.getElementById('chart1').toDataURL("image/png", 1.0);
        var pdf = new jsPDF();

        pdf.addImage(imgData, 'png', 0, 20);
        pdf.save("Reporte de Uso de la app por genero - <?php echo $ahora; ?>");
    }, false);

</script>

             </div>
          </div>
        </div>
		 
		 <div class="col-lg-6 col-xl-6">
          <div class="card">
            <div class="card-header text-uppercase">Porcentaje por genero</div>
            <div class="card-body">
<?php

	$queryCondition_pie_1 = "";
	$queryCondition_pie_2 = "";
				
	$desde = date('Y/m/d');
	$hasta = strtotime ( '-1 year' , strtotime ( $desde ) ) ;
	$hasta = date ( 'Y/m/d' , $hasta );

	if(!empty($_POST["desde_pie"])){
		$desde = date('Y/m/d', strtotime($_POST["desde_pie"]));	
		
		if(!empty($_POST["hasta_pie"])){
			$hasta = date('Y/m/d', strtotime($_POST["hasta_pie"]));
			
			$queryCondition_pie_1 .= "Hora BETWEEN '".$desde." 00:00:00' AND '".$hasta." 23:59:59' AND";
		}
		else{
			$hasta = $desde;
			
			$queryCondition_pie_1 .= "Hora BETWEEN '".$desde." 00:00:00' AND '".$hasta." 23:59:59' AND";
		}			
	}

	$UsoXSem_F1_chart = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition_pie_1 Edad = '$edad1' ";
	$result1 = mysqli_query($conexion, $UsoXSem_F1_chart);
	$numero1 = mysqli_fetch_array($result1);
	$numero1 =  $numero1['total'];
				
	$UsoXSem_F2_chart = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition_pie_1 Edad = '$edad2' ";
	$result2 = mysqli_query($conexion, $UsoXSem_F2_chart);
	$numero2 = mysqli_fetch_array($result2);
	$numero2 =  $numero2['total'];
				
	$UsoXSem_F3_chart = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition_pie_1 Edad = '$edad3' ";
	$result3 = mysqli_query($conexion, $UsoXSem_F3_chart);
	$numero3 = mysqli_fetch_array($result3);
	$numero3 =  $numero3['total'];
				
	$UsoXSem_F4_chart = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition_pie_1 Edad = '$edad4' ";
	$result4 = mysqli_query($conexion, $UsoXSem_F4_chart);
	$numero4 = mysqli_fetch_array($result4);
	$numero4 =  $numero4['total'];
				
	$UsoXSem_F5_chart = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition_pie_1 Edad = '$edad5' ";
	$result5 = mysqli_query($conexion, $UsoXSem_F5_chart);
	$numero5 = mysqli_fetch_array($result5);
	$numero5 =  $numero5['total'];
				
	$UsoXSem_F6_chart = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition_pie_1 Edad = '$edad6' ";
	$result6 = mysqli_query($conexion, $UsoXSem_F6_chart);
	$numero6 = mysqli_fetch_array($result6);
	$numero6 =  $numero6['total'];
	
	$UsoXSem_F7_chart = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition_pie_1 Edad = '$edad7' ";
	$result7 = mysqli_query($conexion, $UsoXSem_F7_chart);
	$numero7 = mysqli_fetch_array($result7);
	$numero7 =  $numero7['total'];

?>
			
			<form name="frmSearch" method="post" action="">  
				<div class="form-group row">
					<label class="col-lg-2 col-form-label form-control-label">Desde</label>
					<div class="col-lg-3">
						<input type="date" name="desde_pie" class="form-control" value="">
					</div>

					<label class="col-lg-2 col-form-label form-control-label">Hasta</label>
					<div class="col-lg-3">
						<input type="date" name="hasta_pie" class="form-control" value="">
					</div>

					 <div class="col-lg-2">
						<input class="btn btn-primary" type="submit" value="Buscar" >
					</div>    
				</div>
			</form>
				
			<div class="form-group row">
				<div class="col-lg-12">
					<canvas id="chart2" ></canvas>
					<button class="btn btn-primary" id="download1">Guardar gráfico</button>
				</div>
			</div>

<script>
var ctx2 = document.getElementById("chart2");
var data2 = {
        labels: ["0 a 2 años", "4 a 6 años", "15 a 20 años", "25 a 32 años", "38 a 43 años","48 a 53 años","60 a 100 años", "<?php echo 'Desde:'.$desde.' a '.$hasta; ?>" ],
        datasets: [{
						backgroundColor: [
							"rgb(0, 198, 160, 1)",
							"rgba(0, 175, 207, 1)",
							"rgb(27, 79, 140, 1)",
							"rgba(0, 135, 195, 1)",
							"rgb(0, 116, 191, 1)",
							"rgba(0, 64, 186, 1)",
							"rgba(77, 34, 179, 1)",
							"rgba(250, 250, 250, 0)"
						],
						data: [<?php echo $numero1; ?>, <?php echo $numero2; ?>, <?php echo $numero3; ?>, <?php echo $numero4; ?>, <?php echo $numero5; ?>, <?php echo $numero6; ?>, <?php echo $numero7; ?>, 0],
						borderWidth: [0, 0, 0, 0, 0, 0]
					}]
    };
var options2 = {
        	legend: {
				 position :"right",	
				 display: true,
			     fontSize: 14,
				 fontStyle: 'bold',
				 fontColor: '#1B4F8C',
				 boxWidth:50,
				 textShadow: true
				},
			plugins: {
				  labels: {
					render: 'percentage',
					fontColor: '#fff' 
				  }
			},

    };
var chart2 = new Chart(ctx2, {
    type: 'pie',
    data: data2,
    options: options2
});

var dataURL = ctx2.toDataURL('image/png');

	download1.addEventListener("click", function () {
        var imgData = document.getElementById('chart2').toDataURL("image/png", 1.0);
        var pdf = new jsPDF();

        pdf.addImage(imgData, 'pgn', 0, 20);
        pdf.save("Reporte porcentaje de Uso de la app por genero - <?php echo $ahora; ?>.pdf");
    }, false);
	
</script>

				
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

</body>
</html>