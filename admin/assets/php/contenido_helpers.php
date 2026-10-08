<?php
/**
 * contenido_helpers.php — Módulo Administrador
 *
 * Funciones compartidas por la biblioteca (contenido.php), la edición de
 * pantallas (editar_modulo.php) y los endpoints de publicar/ordenar/
 * eliminar contenido. Requiere $conexion ya abierta (Conexion_DB.php).
 *
 * Modelo:
 *   - `biblioteca`  — un registro por archivo en assets/galeria/.
 *   - `contenido`   — una "publicación": ese archivo dentro de una
 *                     `lista-reproduccion`, con Orden, Estado y una ventana
 *                     opcional Fecha_Inicio/Fecha_Fin (NULL = sin límite).
 *   - Cada pantalla tiene (o no) una lista propia (`Modulo` = su nombre);
 *     sin lista propia muestra la lista General (`Modulo` NULL).
 */

/** Tamaño máximo de subida, en MB (mismo límite que valida el cliente). */
const CONTENIDO_MAX_MB = 80;

/**
 * Lista de reproducción vigente de una pantalla — misma resolución que usa
 * app/index.php. Con $usuario la crea si no existe; sin él solo la busca.
 * Devuelve el ID o null.
 */
function lista_de_pantalla($conexion, $nombre_modulo, $usuario = null) {
	$nombre_esc = mysqli_real_escape_string($conexion, $nombre_modulo);
	$sql = mysqli_query($conexion,
	    "SELECT `ID` FROM `lista-reproduccion`
	     WHERE `Modulo` = '$nombre_esc' AND `Fecha-Inicio` <= NOW()
	     ORDER BY `Fecha-Inicio` DESC LIMIT 1");
	$row = $sql ? mysqli_fetch_array($sql) : null;
	if ($row) {
		return (int) $row['ID'];
	}
	if ($usuario === null) {
		return null;
	}
	$ahora       = date("Y-m-d H:i:s");
	$nombre_lista = mysqli_real_escape_string($conexion, "Pantalla: " . $nombre_modulo);
	$usuario_esc = mysqli_real_escape_string($conexion, $usuario);
	mysqli_query($conexion,
	    "INSERT INTO `lista-reproduccion` (`Nombre`, `Modulo`, `Fecha-Inicio`, `Estado`, `Fecha-Modificacion`, `Usuario`)
	     VALUES ('$nombre_lista', '$nombre_esc', '$ahora', '1', '$ahora', '$usuario_esc')");
	return (int) mysqli_insert_id($conexion);
}

/**
 * Lista General vigente (la que ven las pantallas sin lista propia) —
 * misma resolución que app/index.php. Con $usuario la crea si no existe.
 */
function lista_general($conexion, $usuario = null) {
	$sql = mysqli_query($conexion,
	    "SELECT `ID` FROM `lista-reproduccion`
	     WHERE (`Modulo` IS NULL OR `Modulo` = '') AND `Fecha-Inicio` <= NOW()
	     ORDER BY `Fecha-Inicio` DESC LIMIT 1");
	$row = $sql ? mysqli_fetch_array($sql) : null;
	if ($row) {
		return (int) $row['ID'];
	}
	if ($usuario === null) {
		return null;
	}
	$ahora       = date("Y-m-d H:i:s");
	$usuario_esc = mysqli_real_escape_string($conexion, $usuario);
	mysqli_query($conexion,
	    "INSERT INTO `lista-reproduccion` (`Nombre`, `Modulo`, `Fecha-Inicio`, `Estado`, `Fecha-Modificacion`, `Usuario`)
	     VALUES ('General', NULL, '$ahora', '1', '$ahora', '$usuario_esc')");
	return (int) mysqli_insert_id($conexion);
}

/** Marca la lista como modificada para que el kiosco recargue (app/tiempo.php). */
function marcar_lista_modificada($conexion, $id_lista) {
	$id_lista = (int) $id_lista;
	if (!$id_lista) { return; }
	$ahora = date("Y-m-d H:i:s");
	mysqli_query($conexion, "UPDATE `lista-reproduccion` SET `Fecha-Modificacion` = '$ahora' WHERE `ID` = $id_lista");
}

/** Siguiente posición libre al final de una lista. */
function siguiente_orden($conexion, $id_lista) {
	$id_lista = (int) $id_lista;
	$sql = mysqli_query($conexion, "SELECT MAX(CAST(`Orden` AS SIGNED)) AS m FROM `contenido` WHERE `Lista-Reproduccion` = $id_lista");
	$row = $sql ? mysqli_fetch_array($sql) : null;
	return ($row && $row['m'] !== null) ? ((int) $row['m'] + 1) : 1;
}

/**
 * Convierte el valor de un <input type="datetime-local"> en un literal SQL:
 * 'YYYY-MM-DD HH:MM:SS' entre comillas, o NULL si viene vacío/inválido.
 */
function fecha_sql($valor) {
	$valor = trim((string) $valor);
	if ($valor === '') { return 'NULL'; }
	$ts = strtotime(str_replace('T', ' ', $valor));
	return $ts ? "'" . date("Y-m-d H:i:s", $ts) . "'" : 'NULL';
}

/** Valor para precargar un <input type="datetime-local"> desde la BD. */
function fecha_input($valor) {
	return $valor ? date("Y-m-d\TH:i", strtotime($valor)) : '';
}

/**
 * Estado efectivo de una publicación ahora mismo, para mostrarlo en el admin.
 * Devuelve [clave, etiqueta, clase de badge].
 */
function estado_publicacion($row, $ahora = null) {
	$ahora = $ahora ?: date("Y-m-d H:i:s");
	if ($row['Estado'] != '1') {
		return ['inactivo', 'Inactivo', 'badge-secondary'];
	}
	if (!empty($row['Fecha_Inicio']) && $row['Fecha_Inicio'] > $ahora) {
		return ['programado', 'Programado', 'badge-info'];
	}
	if (!empty($row['Fecha_Fin']) && $row['Fecha_Fin'] <= $ahora) {
		return ['vencido', 'Finalizado', 'badge-warning'];
	}
	return ['publicado', 'Publicado', 'badge-success'];
}

/** Texto corto de la ventana de publicación ("8 oct 10:00 → sin fin"). */
function ventana_publicacion($row) {
	$fmt = function ($f) { return date("d/m/Y H:i", strtotime($f)); };
	$desde = !empty($row['Fecha_Inicio']) ? $fmt($row['Fecha_Inicio']) : 'ya';
	$hasta = !empty($row['Fecha_Fin'])    ? $fmt($row['Fecha_Fin'])    : 'sin fin';
	return $desde . ' → ' . $hasta;
}

/** Registra un archivo en la biblioteca (idempotente por URL). */
function registrar_en_biblioteca($conexion, $url, $tipo, $usuario) {
	$url_esc     = mysqli_real_escape_string($conexion, $url);
	$tipo_esc    = $tipo === 'video' ? 'video' : 'image';
	$usuario_esc = mysqli_real_escape_string($conexion, $usuario);
	$ahora       = date("Y-m-d H:i:s");
	mysqli_query($conexion,
	    "INSERT IGNORE INTO `biblioteca` (`URL`, `Tipo`, `Fecha_Subida`, `Usuario`)
	     VALUES ('$url_esc', '$tipo_esc', '$ahora', '$usuario_esc')");
}

/**
 * Guarda un archivo subido ($_FILES[...] de un solo archivo) en
 * assets/galeria/ y lo registra en la biblioteca. Nunca sobrescribe: si ya
 * existe un archivo con ese nombre le agrega un sufijo (-1, -2, ...), para
 * no cambiarle el contenido a publicaciones existentes.
 *
 * $dir_galeria es relativo al script que llama (admin/ → 'assets/galeria/').
 * Devuelve ['URL' => ..., 'Tipo' => ...] o un string con el error.
 */
function guardar_subida($conexion, $archivo, $usuario, $dir_galeria = 'assets/galeria/') {
	if (!isset($archivo['error']) || $archivo['error'] !== UPLOAD_ERR_OK) {
		return 'No se recibió el archivo (puede superar el tamaño máximo del servidor).';
	}
	if ($archivo['size'] > CONTENIDO_MAX_MB * 1048576) {
		return 'El archivo "' . $archivo['name'] . '" supera los ' . CONTENIDO_MAX_MB . ' MB.';
	}
	$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
	$tipos     = ['png' => 'image', 'jpg' => 'image', 'jpeg' => 'image', 'mp4' => 'video'];
	if (!isset($tipos[$extension])) {
		return 'Formato no permitido en "' . $archivo['name'] . '" (solo PNG, JPG o MP4).';
	}

	$base = pathinfo(basename($archivo['name']), PATHINFO_FILENAME);
	$base = preg_replace('/[^A-Za-z0-9._-]+/', '_', $base);
	$base = trim(substr($base, 0, 200), '._') ?: 'archivo';

	$nombre = $base . '.' . $extension;
	for ($i = 1; file_exists($dir_galeria . $nombre); $i++) {
		$nombre = $base . '-' . $i . '.' . $extension;
	}

	if (!move_uploaded_file($archivo['tmp_name'], $dir_galeria . $nombre)) {
		return 'No se pudo guardar "' . $archivo['name'] . '" en el servidor.';
	}

	registrar_en_biblioteca($conexion, $nombre, $tipos[$extension], $usuario);
	return ['URL' => $nombre, 'Tipo' => $tipos[$extension]];
}

/**
 * Publica (o reprograma, si ya estaba en esa lista) un archivo de la
 * biblioteca en una lista. $fecha_inicio/$fecha_fin son literales SQL de
 * fecha_sql(). Devuelve true/false.
 */
function publicar_en_lista($conexion, $id_lista, $url, $tipo, $fecha_inicio, $fecha_fin, $usuario) {
	$id_lista    = (int) $id_lista;
	$url_esc     = mysqli_real_escape_string($conexion, $url);
	$tipo_esc    = $tipo === 'video' ? 'video' : 'image';
	$usuario_esc = mysqli_real_escape_string($conexion, $usuario);
	$ahora       = date("Y-m-d H:i:s");

	$existe = mysqli_query($conexion, "SELECT `ID` FROM `contenido` WHERE `Lista-Reproduccion` = $id_lista AND `URL` = '$url_esc' LIMIT 1");
	$row    = $existe ? mysqli_fetch_array($existe) : null;

	if ($row) {
		$ok = mysqli_query($conexion,
		    "UPDATE `contenido` SET `Estado` = 1, `Fecha_Inicio` = $fecha_inicio, `Fecha_Fin` = $fecha_fin,
		            `Fecha_Modificación` = '$ahora', `Usuario` = '$usuario_esc'
		     WHERE `ID` = " . (int) $row['ID']);
	} else {
		$orden = siguiente_orden($conexion, $id_lista);
		$ok = mysqli_query($conexion,
		    "INSERT INTO `contenido` (`URL`, `Tipo`, `Estado`, `Orden`, `Fecha_Inicio`, `Fecha_Fin`, `Lista-Reproduccion`, `Fecha_Modificación`, `Usuario`)
		     VALUES ('$url_esc', '$tipo_esc', 1, '$orden', $fecha_inicio, $fecha_fin, $id_lista, '$ahora', '$usuario_esc')");
	}

	if ($ok) {
		marcar_lista_modificada($conexion, $id_lista);
	}
	return (bool) $ok;
}
