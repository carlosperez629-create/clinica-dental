/**
 * Funciones comunes a todas las páginas.
 */

// Escapa texto antes de insertarlo como HTML (evita XSS)
function esc(t) {
    return $('<div>').text(t == null ? '' : t).html();
}

// 2026-10-08 -> 08/10/2026
function fmtFecha(f) {
    if (!f) { return ''; }
    var p = f.split('-');
    return p[2] + '/' + p[1] + '/' + p[0];
}

function dinero(n) {
    return '$' + parseFloat(n || 0).toFixed(2);
}

// Edad en años a partir de AAAA-MM-DD
function edad(f) {
    var p = f.split('-');
    var hoy = new Date();
    var e = hoy.getFullYear() - parseInt(p[0], 10);
    var m = (hoy.getMonth() + 1) - parseInt(p[1], 10);
    if (m < 0 || (m === 0 && hoy.getDate() < parseInt(p[2], 10))) { e--; }
    return e;
}

// Muestra un mensaje (success, danger, warning...) en el contenedor indicado
function alerta(tipo, mensaje, contenedor) {
    var $a = $('<div class="alert alert-' + tipo + ' alert-dismissible fade show" role="alert"></div>')
        .text(mensaje)
        .append('<button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span>&times;</span></button>');
    $(contenedor || '#alertas').html($a);
    if (tipo === 'success') {
        setTimeout(function () { $a.alert('close'); }, 4000);
    }
}

/**
 * Petición AJAX al servidor. Devuelve una promesa:
 *  - .done(respuesta)  cuando el servidor responde estado "exito"
 *  - .fail(mensaje)    cuando responde estado "error" o hay un fallo de comunicación
 * Así cada pantalla siempre muestra al usuario lo que respondió el servidor.
 */
function api(url, datos, metodo) {
    return $.ajax({ url: url, type: metodo || 'POST', data: datos, dataType: 'json' }).then(
        function (r) {
            if (r.estado !== 'exito') {
                return $.Deferred().reject(r.mensaje).promise();
            }
            return r;
        },
        function (xhr) {
            if (xhr.status === 401) {
                window.location = 'index.php';   // sesión vencida
            }
            var m = (xhr.responseJSON && xhr.responseJSON.mensaje) ||
                    (xhr.status === 200 ? 'El servidor devolvió una respuesta inesperada.' : 'Error de comunicación con el servidor.');
            return $.Deferred().reject(m).promise();
        }
    );
}
