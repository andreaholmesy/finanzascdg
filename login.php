<?php
session_start();
include 'includes/db.php';

$user = null; // Inicializar la variable para evitar "Undefined variable"

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE nombre_usuario = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['contrasena_hash'])) {
        $_SESSION['usuario'] = $username;
        header('Location: index.php');
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }

    // Depuración: Verificar datos
    var_dump($password); // Muestra la contraseña ingresada
    var_dump($user['contrasena_hash'] ?? 'No encontrado'); // Si no hay usuario, muestra "No encontrado"
    var_dump($user ? password_verify($password, $user['contrasena_hash']) : 'No verificado'); // Verifica si el password es correcto
    exit();
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 400px;">
        <div class="card shadow">
            <div class="card-body">
                <h2 class="text-center mb-4">Acceso al Sistema</h2>
                <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <input type="text" name="username" class="form-control mb-3" placeholder="Usuario" required>
                    <input type="password" name="password" class="form-control mb-3" placeholder="Contraseña" required>
                    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>