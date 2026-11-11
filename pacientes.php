<?php
require_once 'php/auth.php';
requerir_sesion_pagina();
$titulo = 'Pacientes';
$activa = 'pacientes';
include 'inc/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <h4 class="mb-2 mb-md-0">Pacientes</h4>
    <div class="d-flex">
        <input type="search" id="buscar" class="form-control mr-2" placeholder="Buscar por cédula o nombre...">
        <button class="btn btn-teal text-nowrap" id="btn-nuevo"><i class="fa fa-plus"></i> Nuevo paciente</button>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="thead-light">
                <tr><th>Cédula</th><th>Paciente</th><th>Edad</th><th>Teléfono</th><th class="text-right">Acciones</th></tr>
            </thead>
            <tbody id="listado"></tbody>
        </table>
    </div>
</div>

<!-- Ventana para registrar / editar un paciente -->
<div class="modal fade" id="modal-paciente" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-paciente">
                <div class="modal-header">
                    <h5 class="modal-title" id="titulo-modal">Nuevo paciente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="alerta-modal"></div>
                    <input type="hidden" name="id" id="id">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="cedula">Cédula *</label>
                            <input type="text" class="form-control" name="cedula" id="cedula" pattern="[0-9]{5,12}" title="Entre 5 y 12 números" maxlength="12" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="nombre">Nombre *</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" maxlength="50" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="apellido">Apellido *</label>
                            <input type="text" class="form-control" name="apellido" id="apellido" maxlength="50" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="fecha_nacimiento">Fecha de nacimiento *</label>
                            <input type="date" class="form-control" name="fecha_nacimiento" id="fecha_nacimiento" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="sexo">Sexo *</label>
                            <select class="form-control" name="sexo" id="sexo" required>
                                <option value="">Seleccione...</option>
                                <option value="F">Femenino</option>
                                <option value="M">Masculino</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="telefono">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" id="telefono" maxlength="20">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="correo">Correo electrónico</label>
                            <input type="email" class="form-control" name="correo" id="correo" maxlength="100">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="direccion">Dirección</label>
                            <input type="text" class="form-control" name="direccion" id="direccion" maxlength="200">
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label for="antecedentes">Antecedentes médicos (alergias, enfermedades, medicamentos)</label>
                        <textarea class="form-control" name="antecedentes" id="antecedentes" rows="3" maxlength="1000"></textarea>
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
$scripts = array('assets/js/pacientes.js');
include 'inc/footer.php';
