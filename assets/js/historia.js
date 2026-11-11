$(function () {
    var pacienteId = parseInt($('#paciente-id').val(), 10);
    var atenciones = [];     // procedimientos de la historia (para editar)

    cargarPaciente();
    cargarProcedimientos();
    listar();

    function cargarPaciente() {
        api('CRUD/pacientes/seleccionar.php', { id: pacienteId }, 'GET').done(function (r) {
            var p = r.datos;
            $('#datos-paciente').html(
                '<div class="row">' +
                    '<div class="col-md-4 mb-2"><div class="dato-etiqueta">Paciente</div><strong>' + esc(p.nombre + ' ' + p.apellido) + '</strong></div>' +
                    '<div class="col-md-2 mb-2"><div class="dato-etiqueta">Cédula</div>' + esc(p.cedula) + '</div>' +
                    '<div class="col-md-3 mb-2"><div class="dato-etiqueta">Nacimiento</div>' + fmtFecha(p.fecha_nacimiento) + ' (' + edad(p.fecha_nacimiento) + ' años)</div>' +
                    '<div class="col-md-3 mb-2"><div class="dato-etiqueta">Sexo</div>' + (p.sexo === 'M' ? 'Masculino' : 'Femenino') + '</div>' +
                    '<div class="col-md-4 mb-2"><div class="dato-etiqueta">Teléfono</div>' + (esc(p.telefono) || '-') + '</div>' +
                    '<div class="col-md-4 mb-2"><div class="dato-etiqueta">Correo</div>' + (esc(p.correo) || '-') + '</div>' +
                    '<div class="col-md-4 mb-2"><div class="dato-etiqueta">Dirección</div>' + (esc(p.direccion) || '-') + '</div>' +
                    '<div class="col-12"><div class="dato-etiqueta">Antecedentes médicos</div>' + (esc(p.antecedentes) || 'Ninguno registrado') + '</div>' +
                '</div>');
        }).fail(function (m) {
            $('#datos-paciente').text('No se pudo cargar el paciente.');
            alerta('danger', m);
        });
    }

    // Llena el selector con los procedimientos activos del catálogo
    function cargarProcedimientos() {
        api('CRUD/procedimientos/listar.php', { activos: 1 }, 'GET').done(function (r) {
            var html = '<option value="">Seleccione...</option>';
            r.datos.forEach(function (p) {
                html += '<option value="' + p.id + '" data-precio="' + p.precio + '">' + esc(p.nombre) + '</option>';
            });
            $('#procedimiento_id').html(html);
        }).fail(function (m) { alerta('danger', m); });
    }

    // Al elegir un procedimiento se propone su precio como costo (se puede cambiar)
    $('#procedimiento_id').on('change', function () {
        var precio = $(this).find('option:selected').data('precio');
        $('#costo').val(precio !== undefined ? precio : '');
    });

    function listar() {
        api('CRUD/atenciones/listar.php', { paciente_id: pacienteId }, 'GET').done(function (r) {
            atenciones = r.datos;
            var html = '', total = 0;
            atenciones.forEach(function (a) {
                total += parseFloat(a.costo);
                html += '<tr data-id="' + a.id + '">' +
                    '<td class="text-nowrap">' + fmtFecha(a.fecha) + '</td>' +
                    '<td>' + esc(a.procedimiento) + '</td>' +
                    '<td>' + esc(a.pieza_dental) + '</td>' +
                    '<td>' + esc(a.diagnostico) + '</td>' +
                    '<td>' + esc(a.observaciones) + '</td>' +
                    '<td class="text-right">' + dinero(a.costo) + '</td>' +
                    '<td class="text-right text-nowrap">' +
                        '<button class="btn btn-sm btn-warning btn-editar"><i class="fa fa-pencil"></i></button> ' +
                        '<button class="btn btn-sm btn-danger btn-eliminar"><i class="fa fa-trash"></i></button>' +
                    '</td></tr>';
            });
            $('#listado').html(html || '<tr><td colspan="7" class="text-center text-muted py-4">Este paciente aún no tiene procedimientos registrados.</td></tr>');
            $('#total').html(atenciones.length ? '<tr><th colspan="5" class="text-right">Total</th><th class="text-right">' + dinero(total) + '</th><th></th></tr>' : '');
        }).fail(function (m) { alerta('danger', m); });
    }

    $('#btn-nueva').on('click', function () {
        $('#form-atencion')[0].reset();
        $('#id').val('');
        $('#alerta-modal').empty();
        $('#fecha').val(new Date().toISOString().slice(0, 10));
        $('#titulo-modal').text('Nuevo procedimiento');
        $('#modal-atencion').modal('show');
    });

    $(document).on('click', '.btn-editar', function () {
        var id = $(this).closest('tr').data('id');
        api('CRUD/atenciones/seleccionar.php', { id: id }, 'GET').done(function (r) {
            var a = r.datos;
            $('#form-atencion')[0].reset();
            $('#alerta-modal').empty();
            // Si el procedimiento fue desactivado, se agrega al selector para no perderlo
            if ($('#procedimiento_id option[value="' + a.procedimiento_id + '"]').length === 0) {
                var nombre = atenciones.filter(function (x) { return x.id == a.id; })[0].procedimiento;
                $('#procedimiento_id').append('<option value="' + a.procedimiento_id + '">' + esc(nombre) + ' (inactivo)</option>');
            }
            $('#id').val(a.id);
            $('#procedimiento_id').val(a.procedimiento_id);
            $('#fecha').val(a.fecha);
            $('#pieza_dental').val(a.pieza_dental);
            $('#diagnostico').val(a.diagnostico);
            $('#observaciones').val(a.observaciones);
            $('#costo').val(a.costo);
            $('#titulo-modal').text('Editar procedimiento');
            $('#modal-atencion').modal('show');
        }).fail(function (m) { alerta('danger', m); });
    });

    $('#form-atencion').on('submit', function (e) {
        e.preventDefault();
        var archivo = $('#id').val() ? 'editar.php' : 'agregar.php';
        api('CRUD/atenciones/' + archivo, $(this).serialize()).done(function (r) {
            $('#modal-atencion').modal('hide');
            alerta('success', r.mensaje);
            listar();
        }).fail(function (m) { alerta('danger', m, '#alerta-modal'); });
    });

    $(document).on('click', '.btn-eliminar', function () {
        var id = $(this).closest('tr').data('id');
        if (!confirm('¿Eliminar este procedimiento de la historia clínica?')) { return; }
        api('CRUD/atenciones/eliminar.php', { id: id }).done(function (r) {
            alerta('success', r.mensaje);
            listar();
        }).fail(function (m) { alerta('danger', m); });
    });
});
