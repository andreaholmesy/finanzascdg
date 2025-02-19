<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/db.php';
include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/user_auth.php';

// Obtener mes, año y tipo de transacción seleccionados, o por defecto el mes y año actuales
$mesSeleccionado = $_GET['mes'] ?? date('m');
$anoSeleccionado = $_GET['ano'] ?? date('Y');
$tipoSeleccionado = $_GET['tipo'] ?? '';

// Límite y offset para la paginación
$transaccionesPorPagina = 10;
$offset = ($_GET['pagina'] ?? 1) - 1;
$offset *= $transaccionesPorPagina;

// Obtener meses y años disponibles
$stmt = $pdo->query("SELECT DISTINCT YEAR(fecha) AS ano FROM transacciones ORDER BY ano DESC");
$fechasDisponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener tipos de transacción disponibles
$tiposTransaccion = ['diezmo', 'ofrenda', 'primicia', 'semilla'];

// Construcción de la consulta SQL
$sql = "SELECT t.id_transaccion, t.tipo, t.monto, t.moneda, t.metodo_pago, t.es_anonimo, t.fecha, 
               m.cedula_miembro, m.nombre AS nombre_miembro, m.apellido AS apellido_miembro, 
               s.nombre AS nombre_sembrador, s.apellido AS apellido_sembrador
        FROM transacciones t
        LEFT JOIN miembros m ON t.cedula_miembro = m.cedula_miembro
        LEFT JOIN sembradores s ON t.id_sembrador = s.id_sembrador
        WHERE YEAR(t.fecha) = :anoSeleccionado AND MONTH(t.fecha) = :mesSeleccionado";

if ($tipoSeleccionado) {
    $sql .= " AND t.tipo = :tipoSeleccionado";
}

$sql .= " ORDER BY t.fecha DESC LIMIT :limite OFFSET :offset";

// Preparar la consulta
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':anoSeleccionado', $anoSeleccionado, PDO::PARAM_INT);
$stmt->bindParam(':mesSeleccionado', $mesSeleccionado, PDO::PARAM_INT);
$stmt->bindParam(':limite', $transaccionesPorPagina, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

if ($tipoSeleccionado) {
    $stmt->bindParam(':tipoSeleccionado', $tipoSeleccionado, PDO::PARAM_STR);
}

// Ejecutar la consulta
$stmt->execute();
$transacciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Transacciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background-color: #007bff;
            color: white;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .badge {
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/header.php'; ?>

    <div class="container mt-4">
        <h2 class="text-center mb-4"><i class="bi bi-list-columns-reverse"></i> Historial de Diezmos, Ofrendas, Primicias, Semillas</h2>

        <!-- FILTRO DE MES, AÑO Y TIPO DE TRANSACCIÓN -->
        <form method="GET" class="mb-4 row g-2 justify-content-center">
            <div class="col-auto">
                <label for="mes" class="form-label">Mes:</label>
                <select name="mes" id="mes" class="form-select">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= sprintf("%02d", $m) ?>" <?= ($m == $mesSeleccionado) ? 'selected' : '' ?>>
                            <?= date("F", mktime(0, 0, 0, $m, 1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-auto">
                <label for="ano" class="form-label">Año:</label>
                <select name="ano" id="ano" class="form-select">
                    <?php foreach ($fechasDisponibles as $fecha): ?>
                        <option value="<?= $fecha['ano'] ?>" <?= ($fecha['ano'] == $anoSeleccionado) ? 'selected' : '' ?>>
                            <?= $fecha['ano'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <label for="tipo" class="form-label">Tipo de Transacción:</label>
                <select name="tipo" id="tipo" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach ($tiposTransaccion as $tipo): ?>
                        <option value="<?= $tipo ?>" <?= ($tipo == $tipoSeleccionado) ? 'selected' : '' ?>>
                            <?= ucfirst($tipo) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </form>

        <!-- TABLA DE TRANSACCIONES -->
        <?php if (empty($transacciones)): ?>
            <div class="alert alert-warning text-center fw-bold">
                No hay transacciones registradas para este mes, año y tipo seleccionados.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Monto</th>
                            <th>Moneda</th>
                            <th>Método de Pago</th>
                            <th>Fecha</th>
                            <th>Cédula</th>
                            <th>Nombre y Apellido</th>
                            <th>Anónimo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transacciones as $t): ?>
                            <tr>
                                <td><?= htmlspecialchars($t['id_transaccion']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($t['tipo']) ?></span></td>
                                <td><strong><?= number_format($t['monto'], 2, ',', '.') ?></strong></td>
                                <td><?= htmlspecialchars($t['moneda']) ?></td>
                                <td>
                                    <?php
                                    $metodos = [
                                        "efectivo" => "success",
                                        "pago_movil" => "warning",
                                        "transferencia_bancaria" => "primary"
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $metodos[$t['metodo_pago']] ?>">
                                        <?= ucfirst(str_replace("_", " ", $t['metodo_pago'])) ?>
                                    </span>
                                </td>
                                <td><?= date("d/m/Y", strtotime($t['fecha'])) ?></td>
                                <td>
                                    <?= $t['es_anonimo'] ? '<span class="text-muted">---</span>' : htmlspecialchars($t['cedula_miembro'] ?? '') ?>
                                </td>
                                <td>
                                    <?php 
                                        if ($t['es_anonimo']) {
                                            echo '<span class="text-muted">Anónimo</span>';
                                        } else {
                                            $nombre = $t['nombre_miembro'] ?? $t['nombre_sembrador'] ?? 'Desconocido';
                                            $apellido = $t['apellido_miembro'] ?? $t['apellido_sembrador'] ?? '';
                                            echo htmlspecialchars($nombre . ' ' . $apellido);
                                        }
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $t['es_anonimo'] ? 'secondary' : 'success' ?>">
                                        <?= $t['es_anonimo'] ? 'Sí' : 'No' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/footer.php'; ?>
</body>
</html>
