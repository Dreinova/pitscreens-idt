<?php
/**
 * Devuelve el <img> de la foto de perfil si existe, o si no, una insignia
 * circular con la inicial del correo sobre fondo azul institucional —
 * evita el ícono de imagen rota cuando el usuario no ha subido foto.
 */
function idt_avatar($foto, $correo, $class = '', $variant = 'circle') {
	$foto = trim((string) $foto);
	if ($foto !== '' && file_exists(__DIR__ . '/../../' . $foto)) {
		return '<img class="' . htmlspecialchars($class) . '" src="' . htmlspecialchars($foto) . '" alt="Foto de perfil">';
	}
	$inicial = strtoupper(substr(trim((string) $correo), 0, 1));
	if ($inicial === '') { $inicial = '?'; }
	$variantClass = $variant === 'block' ? 'avatar-initial-block' : 'avatar-initial';
	return '<span class="' . $variantClass . ' ' . htmlspecialchars($class) . '">' . htmlspecialchars($inicial) . '</span>';
}
?>
<!--inicio menú lateral-->
<div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
	
	<div class="brand-logo">
      <a href="index.php" class="brand-logo-link">
       <span class="brand-plate">
         <img src="assets/images/logo-bogota.svg" class="brand-logo-svg" alt="Bogotá">
       </span>
     </a>
   </div>
	   
   <div class="user-details">
	  <div class="media align-items-center user-pointer collapsed" data-toggle="collapse" data-target="#user-dropdown">
	    <div class="avatar">
			<?php echo idt_avatar($foto_user, $mail_user, 'mr-3 side-user-img'); ?>
		  </div>
	     <div class="media-body">
	     <h6 class="side-user-name"><?php echo $name_user; ?></h6>
	    </div>
       </div>
	   <div id="user-dropdown" class="collapse">
		  <ul class="user-setting-menu">
            <li><a href="perfil_usuario.php"><i class="icon-user"></i>  Mi Cuenta</a></li>
			<?php
			  if($funcion_user == 'Administrador'){
				  echo '<li><a href="usuarios.php"><i class="icon-people icons"></i>Gestión de Usuarios</a></li>';
			  }
			?>
			<li><a href="assets/php/logout.php"><i class="fa fa-sign-out"></i> Cerrar Sesión</a></li>
		  </ul>
	   </div>
     </div>
	
	
      <ul class="sidebar-menu do-nicescrol">
      <li class="sidebar-header">MENÚ PRINCIPAL</li>
	   
      <li>
        <a href="inicio.php" class="waves-effect">
          <i class="zmdi zmdi-view-dashboard"></i><span>Inicio</span>
        </a>
      </li>
		  
	  <li>
        <a href="modulos.php" class="waves-effect">
          <i class="fa fa-tv"></i><span>Pantallas</span>
        </a>
      </li>
		  
	  <li>
        <a href="programacion.php" class="waves-effect">
		  <i class="fa fa-pencil-square-o "></i><span>Programación</span>
        </a>
      </li>
		  
	  <li>
        <a href="contenido.php" class="waves-effect">
          <i class="fa fa-picture-o"></i><span>Contenido</span>
        </a>
      </li>
		  
	  <li>
        <a href="frame.php" class="waves-effect">
          <i class="fa fa-crop"></i><span>Frame <span class="badge badge-warning" title="No tiene ningún punto de entrada desde el kiosco actualmente">No conectado</span></span>
        </a>
      </li>

	  <li>
        <a href="configuracion.php" class="waves-effect">
          <i class="fa fa-clock-o"></i><span>Configuración</span>
        </a>
      </li>
	<?php
		if($funcion_user == 'Administrador'){
			echo '
	<li>
        <a href="javaScript:void();" class="waves-effect">
          <i class="zmdi zmdi-chart"></i> <span>Reportes</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="sidebar-submenu">
          <li>
			  <a href="reporte_tabla.php"><i class="zmdi zmdi-long-arrow-right"></i> Reporte General</a>
		  </li>
		  <li>
			  <a href="reporte_visitantes.php"><i class="zmdi zmdi-long-arrow-right"></i> Reporte de Visitantes</a>
		  </li>
		  <li>
			  <a href="reporte_pantallas.php"><i class="zmdi zmdi-long-arrow-right"></i> Reporte de Pantallas</a>
		  </li>
          <li>
            <a href="javaScript:void();"><i class="zmdi zmdi-long-arrow-right"></i> Estadisticas de Uso <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="sidebar-submenu">
			  <li><a href="reporte_charts_genero.php"><i class="zmdi zmdi-long-arrow-right"></i> Por Genero</a></li>
			  <li><a href="reporte_charts_tiempo.php"><i class="zmdi zmdi-long-arrow-right"></i> Por Tiempo</a></li>
			  <li><a href="reporte_charts_edad.php"><i class="zmdi zmdi-long-arrow-right"></i> Por Edad</a></li>
            </ul>
          </li>
        </ul>
      </li>
			';
		}
	?>
	   

      <li class="sidebar-header">ETIQUETAS</li>
      <li>
		  <a href="assets/php/logout.php" class="waves-effect"><i class="fa fa-sign-out"></i> 
		  <span>Cerrar Sesión</span></a>
	   </li>

    </ul>
   
   </div>
   <!--Fin menú lateral-->
