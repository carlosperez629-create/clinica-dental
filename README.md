# Clínica Dental - Historias clínicas odontológicas

Aplicación web para registrar pacientes y llevar su historia clínica (limpieza, blanqueamiento,
extracciones, tratamientos de conducto, calzas, etc.). Proyecto académico.

**Tecnologías:** PHP (MySQLi) · MySQL · AJAX (jQuery) · Bootstrap 4 · FPDF (PDF)

## Funciones
- Inicio de sesión y cierre de sesión (contraseñas con `password_hash`).
- Panel principal con totales y últimos procedimientos, y menú de opciones.
- Pacientes: registrar, buscar, editar y eliminar.
- Procedimientos odontológicos: catálogo con precio y estado (activo/inactivo).
- Historia clínica por paciente: agregar, editar y eliminar procedimientos realizados.
- Generación de la historia clínica en **PDF** desde el servidor (librería FPDF 1.9).

## Estructura
```
index.php            Inicio de sesión
panel.php            Panel principal
pacientes.php        Gestión de pacientes
procedimientos.php   Catálogo de procedimientos
historia.php         Historia clínica del paciente (+ PDF)
inc/                 Cabecera y pie comunes (menú)
CRUD/                Un archivo PHP por operación y por entidad:
  pacientes/           listar, seleccionar, agregar, editar, eliminar, validar
  procedimientos/      listar, seleccionar, agregar, editar, eliminar, validar
  atenciones/          listar, seleccionar, agregar, editar, eliminar, validar
php/                 Backend común: conexion, auth (sesión y funciones compartidas),
                     login, logout, resumen (panel), historia_pdf (genera el PDF)
php/lib/fpdf/        Librería FPDF 1.9 (fpdf.php + carpeta font/)
assets/              CSS y JavaScript (AJAX)
database.sql         Tablas y datos iniciales
```

## Instalación local (XAMPP / Laragon)
1. Copie la carpeta en `htdocs` (XAMPP) o `www` (Laragon).
2. En phpMyAdmin cree una base de datos llamada `clinica_dental` (cotejamiento `utf8mb4_unicode_ci`).
3. Seleccione esa base de datos e importe `database.sql`.
4. Revise `php/conexion.php` (por defecto: root, sin contraseña).
5. Abra `http://localhost/clinica_dental/`.

**Acceso inicial:** `admin@clinica.com` / `Admin123`

## CRUD
Cada operación es un archivo independiente (`CRUD/<entidad>/<operación>.php`):

| Archivo | Método | Qué hace |
|---|---|---|
| `listar.php` | GET | Devuelve todos los registros (pacientes admite `q` para buscar) |
| `seleccionar.php` | GET | Devuelve un registro por `id` (para llenar el formulario de edición) |
| `agregar.php` | POST | Valida e inserta |
| `editar.php` | POST | Comprueba que exista, valida y actualiza |
| `eliminar.php` | POST | Elimina por `id` y verifica `affected_rows` |
| `validar.php` | - | Validaciones compartidas por `agregar.php` y `editar.php` |

Todas responden JSON `{estado: "exito" | "error", mensaje, ...}`. La carpeta debe llamarse
exactamente `CRUD` (en el hosting las mayúsculas importan).

## PDF con FPDF
El botón **Generar PDF** de la historia clínica abre `php/historia_pdf.php?id=<paciente>`, que:
1. Exige sesión iniciada y valida el id (`(int)`, mayor que 0).
2. Lee el paciente y sus procedimientos con consultas preparadas.
3. Convierte los textos de UTF-8 a Windows-1252 (necesario en FPDF para tildes y ñ).
4. Dibuja los datos del paciente y la tabla de procedimientos (altura de fila automática,
   encabezado repetido en cada página, total y numeración "Página X de Y").

FPDF no necesita extensiones especiales ni Composer. Ese archivo no debe imprimir nada antes
del PDF (ni espacios ni errores), o FPDF se detiene con "Some data has already been output".

## Validaciones
- **Navegador (HTML5):** campos requeridos, formato de correo, patrón de cédula, longitudes máximas.
- **Servidor (PHP):** se repiten todas las validaciones (campos vacíos, cédula, nombres, fecha real y no futura,
  correo con `FILTER_VALIDATE_EMAIL`, montos, longitudes, existencia de paciente y procedimiento).
  El servidor responde `{estado: "exito" | "error", mensaje}` y la pantalla siempre muestra ese mensaje.
- **Base de datos:** `UNIQUE` en cédula, correo y nombre de procedimiento; claves foráneas.
- **Seguridad:** consultas preparadas (`prepare` + `bind_param`), datos escapados al mostrarlos (anti XSS),
  páginas y scripts protegidos por sesión, acciones de escritura solo por POST.
