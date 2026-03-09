<!--Inicio Menú Superior-->
<header class="topbar-nav">
 <nav class="navbar navbar-expand fixed-top">
  <ul class="navbar-nav mr-auto align-items-center">
    <li class="nav-item">
      <a class="nav-link toggle-menu" href="javascript:void();">
       <i class="icon-menu menu-icon"></i>
     </a>
    </li>

  </ul>
     
  <ul class="navbar-nav align-items-center right-nav-link">

    <li class="nav-item">
      <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown" href="#">
        <span class="user-profile"><img src="<?php echo $foto_user; ?>" class="img-circle" alt="user avatar"></span>
      </a>
      <ul class="dropdown-menu dropdown-menu-right">
       <li class="dropdown-item user-details">
        <a href="javaScript:void();">
           <div class="media">
             <div class="avatar"><img class="align-self-start mr-3" src="<?php echo $foto_user; ?>" alt="user avatar"></div>
            <div class="media-body">
            <h6 class="mt-2 user-title"><?php echo $name_user; ?></h6>
            <p class="user-subtitle"><?php echo $mail_user; ?></p>
            </div>
           </div>
          </a>
        </li>
        <li class="dropdown-divider"></li>
        <li class="dropdown-divider"></li>
        <li class="dropdown-divider"></li>
		<li class="dropdown-item"><a href="perfil_usuario.php"><i class="icon-user icons"></i>  Mi Cuenta</a></li>
		<?php
			  if($funcion_user == 'Administrador'){
				  echo '
        <li class="dropdown-item"><a href="usuarios.php"><i class="icon-people icons"></i> Gestión de Usuarios</a></li>
		';
			  }
			?>
        <li class="dropdown-divider"></li>
        <li class="dropdown-item"><a href="assets/php/logout.php"><i class="fa fa-sign-out"></i> Cerrar Sesión</a></li>
      </ul>
    </li>
  </ul>
</nav>
</header>
<!--Fin Menú Superior-->