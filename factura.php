<?php
// Limpiar cualquier búfer o espacio en blanco previo
if (ob_get_length()) ob_clean();

// Incluir FPDF
require('fpdf.php');

// Recibir el JSON enviado desde el frontend
$jsonRecibido = file_get_contents('php://input');
$datos = json_decode($jsonRecibido, true) ?? [];

// Formatear fecha y mes
$fecha_pago = !empty($datos['fecha']) ? $datos['fecha'] : date('Y-m-d');
$timestamp = strtotime($fecha_pago);
$anio = date('Y', $timestamp);

$mes_nombre = !empty($datos['meses']) ? $datos['meses'] : date('F', $timestamp);
$mes_nombre = strtoupper(iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $mes_nombre));

class FacturaPDF extends FPDF {
    function Footer() {
        $this->SetY(-15);
        $this->SetFillColor(181, 211, 107); // Verde #B5D36B
        $this->Rect(0, 282, 210, 15, 'F');
    }
}

$pdf = new FacturaPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);

// Logo
if (file_exists('iconos/logoFactura.png')) {
    $pdf->Image('iconos/logoFactura.png', 12, 25, 75);
}

// Línea azul separadora vertical
$pdf->SetDrawColor(0, 51, 102);
$pdf->SetLineWidth(0.5);
$pdf->Line(96, 20, 96, 68);

// Título y Datos del Usuario
$pdf->SetXY(102, 22);
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(0, 80, 158);
$pdf->Cell(0, 8, "FACTURA " . $mes_nombre . " " . $anio, 0, 1, 'L');

$pdf->Ln(4);
$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(30, 30, 30);

// Nombre
$pdf->SetX(102);
$pdf->Cell(0, 6, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $datos['nombre_completo'] ?? ''), 0, 1, 'L');

// DNI
$pdf->SetX(102);
$pdf->Cell(0, 6, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "DNI: " . ($datos['DNI'] ?? '')), 0, 1, 'L');

// Mail
$pdf->SetX(102);
$pdf->Cell(0, 6, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "Mail: " . ($datos['email'] ?? '')), 0, 1, 'L');

// Tabla de detalles
$pdf->Ln(25);
$pdf->SetX(18);

// Cabecera Verde
$pdf->SetFillColor(181, 211, 107);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetDrawColor(181, 211, 107);

$pdf->Cell(58, 11, 'ESTADO', 1, 0, 'C', true);
$pdf->Cell(58, 11, 'FECHA', 1, 0, 'C', true);
$pdf->Cell(58, 11, 'MONTO', 1, 1, 'C', true);

// Filas de datos
$pdf->SetX(18);
$pdf->SetFont('Arial', '', 10);
$pdf->SetDrawColor(181, 211, 107);
$pdf->SetLineWidth(0.3);

$estadoText = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $datos['estadopago'] ?? 'Impago');
$fechaText = !empty($datos['fecha']) ? date('d/m/Y', strtotime($datos['fecha'])) : '-';
$montoText = "$" . ($datos['monto'] ?? '0');

$pdf->Cell(58, 12, $estadoText, 1, 0, 'C');
$pdf->Cell(58, 12, $fechaText, 1, 0, 'C');
$pdf->Cell(58, 12, $montoText, 1, 1, 'C');

// Output directo
header('Content-Type: application/pdf');
$pdf->Output('I', 'Factura.pdf');
exit;