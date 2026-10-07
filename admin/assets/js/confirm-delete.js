/**
 * confirm-delete.js
 *
 * Conecta cualquier link "Eliminar" (class="js-confirm-delete") con el
 * modal compartido (admin/assets/php/confirm_delete_modal.php). El link
 * nunca navega directo: el modal muestra el texto contextual (data-title/
 * data-body) y solo al confirmar se sigue el href real.
 */
$(document).on('click', '.js-confirm-delete', function (e) {
	e.preventDefault();
	var href = this.getAttribute('href');
	var title = $(this).data('title') || '¿Eliminar este elemento?';
	var body = $(this).data('body') || 'Esta acción no se puede deshacer.';

	$('#confirmDeleteModalLabel').text(title);
	$('#confirmDeleteModalBody').text(body);
	$('#confirmDeleteModalConfirm').attr('href', href);
	$('#confirmDeleteModal').modal('show');
});
