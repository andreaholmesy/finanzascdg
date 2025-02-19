<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/tcpdf/tcpdf.php';

// Arreglo de meses en español
$meses = [
    1 => 'Enero', '2' => 'Febrero', '3' => 'Marzo', '4' => 'Abril', '5' => 'Mayo', '6' => 'Junio',
    '7' => 'Julio', '8' => 'Agosto', '9' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
];

// Obtener mes y año
$mes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');
$mes = (int) $mes; // Convertir a número entero

// Consulta de transacciones
$stmt = $pdo->prepare("
    SELECT t.tipo, t.monto, t.metodo_pago, 
           IFNULL(m.nombre, 'Anónimo') AS nombre_miembro, 
           IFNULL(s.nombre, 'Anónimo') AS nombre_sembrador 
    FROM transacciones t
    LEFT JOIN miembros m ON t.cedula_miembro = m.cedula_miembro
    LEFT JOIN sembradores s ON t.id_sembrador = s.id_sembrador
    WHERE MONTH(t.fecha) = ? AND YEAR(t.fecha) = ?
");
$stmt->execute([$mes, $anio]);
$transacciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consulta de totales
$stmt = $pdo->prepare("
    SELECT 
        SUM(CASE WHEN tipo = 'diezmo' THEN monto ELSE 0 END) AS total_diezmos,
        SUM(CASE WHEN tipo = 'ofrenda' THEN monto ELSE 0 END) AS total_ofrendas,
        SUM(CASE WHEN tipo = 'semilla' THEN monto ELSE 0 END) AS total_semillas,
        SUM(CASE WHEN tipo = 'primicia' THEN monto ELSE 0 END) AS total_primicias
    FROM transacciones
    WHERE MONTH(fecha) = ? AND YEAR(fecha) = ?
");
$stmt->execute([$mes, $anio]);
$totales = $stmt->fetch(PDO::FETCH_ASSOC);

$total_general = array_sum($totales);

// Configurar PDF en tamaño Carta
$pdf = new TCPDF('P', 'mm', 'LETTER');
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->AddPage();
$pdf->setPrintFooter(false);

// Logo reducido (20 mm de ancho)
$logo = $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/assets/logo.jpg';
if (file_exists($logo)) {
    $pdf->Image($logo, 15, 5, 20, 0, 'JPG', '', 'T', false, 300);
}

// Ajuste de la posición Y para mover el encabezado más arriba
$pdf->SetY(15);

// Encabezado
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 6, 'MINISTERIO CASA DE GLORIA', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 6, 'Reporte Financiero Mensual', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);

// Mostrar el mes en español
$nombre_mes = $meses[$mes] ?? 'Mes desconocido';
$pdf->Cell(0, 6, strtoupper($nombre_mes) . ' ' . $anio, 0, 1, 'C');
$pdf->Ln(6);

// Contenido principal
if (!empty($transacciones)) {
    // Tabla de transacciones
    $pdf->SetFont('helvetica', '', 10);
    $header = ['Tipo', 'Monto (Bs)', 'Método', 'Contribuyente'];
    $w = [35, 35, 40, 85];

    // Cabecera de tabla
    $pdf->SetFillColor(225, 225, 225);
    foreach ($header as $key => $col) {
        $pdf->Cell($w[$key], 7, $col, 1, 0, 'C', true);
    }
    $pdf->Ln();

    // Filas de datos
    $fill = false;
    $pdf->SetFillColor(245, 245, 245);
    foreach ($transacciones as $row) {
        $metodo = str_replace('_', ' ', ucfirst($row['metodo_pago']));
        $nombre = ($row['nombre_miembro'] != 'Anónimo') 
                ? $row['nombre_miembro'] 
                : $row['nombre_sembrador'];

        $pdf->Cell($w[0], 8, ucfirst($row['tipo']), 'LR', 0, 'C', $fill);
        $pdf->Cell($w[1], 8, number_format($row['monto'], 2), 'LR', 0, 'C', $fill);
        $pdf->Cell($w[2], 8, $metodo, 'LR', 0, 'C', $fill);
        $pdf->Cell($w[3], 8, $nombre, 'LR', 1, 'L', $fill);
        $fill = !$fill;
    }
    $pdf->Cell(array_sum($w), 0, '', 'T');
    $pdf->Ln(8);

    // Totales
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(45, 8, 'RESUMEN DE TOTALES:', 0, 1);

    $detalles = [
        'Diezmos' => $totales['total_diezmos'],
        'Ofrendas' => $totales['total_ofrendas'],
        'Semillas' => $totales['total_semillas'],
        'Primicias' => $totales['total_primicias'],
        'TOTAL GENERAL' => $total_general
    ];

    foreach ($detalles as $label => $monto) {
        $pdf->Cell(50, 7, $label, 0, 0);
        $pdf->Cell(40, 7, number_format($monto, 2) . ' Bs', 0, 1, 'R');
    }

    // Control de espacio para el pie de página
    $current_page = $pdf->getPage();
    $space_left = $pdf->GetPageHeight() - $pdf->GetY() - 15;

    if ($space_left < 15 && $current_page == 1) {
        $pdf->AddPage();
    }
} else {
    $pdf->SetFont('helvetica', 'I', 14);
    $pdf->Cell(0, 10, 'No se encontraron transacciones', 0, 1, 'C');
}

// Pie de página
$pdf->SetY(250);
$pdf->SetFont('helvetica', 'I', 9);
$pdf->Cell(0, 4, 'Generado: ' . date('d/m/Y H:i'), 0, 1, 'C');
$pdf->Cell(0, 4, 'Sistema de Gestión Financiera - Ministerio Casa de Gloria', 0, 0, 'C');

// Salida final
$pdf->Output('reporte_financiero_' . $mes . '_' . $anio . '.pdf', 'I');
?>
