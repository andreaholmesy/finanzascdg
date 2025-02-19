<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/db.php';
include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/user_auth.php';

// Obtener los años disponibles en la base de datos
$stmt = $pdo->query("SELECT DISTINCT YEAR(fecha) AS anio FROM transacciones ORDER BY anio DESC");
$anios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener el mes y año seleccionados o los valores por defecto
$mes = $_GET['mes'] ?? date('m');
$anio = $_GET['anio'] ?? date('Y');

// Mapeo de números a nombres de meses
$nombres_meses = [
    '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
    '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
    '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
];

// Consulta para obtener el resumen del informe del mes seleccionado
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

// Consulta para obtener las transacciones del mes seleccionado
$stmt = $pdo->prepare("
    SELECT t.*, 
        m.nombre AS nombre_miembro, 
        m.apellido AS apellido_miembro, 
        s.nombre AS nombre_sembrador
    FROM transacciones t
    LEFT JOIN miembros m ON t.cedula_miembro = m.cedula_miembro
    LEFT JOIN sembradores s ON t.id_sembrador = s.id_sembrador
    WHERE MONTH(t.fecha) = ? AND YEAR(t.fecha) = ?
    ORDER BY t.fecha DESC
");
$stmt->execute([$mes, $anio]);
$transacciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Informe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
        }
        .card-header {
            background-color: #343a40;
            color: white;
        }
        .card-body {
            background-color: #fff;
        }
        .table thead {
            background-color: #343a40;
            color: white;
        }
        .btn-success {
            background-color: #28a745;
            border: none;
        }
        .btn-success:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/header.php'; ?>

    <div class="container mt-4">
        <h2 class="text-center mb-4">INFORMES MENSUALES</h2>

        <!-- Filtros por mes y año -->
        <form method="GET" class="row g-3 align-items-center mb-4">
            <div class="col-md-4">
                <label class="form-label fw-bold">Mes:</label>
                <select name="mes" class="form-select">
                    <?php foreach ($nombres_meses as $num => $nombre): ?>
                        <option value="<?= $num ?>" <?= ($mes == $num) ? 'selected' : '' ?>><?= $nombre ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Año:</label>
                <select name="anio" class="form-select">
                    <?php foreach ($anios as $row): ?>
                        <option value="<?= $row['anio'] ?>" <?= ($anio == $row['anio']) ? 'selected' : '' ?>><?= $row['anio'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>

        <!-- Tarjetas de Resumen -->
        <div class="row text-center mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">Diezmos</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= number_format($totales['total_diezmos'], 2) ?> Bs</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">Ofrendas</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= number_format($totales['total_ofrendas'], 2) ?> Bs</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">Semillas</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= number_format($totales['total_semillas'], 2) ?> Bs</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">Primicias</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= number_format($totales['total_primicias'], 2) ?> Bs</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Transacciones -->
        <h4 class="mt-4">Transacciones del Mes</h4>
        <?php if (!empty($transacciones)): ?>
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Monto (Bs)</th>
                        <th>Método de Pago</th>
                        <th>Contribuyente</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transacciones as $transaccion): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($transaccion['fecha'])) ?></td>
                        <td><?= ucfirst($transaccion['tipo']) ?></td>
                        <td><?= number_format($transaccion['monto'], 2) ?></td>
                        <td><?= ucfirst(str_replace('_', ' ', $transaccion['metodo_pago'])) ?></td>
                        <td>
                            <?= $transaccion['es_anonimo'] ? 'Anónimo' : ($transaccion['nombre_miembro'] ? $transaccion['nombre_miembro'] . ' ' . $transaccion['apellido_miembro'] : $transaccion['nombre_sembrador']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No hay transacciones registradas para este mes.</div>
        <?php endif; ?>

        <a href="generar_pdf.php?mes=<?= $mes ?>&anio=<?= $anio ?>" class="btn btn-success mt-3">Generar PDF</a>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/footer.php'; ?>
</body>
</html>
