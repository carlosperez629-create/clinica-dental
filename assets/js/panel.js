$(function () {
    api('php/resumen.php', {}, 'GET').done(function (r) {
        $('#n-pacientes').text(r.pacientes);
        $('#n-procedimientos').text(r.procedimientos);
        $('#n-mes').text(r.atenciones_mes);
        $('#n-total').text(r.atenciones);

        var html = '';
        r.ultimas.forEach(function (u) {
            html += '<tr>' +
                '<td>' + fmtFecha(u.fecha) + '</td>' +
                '<td><a href="historia.php?id=' + u.paciente_id + '">' + esc(u.nombre + ' ' + u.apellido) + '</a></td>' +
                '<td>' + esc(u.procedimiento) + '</td></tr>';
        });
        $('#ultimas').html(html || '<tr><td colspan="3" class="text-center text-muted py-4">Aún no hay procedimientos registrados.</td></tr>');
    }).fail(function (m) { alerta('danger', m); });
});
