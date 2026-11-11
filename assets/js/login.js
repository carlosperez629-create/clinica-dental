$(function () {
    $('#form-login').on('submit', function (e) {
        e.preventDefault();
        api('php/login.php', $(this).serialize())
            .done(function () { window.location = 'panel.php'; })
            .fail(function (mensaje) { alerta('danger', mensaje); });
    });
});
