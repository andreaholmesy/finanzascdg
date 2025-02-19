<?php
session_start();
include 'includes/db.php'; // Incluir la conexión PDO
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Obtener nombre del usuario
$usuario = $_SESSION['usuario'];

include 'includes/header.php';
?>

<!-- Cargar Bootstrap y Font Awesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<style>
    /* Estilos de la barra lateral */
    .sidebar {
        width: 250px;
        height: 100vh;
        background-color: #343a40;
        color: white;
        position: fixed;
        top: 0;
        left: 0;
        padding-top: 20px;
    }

    .sidebar a {
        color: white;
        text-decoration: none;
        padding: 10px;
        display: block;
        transition: 0.3s;
    }

    .sidebar a:hover {
        background-color: #495057;
    }

    /* Estilos del contenido */
    .content {
        margin-left: 250px;
        padding: 20px;
    }

    /* Estilos de las tarjetas */
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* Mejor espaciado y alineación */
    .container {
        max-width: 100%;
        padding: 0 15px;
    }

    /* Estilo del logo en la esquina */
    img[src*="logo"] {
        position: absolute;
        top: 10px;
        right: 20px;
        max-width: 100px;
    }

    /* Responsividad */
    @media (max-width: 768px) {
        .sidebar {
            width: 200px;
        }

        .content {
            margin-left: 210px;
        }

        .container {
            padding: 0;
        }
    }
</style>

<div class="d-flex">
    <!-- Barra lateral -->
    <div class="sidebar">
        <h4 class="text-center">Menú</h4>
        <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="miembros/registrar.php"><i class="fas fa-user-plus"></i> Registrar Miembro</a>
        <a href="miembros/listado.php"><i class="fas fa-users"></i> Ver Miembros</a>
        <a href="transacciones/registrar.php"><i class="fas fa-hand-holding-usd"></i> Registrar Contribución</a>
        <a href="transacciones/historial.php"><i class="fas fa-history"></i> Historial de Contribuciones</a>
        <a href="informes/generar.php"><i class="fas fa-chart-line"></i> Generar Informe</a>
        <a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <!-- Logo y Bienvenida -->
        <div class="text-center mb-4">
            <img src="assets/logo.png" alt="Logo Iglesia" class="img-fluid mb-3" style="max-width: 110px;">
            <h2 class="text-success">Bienvenido, <strong><?= $usuario ?></strong></h2>
            <p class="text-muted">Hoy es <?= date('d/m/Y') ?></p>
        </div>

        <!-- Tarjetas de acceso rápido -->
        <div class="row justify-content-center">
            <div class="col-md-4 mb-4">
                <a href="miembros/registrar.php" class="card shadow-lg text-decoration-none">
                    <div class="card-body text-center">
                        <i class="fas fa-user-plus fa-4x mb-3"></i>
                        <h5>Registrar Miembro</h5>
                    </div>
                </a>
            </div>

            <div class="col-md-4 mb-4">
                <a href="transacciones/registrar.php" class="card shadow-lg text-decoration-none">
                    <div class="card-body text-center">
                        <i class="fas fa-hand-holding-usd fa-4x mb-3"></i>
                        <h5>Registrar Contribución</h5>
                    </div>
                </a>
            </div>

            <div class="col-md-4 mb-4">
                <a href="transacciones/historial.php" class="card shadow-lg text-decoration-none">
                    <div class="card-body text-center">
                        <i class="fas fa-history fa-4x mb-3"></i>
                        <h5>Historial de Contribuciones</h5>
                    </div>
                </a>
            </div>

            <div class="col-md-4 mb-4">
                <a href="informes/generar.php" class="card shadow-lg text-decoration-none">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line fa-4x mb-3"></i>
                        <h5>Generar Informe</h5>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
