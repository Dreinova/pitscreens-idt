<?php
/**
 * logout.php — Módulo Administrador
 *
 * Cierra la sesión activa del usuario administrador.
 * Destruye todas las variables de sesión y redirige al login.
 */

// Iniciar sesión para poder destruirla
session_start();

// Eliminar todas las variables de sesión
session_unset();

// Destruir completamente la sesión en el servidor
session_destroy();

// Redirigir al formulario de inicio de sesión
header('Location: ../../index.php');