<?php
require_once 'php/auth.php';
requerir_sesion_pagina();
$paciente_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($paciente_id <= 0) {
    header('Location: pacientes.php');
    exit;
}
$titulo = 'Historia clínica';
$activa = 'pacientes';
include 'inc/header.php';
?>
<input type="hidden" id="paciente-id" value="<?= $paciente_id ?>">

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <h4 class="mb-2 mb-md-0">Historia clínica</h4>
    <div>
        <a href="pacientes.php" class="btn btn-outline-secondary"><i class="fa fa-arrow-left"></i> Volver</a>
        <a class="btn btn-danger" href="php/historia_pdf.php?id=<?= $paciente_id ?>" target="_blank" rel="noopener"><i class="fa fa-file-pdf-o"></i> Generar PDF</a>
        <button class="btn btn-teal" id="btn-nueva"><i class="fa fa-plus"></i> Nuevo procedimiento</button>
    </div>
</div>

<!-- Datos del paciente -->
<div class="card shadow-sm mb-4">
    <div class="card-body" id="datos-paciente">Cargando datos del paciente...</div>
</div>

<!-- Procedimientos realizados -->
<div class="card shadow-sm">
    <div class="card-header bg-white"><strong>Procedimientos realizados</strong></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="thead-light">
                <tr><th>Fecha</th><th>Procedimiento</th><th>Pieza</th><th>Diagnóstico</th><th>Observaciones</th><th class="text-right">Costo</th><th class="text-right">Acciones</th></tr>
            </thead>
            <tbody id="listado"></tbody>
            <tfoot id="total"></tfoot>
        </table>
    </div>
</div>

<!-- Ventana para registrar / editar un procedimiento de la historia -->
<div class="modal fade" id="modal-atencion" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-atencion">
                <div class="modal-header">
                    <h5 class="modal-title" id="titulo-modal">Nuevo procedimiento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="alerta-modal"></div>
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="paciente_id" value="<?= $paciente_id ?>">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="procedimiento_id">Procedimiento *</label>
                            <select class="form-control" name="procedimiento_id" id="procedimiento_id" required></select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="fecha">Fecha *</label>
                            <input type="date" class="form-control" name="fecha" id="fecha" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="pieza_dental">Pieza dental</label>
                            <input type="text" class="form-control" name="pieza_dental" id="pieza_dental" maxlength="20" placeholder="Ej: 36">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="diagnostico">Diagnóstico</label>
                        <textarea class="form-control" name="diagnostico" id="diagnostico" rows="2" maxlength="500"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="observaciones">Observaciones del tratamiento</label>
                        <textarea class="form-control" name="observaciones" id="observaciones" rows="2" maxlength="1000"></textarea>
                    </div>
                    <div class="form-group col-md-4 px-0 mb-0">
                        <label for="costo">Costo ($)</label>
                        <input type="number" class="form-control" name="costo" id="costo" min="0" step="0.01">
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
$scripts = array('assets/js/historia.js');
include 'inc/footer.php';
