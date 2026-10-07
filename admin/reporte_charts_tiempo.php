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
  <title>Reportes Graficos - IDT App by Electronika</title>
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
			<h4 class="page-title">Estadisticas de uso por tiempo de actividad</h4>
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
	
	$tiempo1 = "'1' AND '2' AND '3' AND '4' AND '5' AND '6' AND '7' AND '8' AND '9' AND '10'";
	$tiempo2 = "'11' AND '12' AND '13' AND '14' AND '15' AND '16' AND '17' AND '18' AND '19' AND '20'";
	$tiempo3 = "'21' AND '22' AND '23' AND '24' AND '25' AND '26' AND '27' AND '28' AND '29' AND '30'";
	$tiempo4 = "'31' AND '32' AND '33' AND '34' AND '35' AND '36' AND '37' AND '38' AND '39' AND '40'";
	$tiempo5 = "'41' AND '42' AND '43' AND '44' AND '45' AND '46' AND '47' AND '48' AND '49' AND '50'";
	$tiempo6 = "'51' AND '52' AND '53' AND '54' AND '55' AND '56' AND '57' AND '58' AND '59' AND '60'";
	 
	$queryCondition1 = "";
	 
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

	$sql_recorrido0 = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition1 Tiempo = '0' ";
	$consulta_mes_F0 = mysqli_query($conexion, $sql_recorrido0);
	$conteoF0 = mysqli_fetch_array($consulta_mes_F0);
	$conteoF0 = $conteoF0['total'];
	 
	$sql_recorrido1 = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition1 Tiempo = $tiempo1 ";
	$consulta_mes_F1 = mysqli_query($conexion, $sql_recorrido1);
	$conteoF1 = mysqli_fetch_array($consulta_mes_F1);
	$conteoF1 = $conteoF1['total'];
	 
	$sql_recorrido2 = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition1 Tiempo = $tiempo2 ";
	$consulta_mes_F2 = mysqli_query($conexion, $sql_recorrido2);
	$conteoF2 = mysqli_fetch_array($consulta_mes_F2);
	$conteoF2 = $conteoF2['total'];
	 
	$sql_recorrido3 = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition1 Tiempo = $tiempo3 ";
	$consulta_mes_F3 = mysqli_query($conexion, $sql_recorrido3);
	$conteoF3 = mysqli_fetch_array($consulta_mes_F3);
	$conteoF3 = $conteoF3['total'];
	 
	$sql_recorrido4 = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition1 Tiempo = $tiempo4 ";
	$consulta_mes_F4 = mysqli_query($conexion, $sql_recorrido4);
	$conteoF4 = mysqli_fetch_array($consulta_mes_F4);
	$conteoF4 = $conteoF4['total'];
	 
	$sql_recorrido5 = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition1 Tiempo = $tiempo5 ";
	$consulta_mes_F5 = mysqli_query($conexion, $sql_recorrido5);
	$conteoF5 = mysqli_fetch_array($consulta_mes_F5);
	$conteoF5 = $conteoF5['total'];
	 
	$sql_recorrido6 = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition1 Tiempo = $tiempo6 ";
	$consulta_mes_F6 = mysqli_query($conexion, $sql_recorrido6);
	$conteoF6 = mysqli_fetch_array($consulta_mes_F6);
	$conteoF6 = $conteoF6['total'];

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
						label: '0 minutos',
						data: ['<?php echo $conteoF0; ?>'],
						backgroundColor: "rgb(27, 79, 140, 1)"
						}, 
						{
						label: 'de 1 a 10 minutos',
						data: ['<?php echo $conteoF1; ?>'],
						backgroundColor: "rgba(0, 198, 160, 1)"
						},
						{
						label: 'de 11 a 20 minutos',
						data: ['<?php echo $conteoF2; ?>'],
						backgroundColor: "rgb(59, 190, 0, 1)"
						}, 
						{
						label: 'de 21 a 30 minutos',
						data: ['<?php echo $conteoF3; ?>'],
						backgroundColor: "rgba(186, 223, 0, 1)"
						},
						{
						label: 'de 31 a 40 minutos',
						data: ['<?php echo $conteoF4; ?>'],
						backgroundColor: "rgb(255, 237, 0, 1)"
						}, 
						{
						label: 'de 41 a 50 minutos',
						data: ['<?php echo $conteoF5; ?>'],
						backgroundColor: "rgba(255, 147, 0, 1)"
						},
						{
						label: 'de 51 a 60 minutos',
						data: ['<?php echo $conteoF6; ?>'],
						backgroundColor: "rgba(255, 38, 0, 1)"
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

	$UsoXSem_F0_chart = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition_pie_1 Tiempo = '0' ";
	$result0 = mysqli_query($conexion, $UsoXSem_F0_chart);
	$numero0 = mysqli_fetch_array($result0);
	$numero0 =  $numero0['total'];
				
	$UsoXSem_F1_chart = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition_pie_1 Tiempo = $tiempo1 ";
	$result1 = mysqli_query($conexion, $UsoXSem_F1_chart);
	$numero1 = mysqli_fetch_array($result1);
	$numero1 =  $numero1['total'];
				
	$UsoXSem_F2_chart = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition_pie_1 Tiempo = $tiempo2 ";
	$result2 = mysqli_query($conexion, $UsoXSem_F2_chart);
	$numero2 = mysqli_fetch_array($result2);
	$numero2 = $numero2['total'];
	
	$UsoXSem_F3_chart = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition_pie_1 Tiempo = $tiempo3 ";
	$result3 = mysqli_query($conexion, $UsoXSem_F3_chart);
	$numero3 = mysqli_fetch_array($result3);
	$numero3 =  $numero3['total'];
				
	$UsoXSem_F4_chart = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition_pie_1 Tiempo = $tiempo4 ";
	$result4 = mysqli_query($conexion, $UsoXSem_F4_chart);
	$numero4 = mysqli_fetch_array($result4);
	$numero4 = $numero4['total'];
				
	$UsoXSem_F5_chart = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition_pie_1 Tiempo = $tiempo5 ";
	$result5 = mysqli_query($conexion, $UsoXSem_F5_chart);
	$numero5 = mysqli_fetch_array($result5);
	$numero5 =  $numero5['total'];
				
	$UsoXSem_F6_chart = "SELECT COUNT(ID) AS total FROM reco WHERE $queryCondition_pie_1 Tiempo = $tiempo6 ";
	$result6 = mysqli_query($conexion, $UsoXSem_F6_chart);
	$numero6 = mysqli_fetch_array($result6);
	$numero6 = $numero6['total'];

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
        labels: ["0 minutos", "de 1 a 10 minutos", "de 11 a 20 minutos", "de 21 a 30 minutos", "de 31 a 40 minutos", "de 41 a 50 minutos", "de 51 a 60 minutos", "<?php echo 'Desde:'.$desde.' a '.$hasta; ?>" ],
        datasets: [{
						backgroundColor: [
							"rgb(27, 79, 140, 1)",
							"rgb(0, 198, 160, 1)",
							"rgb(59, 190, 0, 1)",
							"rgb(186, 223, 0, 1)",
							"rgb(255, 237, 0, 1)",
							"rgb(255, 147, 0, 1)",
							"rgb(255, 38, 0, 1)",
							
							"rgb(250, 250, 250, 0)"
						],
						data: [<?php echo $numero0; ?>,<?php echo $numero1; ?>, <?php echo $numero2; ?>,<?php echo $numero2; ?>,<?php echo $numero2; ?>,<?php echo $numero2; ?>,<?php echo $numero2; ?>, 0, 0],
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
          Desarrollado por Electronika © 2020 | Todos los derechos reservados.
        </div>
      </div>
    </footer>
	<!--End footer-->
	
  </div><!--End wrapper-->

</body>
</html>