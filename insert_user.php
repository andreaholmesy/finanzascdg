<?php
include 'includes/db.php'; // Asegúrate de que este archivo contiene la conexión a la BD

$password = 'segura';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE usuario SET contrasena_hash = ? WHERE nombre_usuario = ?");
$stmt->execute([$hashedPassword, 'admin']);

echo "Contraseña actualizada correctamente.";
?>
