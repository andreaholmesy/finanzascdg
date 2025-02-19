<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/db.php';

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: /finanzascdg/login.php');
    exit();
}

// Obtener filtro de búsqueda
$search = $_GET['search'] ?? '';

// Consulta usando PDO con búsqueda
$sql = "SELECT cedula_miembro, nombre, apellido, rol, red FROM miembros 
        WHERE nombre LIKE :search OR apellido LIKE :search OR rol LIKE :search";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->execute();
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/header.php';
?>

<!-- Estilos y Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<div class="container mt-4">
    <!-- Botón Volver al Inicio -->
    <a href="/finanzascdg/index.php" class="btn btn-primary mb-3">
        <i class="fas fa-arrow-left"></i> Volver al Inicio
    </a>

    <!-- Sección Miembros Actuales -->
    <div class="card shadow-sm border-0">
        <div class="card-header text-center fw-bold" style="background-color:rgb(9, 49, 180); color: white; border-radius: 10px 10px 0 0;">
            <h3 class="mb-0"><i class="fas fa-users"></i> Miembros Actuales</h3>
        </div>
        <div class="card-body">
            <!-- Filtro de búsqueda con ícono -->
            <form method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, apellido o rol" value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-light">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>

            <!-- Mensaje si no hay miembros -->
            <?php if (empty($resultado)): ?>
                <div class="alert alert-light text-center" role="alert">
                    <i class="fas fa-info-circle"></i> No hay miembros registrados en este momento.
                </div>
            <?php else: ?>
                <!-- Tabla de miembros -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-light">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-id-card"></i> Cédula</th>
                                <th><i class="fas fa-user"></i> Nombre</th>
                                <th><i class="fas fa-user"></i> Apellido</th>
                                <th><i class="fas fa-briefcase"></i> Rol</th>
                                <th><i class="fas fa-network-wired"></i> Red</th>
                                <th><i class="fas fa-cogs"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultado as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['cedula_miembro']) ?></td>
                                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                                    <td><?= htmlspecialchars($row['apellido']) ?></td>
                                    <td><span class="badge bg-light text-dark"><?= ucfirst(htmlspecialchars($row['rol'])) ?></span></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['red']) ?></span></td>
                                    <td>
                                        <a href="editar.php?cedula=<?= urlencode($row['cedula_miembro']) ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="eliminar.php?cedula=<?= urlencode($row['cedula_miembro']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este miembro?');">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/finanzascdg/includes/footer.php'; ?>
