<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/db.php';

// Verificar sesión activa
if (!isset($_SESSION['usuario'])) {
    header('Location: /finanzascdg/login.php');
    exit();
}

// Variables para mensajes
$mensaje = "";
$mensaje_tipo = "";

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = trim($_POST['cedula']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);
    $red = trim($_POST['red']);
    $rol = trim($_POST['rol']);

    // Validar campos obligatorios
    if (empty($cedula) || empty($nombre) || empty($apellido) || empty($telefono) || empty($rol) || empty($red)) {
        $mensaje = "Por favor, completa todos los campos obligatorios.";
        $mensaje_tipo = "danger";
    } else {
        // Insertar en la base de datos
        $sql = "INSERT INTO miembros (cedula_miembro, nombre, apellido, direccion, telefono, red, rol) 
                VALUES (:cedula, :nombre, :apellido, :direccion, :telefono, :red, :rol)";
        $stmt = $pdo->prepare($sql);
        $ejecutado = $stmt->execute([
            ':cedula' => $cedula,
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':direccion' => $direccion,
            ':telefono' => $telefono,
            ':red' => $red,
            ':rol' => $rol
        ]);

        if ($ejecutado) {
            $mensaje = "Miembro registrado correctamente.";
            $mensaje_tipo = "success";
        } else {
            $mensaje = "Error al registrar el miembro.";
            $mensaje_tipo = "danger";
        }
    }
}

include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/header.php';
?>

<!-- Cargar Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary"><i class="fas fa-user-plus"></i> Registrar Miembro</h2>
        <a href="/finanzascdg/index.php" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left"></i> Volver al Inicio
        </a>
    </div>

    <div class="card border-0 shadow-lg p-4" style="border-radius: 15px;">
        <!-- Mostrar mensaje -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?= $mensaje_tipo ?> text-center" role="alert">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="cedula" class="form-label fw-bold">Cédula*</label>
                    <input type="text" name="cedula" class="form-control form-control-lg shadow-sm" required>
                </div>
                <div class="col-md-6">
                    <label for="nombre" class="form-label fw-bold">Nombre*</label>
                    <input type="text" name="nombre" class="form-control form-control-lg shadow-sm" required>
                </div>
                <div class="col-md-6">
                    <label for="apellido" class="form-label fw-bold">Apellido*</label>
                    <input type="text" name="apellido" class="form-control form-control-lg shadow-sm" required>
                </div>
                <div class="col-md-6">
                    <label for="direccion" class="form-label fw-bold">Dirección</label>
                    <input type="text" name="direccion" class="form-control form-control-lg shadow-sm">
                </div>
                <div class="col-md-6">
                    <label for="telefono" class="form-label fw-bold">Teléfono*</label>
                    <input type="text" name="telefono" class="form-control form-control-lg shadow-sm" required>
                </div>
                <div class="col-md-6">
                    <label for="red" class="form-label fw-bold">Red*</label>
                    <select name="red" class="form-select form-select-lg shadow-sm" required>
                        <option value="">Seleccione una red</option>
                        <option value="David">David</option>
                        <option value="Josué">Josué</option>
                        <option value="Daniel">Daniel</option>
                        <option value="Gedeón">Gedeón</option>
                        <option value="Caleb">Caleb</option>
                        <option value="Timoteo">Timoteo</option>
                        <option value="Pablo">Pablo</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="rol" class="form-label fw-bold">Rol*</label>
                    <select name="rol" class="form-select form-select-lg shadow-sm" required>
                        <option value="">Seleccione un rol</option>
                        <option value="Mentor">Mentor</option>
                        <option value="Diácono">Diácono</option>
                        <option value="Líder">Líder</option>
                        <option value="Discípulo">Discípulo</option>
                        <option value="Pueblo">Pueblo</option>
                    </select>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success btn-lg px-5 shadow">
                    <i class="fas fa-save"></i> Registrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Estilos personalizados -->
<style>
    .form-control, .form-select {
        border-radius: 10px;
        border: 2px solid #dee2e6;
    }

    .form-control:focus, .form-select:focus {
        border-color: #28a745;
        box-shadow: 0px 0px 8px rgba(40, 167, 69, 0.5);
    }

    .btn-lg {
        font-size: 18px;
        padding: 12px 20px;
    }

    .card {
        background: #ffffff;
        border-radius: 15px;
        transition: transform 0.2s ease-in-out;
    }

    .card:hover {
        transform: scale(1.02);
    }

    .shadow-sm {
        box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
    }

    .shadow-lg {
        box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.15);
    }
</style>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/footer.php'; ?>
