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
    header('Location: listado.php');
    exit();
}

$cedula = $_GET['cedula'];

// Eliminar miembro
$sql = "DELETE FROM miembros WHERE cedula_miembro = :cedula";
$stmt = $pdo->prepare($sql);
$stmt->execute(['cedula' => $cedula]);

echo "<script>alert('Miembro eliminado correctamente.'); window.location='listado.php';</script>";
exit();
