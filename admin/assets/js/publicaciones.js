/**
 * publicaciones.js
 *
 * - Listas ordenables (.js-sortable, ver assets/php/lista_publicaciones.php):
 *   arrastrar o usar las flechas guarda el orden nuevo vía
 *   assets/php/ordenar_contenido.php. Requiere jQuery UI (sortable).
 * - Modal "Publicar" de la biblioteca (contenido.php): se abre desde el
 *   ícono de cada archivo y se llena con sus datos.
 * - Filtro Todos / Imágenes / Videos de la biblioteca.
 */
(function ($) {

	function numerar($lista) {
		$lista.children('li').each(function (i) {
			$(this).find('.publicacion-pos').text(i + 1);
		});
	}

	function guardarOrden($lista) {
		numerar($lista);
		var ids = $lista.children('li').map(function () { return $(this).data('id'); }).get();
		$lista.addClass('guardando');
		$.post('assets/php/ordenar_contenido.php', { lista: $lista.data('lista'), ids: ids })
			.fail(function () { alert('No se pudo guardar el nuevo orden. Recarga la página e inténtalo de nuevo.'); })
			.always(function () { $lista.removeClass('guardando'); });
	}

	$(function () {
		$('.js-sortable').each(function () {
			var $lista = $(this);
			numerar($lista);
			if ($.fn.sortable) {
				$lista.sortable({
					handle: '.publicacion-handle',
					axis: 'y',
					placeholder: 'publicacion-placeholder',
					update: function () { guardarOrden($lista); }
				});
			}
		});

		$(document).on('click', '.js-mover', function () {
			var $li = $(this).closest('li');
			var $lista = $li.parent();
			if ($(this).data('dir') < 0) {
				if (!$li.prev().length) { return; }
				$li.insertBefore($li.prev());
			} else {
				if (!$li.next().length) { return; }
				$li.insertAfter($li.next());
			}
			guardarOrden($lista);
		});

		// --- Modal de publicar ---
		$(document).on('click', '.js-publicar', function (e) {
			e.preventDefault();
			var $btn = $(this);
			var $modal = $('#modalPublicar');
			$modal.find('[name="biblioteca_id"]').val($btn.data('id'));
			$modal.find('.js-publicar-nombre').text($btn.data('nombre'));
			$modal.find('[name="destinos[]"]').prop('checked', false);
			$modal.find('[name="fecha_inicio"], [name="fecha_fin"]').val('');
			// Marca (sin bloquear) dónde ya está publicado, como referencia.
			var publicadoEn = String($btn.data('publicado') || '').split(',');
			$modal.find('.js-destino').each(function () {
				var ya = publicadoEn.indexOf(String($(this).find('input').val())) !== -1;
				$(this).find('.js-ya-publicado').toggle(ya);
			});
			$modal.modal('show');
		});

		$('#modalPublicar').on('click', '.js-marcar-todas', function () {
			var $checks = $('#modalPublicar').find('[name="destinos[]"]');
			$checks.prop('checked', $checks.filter(':checked').length !== $checks.length);
		});

		$('#formPublicar').on('submit', function () {
			if (!$(this).find('[name="destinos[]"]:checked').length) {
				alert('Selecciona al menos una pantalla.');
				return false;
			}
			var ini = $(this).find('[name="fecha_inicio"]').val();
			var fin = $(this).find('[name="fecha_fin"]').val();
			if (ini && fin && fin <= ini) {
				alert('La fecha de fin debe ser posterior a la de inicio.');
				return false;
			}
			return true;
		});

		// --- Filtro de la biblioteca ---
		$(document).on('click', '.js-filtro-biblioteca', function () {
			var tipo = $(this).data('tipo');
			$('.js-filtro-biblioteca').removeClass('active');
			$(this).addClass('active');
			$('.biblioteca-item').each(function () {
				$(this).toggle(tipo === 'todos' || $(this).data('tipo') === tipo);
			});
		});

		// --- Subida: mostrar cuántos archivos se eligieron ---
		$('#archivosBiblioteca').on('change', function () {
			var permitidas = ['png', 'jpg', 'jpeg', 'mp4'];
			var maxMb = 80;
			for (var i = 0; this.files && i < this.files.length; i++) {
				var f = this.files[i];
				var ext = f.name.split('.').pop().toLowerCase();
				if (permitidas.indexOf(ext) === -1 || f.size / 1048576 > maxMb) {
					alert('"' + f.name + '" no es válido: solo PNG, JPG o MP4 de hasta ' + maxMb + ' MB.');
					this.value = '';
					break;
				}
			}
			var n = this.files ? this.files.length : 0;
			$('#archivosBibliotecaLabel').text(n ? n + ' archivo(s) seleccionado(s)' : 'Elegir archivos…');
			$('#btnSubirBiblioteca').prop('disabled', !n);
		});
	});

})(jQuery);
