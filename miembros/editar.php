<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/db.php';

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: /finanzascdg/login.php');
    exit();
}

// Verificar si se recibió la cédula
if (!isset($_GET['cedula'])) {
    header('Location: listado.php'); // Redirige si no hay cédula
    exit();
}

$cedula = $_GET['cedula'];

// Obtener datos del miembro
$sql = "SELECT * FROM miembros WHERE cedula_miembro = :cedula";
$stmt = $pdo->prepare($sql);
$stmt->execute(['cedula' => $cedula]);
$miembro = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si el miembro existe
if (!$miembro) {
    echo "<script>alert('Miembro no encontrado.'); window.location='listado.php';</script>";
    exit();
}

// Procesar formulario de actualización
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $red = $_POST['red'];
    $rol = $_POST['rol'];

    $sql = "UPDATE miembros SET nombre = :nombre, apellido = :apellido, direccion = :direccion, telefono = :telefono, red = :red, rol = :rol WHERE cedula_miembro = :cedula";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'nombre' => $nombre,
        'apellido' => $apellido,
        'direccion' => $direccion,
        'telefono' => $telefono,
        'red' => $red,
        'rol' => $rol,
        'cedula' => $cedula
    ]);

    echo "<script>alert('Datos actualizados correctamente'); window.location='listado.php';</script>";
    exit();
}

include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/header.php';
?>

<!-- Cargar Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
    <a href="listado.php" class="btn btn-primary mb-3"><i class="fas fa-arrow-left"></i> Volver al Listado</a>

    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0"><i class="fas fa-edit"></i> Editar Miembro</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre:</label>
                        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($miembro['nombre']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido:</label>
                        <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($miembro['apellido']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dirección:</label>
                        <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($miembro['direccion']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono:</label>
                        <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($miembro['telefono']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Red:</label>
                        <select name="red" class="form-control">
                            <option value="">Seleccione una red</option>
                            <option value="David" <?= $miembro['red'] == 'David' ? 'selected' : '' ?>>David</option>
                            <option value="Josué" <?= $miembro['red'] == 'Josué' ? 'selected' : '' ?>>Josué</option>
                            <option value="Daniel" <?= $miembro['red'] == 'Daniel' ? 'selected' : '' ?>>Daniel</option>
                            <option value="Gedeón" <?= $miembro['red'] == 'Gedeón' ? 'selected' : '' ?>>Gedeón</option>
                            <option value="Caleb" <?= $miembro['red'] == 'Caleb' ? 'selected' : '' ?>>Caleb</option>
                            <option value="Timoteo" <?= $miembro['red'] == 'Timoteo' ? 'selected' : '' ?>>Timoteo</option>
                            <option value="Pablo" <?= $miembro['red'] == 'Pablo' ? 'selected' : '' ?>>Pablo</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Rol:</label>
                        <select name="rol" class="form-control" required>
                            <option value="Mentor" <?= $miembro['rol'] == 'Mentor' ? 'selected' : '' ?>>Mentor</option>
                            <option value="Diácono" <?= $miembro['rol'] == 'Diácono' ? 'selected' : '' ?>>Diácono</option>
                            <option value="Líder" <?= $miembro['rol'] == 'Líder' ? 'selected' : '' ?>>Líder</option>
                            <option value="Discípulo" <?= $miembro['rol'] == 'Discípulo' ? 'selected' : '' ?>>Discípulo</option>
                            <option value="Pueblo" <?= $miembro['rol'] == 'Pueblo' ? 'selected' : '' ?>>Pueblo</option>
                        </select>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar Cambios</button>
                    <a href="listado.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/footer.php'; ?>
