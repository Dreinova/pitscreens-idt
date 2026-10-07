<?php
/**
 * logout.php — Módulo App (Pantalla)
 *
 * Limpia la sesión del módulo seleccionado ($_SESSION['log-modulo'] /
 * $_SESSION['modulo']) y devuelve al operador a la pantalla de selección
 * de módulo (log_index.php) para poder reconfigurar el kiosco.
 */
session_start();
session_unset();
session_destroy();
header('Location: ../../log_index.php');
exit();