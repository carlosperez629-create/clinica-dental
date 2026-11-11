<?php
require_once 'php/auth.php';
requerir_sesion_pagina();
$titulo = 'Procedimientos';
$activa = 'procedimientos';
include 'inc/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Procedimientos odontológicos</h4>
    <button class="btn btn-teal" id="btn-nuevo"><i class="fa fa-plus"></i> Nuevo procedimiento</button>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="thead-light">
                <tr><th>Procedimiento</th><th>Descripción</th><th>Precio</th><th>Estado</th><th class="text-right">Acciones</th></tr>
            </thead>
            <tbody id="listado"></tbody>
        </table>
    </div>
</div>

<!-- Ventana para registrar / editar un procedimiento -->
<div class="modal fade" id="modal-procedimiento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-procedimiento">
                <div class="modal-header">
                    <h5 class="modal-title" id="titulo-modal">Nuevo procedimiento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="alerta-modal"></div>
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <label for="nombre">Nombre *</label>
                        <input type="text" class="form-control" name="nombre" id="nombre" maxlength="80" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <input type="text" class="form-control" name="descripcion" id="descripcion" maxlength="255">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="precio">Precio ($) *</label>
                            <input type="number" class="form-control" name="precio" id="precio" min="0" step="0.01" required>
                        </div>
                        <div class="form-group col-6">
                            <label for="activo">Estado</label>
                            <select class="form-control" name="activo" id="activo">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-teal"><i class="fa fa-floppy-o"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$scripts = array('assets/js/procedimientos.js');
include 'inc/footer.php';
