<?php 
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/db.php';
include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/user_auth.php';

$mensaje_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';
    $monto = $_POST['monto'] ?? '';
    $moneda = $_POST['moneda'] ?? '';
    $metodo_pago = $_POST['metodo_pago'] ?? '';
    $es_anonimo = isset($_POST['es_anonimo']) ? 1 : 0;
    
    if (empty($tipo) || empty($monto) || empty($moneda) || empty($metodo_pago)) {
        $mensaje_error = "Todos los campos son obligatorios.";
    } else {
        if ($es_anonimo) {
            $stmt = $pdo->prepare("
                INSERT INTO transacciones (tipo, monto, moneda, metodo_pago, es_anonimo, fecha)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$tipo, $monto, $moneda, $metodo_pago, $es_anonimo]);
            header('Location: ../index.php');
            exit();
        } else {
            $es_miembro = $_POST['es_miembro'] ?? '';

            if ($es_miembro == 'si' && !empty($_POST['cedula_miembro'])) {
                $cedula_miembro = $_POST['cedula_miembro'];
                $stmt = $pdo->prepare("SELECT cedula_miembro FROM miembros WHERE cedula_miembro = ?");
                $stmt->execute([$cedula_miembro]);
                $miembro = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$miembro) {
                    $mensaje_error = "La cédula no corresponde a un miembro registrado.";
                } else {
                    $stmt = $pdo->prepare("
                        SELECT COUNT(*) FROM transacciones 
                        WHERE cedula_miembro = ? AND fecha > NOW() - INTERVAL 1 HOUR
                    ");
                    $stmt->execute([$cedula_miembro]);
                    $contribuciones_recientes = $stmt->fetchColumn();

                    if ($contribuciones_recientes > 0) {
                        $mensaje_error = "Este miembro ya ha realizado una contribución recientemente.";
                    } else {
                        $stmt = $pdo->prepare("
                            INSERT INTO transacciones (tipo, monto, moneda, metodo_pago, cedula_miembro, es_anonimo, fecha)
                            VALUES (?, ?, ?, ?, ?, ?, NOW())
                        ");
                        $stmt->execute([$tipo, $monto, $moneda, $metodo_pago, $cedula_miembro, $es_anonimo]);
                        header('Location: ../index.php');
                        exit();
                    }
                }
            } elseif ($es_miembro == 'no' && !empty($_POST['nombre_sembrador'])) {
                $nombre_sembrador = $_POST['nombre_sembrador'];
                $stmt = $pdo->prepare("SELECT id_sembrador FROM sembradores WHERE nombre = ?");
                $stmt->execute([$nombre_sembrador]);
                $sembrador = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$sembrador) {
                    $stmt = $pdo->prepare("INSERT INTO sembradores (nombre) VALUES (?)");
                    $stmt->execute([$nombre_sembrador]);
                    $id_sembrador = $pdo->lastInsertId();
                } else {
                    $id_sembrador = $sembrador['id_sembrador'];
                }

                $stmt = $pdo->prepare("
                    INSERT INTO transacciones (tipo, monto, moneda, metodo_pago, id_sembrador, es_anonimo, fecha)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$tipo, $monto, $moneda, $metodo_pago, $id_sembrador, $es_anonimo]);
                header('Location: ../index.php');
                exit();
            } else {
                $mensaje_error = "Debe ingresar la cédula del miembro o el nombre del sembrador.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Contribución</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f9fafb;
            font-family: 'Arial', sans-serif;
            padding-top: 50px;
        }
        .container {
            background-color: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        h4 {
            color: #28a745;
            font-weight: bold;
            text-align: center;
        }
        .form-select, .form-control {
            border-radius: 10px;
            border: 1px solid #d1d8e2;
            font-size: 16px;
            padding: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .form-select:focus, .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            border-radius: 10px;
            padding: 12px 30px;
            font-size: 18px;
            width: 100%;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .alert-danger {
            border-radius: 8px;
            font-size: 16px;
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
        }
        .form-check-label {
            font-size: 16px;
        }
        .form-check-input {
            transform: scale(1.2);
        }
        #datos_persona {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/header.php'; ?>
    
    <div class="container">
        <h4>Registrar Nueva Contribución</h4>

        <?php if ($mensaje_error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($mensaje_error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Tipo:</label>
                <select name="tipo" class="form-select" required>
                    <option value="diezmo">Diezmo</option>
                    <option value="ofrenda">Ofrenda</option>
                    <option value="semilla">Semilla</option>
                    <option value="primicia">Primicia</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Monto:</label>
                <input type="number" name="monto" step="0.01" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Moneda:</label>
                <select name="moneda" class="form-select" required>
                    <option value="bolívares">Bolívares</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Método de Pago:</label>
                <select name="metodo_pago" class="form-select" required>
                    <option value="efectivo">Efectivo</option>
                    <option value="pago_movil">Pago Móvil</option>
                    <option value="transferencia_bancaria">Transferencia Bancaria</option>
                </select>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" name="es_anonimo" id="es_anonimo" class="form-check-input">
                <label class="form-check-label">Contribución Anónima</label>
            </div>

            <div id="datos_persona">
                <div class="mb-3">
                    <label>¿Es miembro?</label><br>
                    <input type="radio" name="es_miembro" value="si" required> Sí
                    <input type="radio" name="es_miembro" value="no"> No
                </div>

                <div class="mb-3" id="cedula_miembro" style="display:none;">
                    <label>Cédula del Miembro:</label>
                    <input type="text" name="cedula_miembro" class="form-control">
                </div>

                <div class="mb-3" id="nombre_sembrador" style="display:none;">
                    <label>Nombre del Sembrador:</label>
                    <input type="text" name="nombre_sembrador" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-success">Guardar Contribución</button>
            <button type="button" class="btn btn-secondary mt-3" onclick="window.location.href='../index.php';">Volver al Inicio</button>
        </form>
    </div>
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/footer.php'; ?>

    <script>
        document.getElementById('es_anonimo').addEventListener('change', function() {
            let datosPersona = document.getElementById('datos_persona');
            if (this.checked) {
                datosPersona.style.display = 'none';
            } else {
                datosPersona.style.display = 'block';
            }
        });

        document.querySelectorAll('input[name="es_miembro"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                if (this.value === 'si') {
                    document.getElementById('cedula_miembro').style.display = 'block';
                    document.getElementById('nombre_sembrador').style.display = 'none';
                } else if (this.value === 'no') {
                    document.getElementById('cedula_miembro').style.display = 'none';
                    document.getElementById('nombre_sembrador').style.display = 'block';
                }
            });
        });
    </script>
</body>
</html>
