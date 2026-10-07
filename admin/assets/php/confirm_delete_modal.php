<?php
/**
 * confirm_delete_modal.php — Módulo Administrador
 *
 * Modal de confirmación reutilizable para cualquier acción de eliminar.
 * Se incluye una sola vez por página; los links "Eliminar" lo disparan
 * con la clase .js-confirm-delete y los atributos data-title/data-body
 * (ver admin/assets/js/confirm-delete.js) — así el texto es contextual
 * al recurso real, sin duplicar el modal por cada fila de una tabla.
 */
?>
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel">¿Eliminar este elemento?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <p id="confirmDeleteModalBody">Esta acción no se puede deshacer.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <a href="#" id="confirmDeleteModalConfirm" class="btn btn-danger">Confirmar eliminación</a>
      </div>
    </div>
  </div>
</div>
