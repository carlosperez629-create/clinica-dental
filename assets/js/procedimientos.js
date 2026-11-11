$(function () {
    listar();

    function listar() {
        api('CRUD/procedimientos/listar.php', {}, 'GET').done(function (r) {
            var html = '';
            r.datos.forEach(function (p) {
                var estado = p.activo == 1 ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-secondary">Inactivo</span>';
                html += '<tr data-id="' + p.id + '">' +
                    '<td>' + esc(p.nombre) + '</td>' +
                    '<td>' + esc(p.descripcion) + '</td>' +
                    '<td>' + dinero(p.precio) + '</td>' +
                    '<td>' + estado + '</td>' +
                    '<td class="text-right text-nowrap">' +
                        '<button class="btn btn-sm btn-warning btn-editar"><i class="fa fa-pencil"></i></button> ' +
                        '<button class="btn btn-sm btn-danger btn-eliminar"><i class="fa fa-trash"></i></button>' +
                    '</td></tr>';
            });
            $('#listado').html(html || '<tr><td colspan="5" class="text-center text-muted py-4">Aún no hay procedimientos registrados.</td></tr>');
        }).fail(function (m) { alerta('danger', m); });
    }

    $('#btn-nuevo').on('click', function () {
        $('#form-procedimiento')[0].reset();
        $('#id').val('');
        $('#alerta-modal').empty();
        $('#titulo-modal').text('Nuevo procedimiento');
        $('#modal-procedimiento').modal('show');
    });

    $(document).on('click', '.btn-editar', function () {
        var id = $(this).closest('tr').data('id');
        api('CRUD/procedimientos/seleccionar.php', { id: id }, 'GET').done(function (r) {
            var p = r.datos;
            $('#form-procedimiento')[0].reset();
            $('#alerta-modal').empty();
            $('#id').val(p.id);
            $('#nombre').val(p.nombre);
            $('#descripcion').val(p.descripcion);
            $('#precio').val(p.precio);
            $('#activo').val(p.activo);
            $('#titulo-modal').text('Editar procedimiento');
            $('#modal-procedimiento').modal('show');
        }).fail(function (m) { alerta('danger', m); });
    });

    $('#form-procedimiento').on('submit', function (e) {
        e.preventDefault();
        var archivo = $('#id').val() ? 'editar.php' : 'agregar.php';
        api('CRUD/procedimientos/' + archivo, $(this).serialize()).done(function (r) {
            $('#modal-procedimiento').modal('hide');
            alerta('success', r.mensaje);
            listar();
        }).fail(function (m) { alerta('danger', m, '#alerta-modal'); });
    });

    $(document).on('click', '.btn-eliminar', function () {
        var id = $(this).closest('tr').data('id');
        if (!confirm('¿Eliminar este procedimiento?')) { return; }
        api('CRUD/procedimientos/eliminar.php', { id: id }).done(function (r) {
            alerta('success', r.mensaje);
            listar();
        }).fail(function (m) { alerta('danger', m); });
    });
});
