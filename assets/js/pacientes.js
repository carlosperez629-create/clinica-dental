$(function () {
    var temporizador;
    listar();

    // Búsqueda mientras se escribe (con una pequeña espera)
    $('#buscar').on('input', function () {
        clearTimeout(temporizador);
        temporizador = setTimeout(listar, 300);
    });

    function listar() {
        api('CRUD/pacientes/listar.php', { q: $('#buscar').val() }, 'GET').done(function (r) {
            var html = '';
            r.datos.forEach(function (p) {
                html += '<tr data-id="' + p.id + '">' +
                    '<td>' + esc(p.cedula) + '</td>' +
                    '<td>' + esc(p.nombre + ' ' + p.apellido) + '</td>' +
                    '<td>' + edad(p.fecha_nacimiento) + ' años</td>' +
                    '<td>' + esc(p.telefono) + '</td>' +
                    '<td class="text-right text-nowrap">' +
                        '<a class="btn btn-sm btn-info" href="historia.php?id=' + p.id + '"><i class="fa fa-file-text-o"></i> Historia</a> ' +
                        '<button class="btn btn-sm btn-warning btn-editar"><i class="fa fa-pencil"></i></button> ' +
                        '<button class="btn btn-sm btn-danger btn-eliminar"><i class="fa fa-trash"></i></button>' +
                    '</td></tr>';
            });
            $('#listado').html(html || '<tr><td colspan="5" class="text-center text-muted py-4">No hay pacientes para mostrar.</td></tr>');
        }).fail(function (m) { alerta('danger', m); });
    }

    // Abrir el formulario vacío para un paciente nuevo
    $('#btn-nuevo').on('click', function () {
        $('#form-paciente')[0].reset();
        $('#id').val('');
        $('#alerta-modal').empty();
        $('#titulo-modal').text('Nuevo paciente');
        $('#modal-paciente').modal('show');
    });

    // Cargar los datos del paciente en el formulario para editarlo
    $(document).on('click', '.btn-editar', function () {
        var id = $(this).closest('tr').data('id');
        api('CRUD/pacientes/seleccionar.php', { id: id }, 'GET').done(function (r) {
            var p = r.datos;
            $('#form-paciente')[0].reset();
            $('#alerta-modal').empty();
            $('#id').val(p.id);
            $('#cedula').val(p.cedula);
            $('#nombre').val(p.nombre);
            $('#apellido').val(p.apellido);
            $('#fecha_nacimiento').val(p.fecha_nacimiento);
            $('#sexo').val(p.sexo);
            $('#telefono').val(p.telefono);
            $('#correo').val(p.correo);
            $('#direccion').val(p.direccion);
            $('#antecedentes').val(p.antecedentes);
            $('#titulo-modal').text('Editar paciente');
            $('#modal-paciente').modal('show');
        }).fail(function (m) { alerta('danger', m); });
    });

    // Guardar: agregar o editar según exista el id oculto
    $('#form-paciente').on('submit', function (e) {
        e.preventDefault();
        var archivo = $('#id').val() ? 'editar.php' : 'agregar.php';
        api('CRUD/pacientes/' + archivo, $(this).serialize()).done(function (r) {
            $('#modal-paciente').modal('hide');
            alerta('success', r.mensaje);
            listar();
        }).fail(function (m) { alerta('danger', m, '#alerta-modal'); });
    });

    // Eliminar (pide confirmación porque también borra su historia clínica)
    $(document).on('click', '.btn-eliminar', function () {
        var id = $(this).closest('tr').data('id');
        if (!confirm('¿Eliminar este paciente y toda su historia clínica? Esta acción no se puede deshacer.')) { return; }
        api('CRUD/pacientes/eliminar.php', { id: id }).done(function (r) {
            alerta('success', r.mensaje);
            listar();
        }).fail(function (m) { alerta('danger', m); });
    });
});
