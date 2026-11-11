<?php
/**
 * Genera en PDF la historia clínica de un paciente usando la librería FPDF.
 * Uso: php/historia_pdf.php?id=<id del paciente>
 *
 * Importante: este archivo no debe imprimir nada antes del PDF
 * (ni espacios, ni saltos de línea, ni errores), o FPDF se detiene.
 */
include_once(__DIR__ . '/auth.php');

// Solo pueden descargar el PDF los usuarios con sesión iniciada
if (empty($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

include_once(__DIR__ . '/conexion.php');
require_once(__DIR__ . '/lib/fpdf/fpdf.php');

// Muestra un mensaje simple si algo falla (paciente inválido o inexistente)
function terminar($codigo, $mensaje) {
    http_response_code($codigo);
    header('Content-Type: text/plain; charset=utf-8');
    echo $mensaje;
    exit;
}

// FPDF trabaja con Windows-1252: se convierte el texto UTF-8 para que
// las tildes y la ñ se vean bien (sin esto "María" saldría "MarÃ­a")
function pdf_txt($s) {
    $r = @iconv('UTF-8', 'windows-1252//TRANSLIT', (string)$s);
    return ($r === false) ? mb_convert_encoding((string)$s, 'ISO-8859-1', 'UTF-8') : $r;
}

function pdf_dinero($n) {
    return '$' . number_format((float)$n, 2, '.', '');
}

function pdf_fecha($f) {
    $d = DateTime::createFromFormat('Y-m-d', $f);
    return $d ? $d->format('d/m/Y') : $f;
}

/**
 * Documento PDF: encabezado y pie de página + filas de tabla
 * con altura automática (el texto largo ocupa varias líneas).
 */
class HistoriaPDF extends FPDF
{
    public $cab = null;          // encabezado de tabla que se repite en cada página nueva
    protected $relleno = false;  // si la celda actual lleva color de fondo

    // Se ejecuta automáticamente al crear cada página
    function Header()
    {
        $this->SetFont('Helvetica', 'B', 16);
        $this->SetTextColor(15, 118, 110);
        $this->Cell(0, 8, pdf_txt('Historia Clínica Odontológica'), 0, 1, 'L');
        $this->SetFont('Helvetica', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 5, pdf_txt('Clínica Dental - Generado el ') . date('d/m/Y'), 0, 1, 'L');
        $this->SetDrawColor(15, 118, 110);
        $this->SetLineWidth(0.5);
        $this->Line(15, $this->GetY() + 1, 195, $this->GetY() + 1);
        $this->Ln(6);
    }

    // Número de página al pie ({nb} = total de páginas)
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Helvetica', '', 8);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 10, pdf_txt('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'C');
    }

    // Formato de celda: E = encabezado de tabla, L = etiqueta / total, N = normal
    protected function Estilo($f)
    {
        if ($f === 'E') {
            $this->SetFont('Helvetica', 'B', 9);
            $this->SetFillColor(15, 118, 110);
            $this->SetTextColor(255, 255, 255);
            $this->relleno = true;
        } elseif ($f === 'L') {
            $this->SetFont('Helvetica', 'B', 9);
            $this->SetFillColor(236, 245, 244);
            $this->SetTextColor(30, 30, 30);
            $this->relleno = true;
        } else {
            $this->SetFont('Helvetica', '', 9);
            $this->SetTextColor(30, 30, 30);
            $this->relleno = false;
        }
    }

    // Cantidad de líneas que ocupará un texto en una celda de ancho $w
    // (misma lógica que usa MultiCell internamente)
    protected function NbLines($w, $txt)
    {
        $cw = $this->CurrentFont['cw'];
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', (string)$txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }

    /**
     * Dibuja una fila de tabla.
     * $textos: textos de cada celda | $anchos: ancho en mm | $fmt: formato por celda | $alin: L, C o R
     */
    function Fila($textos, $anchos, $fmt, $alin = array())
    {
        $n = count($textos);

        // 1. Calcular la altura: la celda con más líneas manda
        $lineas = 1;
        for ($i = 0; $i < $n; $i++) {
            $this->Estilo($fmt[$i]);
            $lineas = max($lineas, $this->NbLines($anchos[$i], pdf_txt($textos[$i])));
        }
        $h = 5 * $lineas + 2;

        // 2. Si no cabe en la página, se pasa a la siguiente y se repite el encabezado
        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage();
            if ($this->cab !== null) {
                $this->Fila($this->cab[0], $this->cab[1], $this->cab[2]);
            }
        }

        // 3. Dibujar cada celda (recuadro + texto)
        $x = $this->GetX();
        $y = $this->GetY();
        for ($i = 0; $i < $n; $i++) {
            $this->Estilo($fmt[$i]);
            $a = isset($alin[$i]) ? $alin[$i] : 'L';
            $this->Rect($x, $y, $anchos[$i], $h, $this->relleno ? 'DF' : 'D');
            $this->SetXY($x, $y + 1);
            $this->MultiCell($anchos[$i], 5, pdf_txt($textos[$i]), 0, $a);
            $x += $anchos[$i];
        }
        $this->SetXY($this->lMargin, $y + $h);
    }
}

// ---------------------------------------------------------------------
// 1. Validar el id y leer los datos de la base de datos
// ---------------------------------------------------------------------
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    terminar(400, 'El paciente no es válido.');
}

$stmt = mysqli_prepare($conexion, "SELECT cedula, nombre, apellido, fecha_nacimiento, sexo, telefono, correo, direccion, antecedentes FROM pacientes WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$p = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$p) {
    terminar(404, 'No se encontró el paciente.');
}

$stmt = mysqli_prepare($conexion, "SELECT a.fecha, a.pieza_dental, a.diagnostico, a.observaciones, a.costo, pr.nombre AS procedimiento
    FROM atenciones a
    INNER JOIN procedimientos pr ON pr.id = a.procedimiento_id
    WHERE a.paciente_id = ?
    ORDER BY a.fecha ASC, a.id ASC");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$atenciones = filas($stmt);

// ---------------------------------------------------------------------
// 2. Construir el PDF
// ---------------------------------------------------------------------
$pdf = new HistoriaPDF('P', 'mm', 'A4');
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 20);
$pdf->AliasNbPages();
$pdf->SetTitle(pdf_txt('Historia clínica - ' . $p['nombre'] . ' ' . $p['apellido']));
$pdf->SetCreator(pdf_txt('Clínica Dental'));
$pdf->AddPage();
$pdf->SetDrawColor(180, 195, 195);
$pdf->SetLineWidth(0.2);

// Datos del paciente
$edad = (new DateTime($p['fecha_nacimiento']))->diff(new DateTime('today'))->y;
$sexo = ($p['sexo'] === 'M') ? 'Masculino' : 'Femenino';
$vacio = function ($v) { return $v !== '' ? $v : '-'; };

$a4 = array(32, 58, 24, 66);   // 4 columnas: etiqueta, dato, etiqueta, dato
$a2 = array(32, 148);          // 2 columnas: etiqueta y dato que ocupa el resto
$pdf->Fila(array('Paciente', $p['nombre'] . ' ' . $p['apellido'], 'Cédula', $p['cedula']), $a4, array('L', 'N', 'L', 'N'));
$pdf->Fila(array('Nacimiento', pdf_fecha($p['fecha_nacimiento']) . ' (' . $edad . ' años)', 'Sexo', $sexo), $a4, array('L', 'N', 'L', 'N'));
$pdf->Fila(array('Teléfono', $vacio($p['telefono']), 'Correo', $vacio($p['correo'])), $a4, array('L', 'N', 'L', 'N'));
$pdf->Fila(array('Dirección', $vacio($p['direccion'])), $a2, array('L', 'N'));
$pdf->Fila(array('Antecedentes médicos', $p['antecedentes'] !== '' ? $p['antecedentes'] : 'Ninguno registrado'), $a2, array('L', 'N'));

// Procedimientos realizados (del más antiguo al más reciente)
$pdf->Ln(8);
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->SetTextColor(15, 118, 110);
$pdf->Cell(0, 8, 'Procedimientos realizados', 0, 1, 'L');

if (count($atenciones) === 0) {
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 8, pdf_txt('Este paciente aún no tiene procedimientos registrados.'), 0, 1, 'L');
} else {
    $anchos = array(20, 34, 14, 44, 46, 22);   // suman 180 mm
    $titulos = array('Fecha', 'Procedimiento', 'Pieza', 'Diagnóstico', 'Observaciones', 'Costo');
    $fmtCab = array('E', 'E', 'E', 'E', 'E', 'E');

    $pdf->Fila($titulos, $anchos, $fmtCab);
    $pdf->cab = array($titulos, $anchos, $fmtCab);   // se repite si la tabla pasa a otra página

    $total = 0;
    foreach ($atenciones as $a) {
        $total += (float)$a['costo'];
        $pdf->Fila(
            array(pdf_fecha($a['fecha']), $a['procedimiento'], $vacio($a['pieza_dental']), $vacio($a['diagnostico']), $vacio($a['observaciones']), pdf_dinero($a['costo'])),
            $anchos,
            array('N', 'N', 'N', 'N', 'N', 'N'),
            array('L', 'L', 'L', 'L', 'L', 'R')
        );
    }
    $pdf->cab = null;
    $pdf->Fila(array('Total', pdf_dinero($total)), array(158, 22), array('L', 'L'), array('R', 'R'));
}

// ---------------------------------------------------------------------
// 3. Enviar el PDF al navegador (se abre en una pestaña nueva)
// ---------------------------------------------------------------------
$pdf->Output('I', 'historia_clinica_' . $p['cedula'] . '.pdf');
