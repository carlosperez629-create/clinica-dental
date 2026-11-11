<?php
require_once 'php/auth.php';
requerir_sesion_pagina();
$titulo = 'Panel principal';
$activa = 'panel';
include 'inc/header.php';
?>
<h4 class="mb-3">Panel principal</h4>

<div class="row">
    <div class="col-6 col-lg-3 mb-3">
        <div class="card tarjeta-dato"><div class="card-body">
            <div class="text-muted">Pacientes</div>
            <div class="numero" id="n-pacientes">-</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3 mb-3">
        <div class="card tarjeta-dato"><div class="card-body">
            <div class="text-muted">Procedimientos activos</div>
            <div class="numero" id="n-procedimientos">-</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3 mb-3">
        <div class="card tarjeta-dato"><div class="card-body">
            <div class="text-muted">Atenciones este mes</div>
            <div class="numero" id="n-mes">-</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3 mb-3">
        <div class="card tarjeta-dato"><div class="card-body">
            <div class="text-muted">Atenciones totales</div>
            <div class="numero" id="n-total">-</div>
        </div></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-3">
        <div class="card shadow-sm">
            <div class="card-header bg-white"><strong>Últimos procedimientos registrados</strong></div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light"><tr><th>Fecha</th><th>Paciente</th><th>Procedimiento</th></tr></thead>
                    <tbody id="ultimas"></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-header bg-white"><strong>Accesos rápidos</strong></div>
            <div class="list-group list-group-flush">
                <a href="pacientes.php" class="list-group-item list-group-item-action"><i class="fa fa-user-plus"></i> Registrar o buscar un paciente</a>
                <a href="procedimientos.php" class="list-group-item list-group-item-action"><i class="fa fa-medkit"></i> Gestionar procedimientos</a>
            </div>
        </div>
    </div>
</div>
<?php
$scripts = array('assets/js/panel.js');
include 'inc/footer.php';
