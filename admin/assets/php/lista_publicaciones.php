<?php
/**
 * lista_publicaciones.php — Módulo Administrador (parcial)
 *
 * Lista ordenable de lo publicado en una lista de reproducción: arrastrar
 * (o usar las flechas) cambia el orden en que la pantalla lo reproduce y
 * se guarda solo (assets/js/publicaciones.js → ordenar_contenido.php).
 * Cada fila muestra su ventana de publicación y su estado efectivo.
 *
 * Variables que espera del archivo que lo incluye:
 *   $conexion, $lista_render (ID de lista o null), $name_user,
 *   $texto_sin_lista (opcional) — mensaje si $lista_render es null ('' = nada).
 * Requiere contenido_helpers.php ya incluido.
 */

if (!$lista_render) {
	$texto_sin_lista = $texto_sin_lista ?? 'Todavía no hay nada publicado aquí.';
	if ($texto_sin_lista !== '') {
		echo '<p class="text-muted mb-0">' . $texto_sin_lista . '</p>';
	}
	return;
}

$lista_render  = (int) $lista_render;
$sql_items     = mysqli_query($conexion,
    "SELECT * FROM `contenido` WHERE `Lista-Reproduccion` = $lista_render
     ORDER BY CAST(`Orden` AS SIGNED) ASC, `ID` ASC");
$ahora_render  = date("Y-m-d H:i:s");

if (mysqli_num_rows($sql_items) === 0) {
	echo '<p class="text-muted mb-0">Todavía no hay nada publicado aquí. Usa el ícono <i class="fa fa-paper-plane"></i> de un archivo de la biblioteca para publicarlo.</p>';
	return;
}
?>
<p class="text-muted small mb-2"><i class="fa fa-arrows-v"></i> Arrastra los elementos (o usa las flechas) para cambiar el orden de reproducción — se guarda automáticamente.</p>
<ul class="lista-publicaciones js-sortable" data-lista="<?php echo $lista_render; ?>">
<?php while ($item = mysqli_fetch_array($sql_items)):
	list($clave_estado, $etiqueta_estado, $clase_estado) = estado_publicacion($item, $ahora_render);
	$src = 'assets/galeria/' . rawurlencode($item['URL']);
?>
	<li class="publicacion publicacion-<?php echo $clave_estado; ?>" data-id="<?php echo (int) $item['ID']; ?>">
		<span class="publicacion-handle" title="Arrastrar para reordenar"><i class="fa fa-bars"></i></span>
		<span class="publicacion-pos"></span>
		<span class="publicacion-thumb">
			<?php if ($item['Tipo'] == 'video'): ?>
				<video src="<?php echo $src; ?>#t=0.5" preload="metadata" muted></video>
				<i class="fa fa-play-circle publicacion-thumb-icon"></i>
			<?php else: ?>
				<img src="<?php echo $src; ?>" alt="">
			<?php endif; ?>
		</span>
		<span class="publicacion-info">
			<strong><?php echo htmlspecialchars($item['URL']); ?></strong>
			<small class="d-block text-muted"><i class="fa fa-calendar"></i> <?php echo ventana_publicacion($item); ?></small>
		</span>
		<span class="badge <?php echo $clase_estado; ?>"><?php echo $etiqueta_estado; ?></span>
		<span class="btn-group btn-group-sm publicacion-acciones" role="group">
			<button type="button" class="btn btn-outline-secondary js-mover" data-dir="-1" title="Subir"><i class="fa fa-arrow-up"></i></button>
			<button type="button" class="btn btn-outline-secondary js-mover" data-dir="1" title="Bajar"><i class="fa fa-arrow-down"></i></button>
			<a href="editar_contenido.php?id=<?php echo (int) $item['ID']; ?>" class="btn btn-outline-secondary" title="Editar fechas / estado"><i class="fa fa-edit"></i></a>
			<a href="assets/php/eliminar_img.php?id=<?php echo (int) $item['ID']; ?>" class="btn btn-outline-danger js-confirm-delete" title="Quitar de aquí"
			   data-title="¿Quitar esta publicación?"
			   data-body="&quot;<?php echo htmlspecialchars($item['URL']); ?>&quot; dejará de mostrarse aquí. El archivo sigue en la biblioteca."><i class="fa fa-times"></i></a>
		</span>
	</li>
<?php endwhile; ?>
</ul>
