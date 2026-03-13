<?php
require_once 'config.php';
session_start();

$db  = getDB();
$id  = intval($_GET['id'] ?? 0);

if (!$id) {
    header('Location: index.php');
    exit;
}

// Verificar que existe
$stmt = $db->prepare("SELECT nombre, apellido FROM empleados WHERE id = ? AND activo = 1");
$stmt->execute([$id]);
$empleado = $stmt->fetch();

if (!$empleado) {
    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Empleado no encontrado.'];
    header('Location: index.php');
    exit;
}

// Soft delete — marcamos como inactivo, NO borramos el registro
$stmt = $db->prepare("UPDATE empleados SET activo = 0 WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['mensaje'] = [
    'tipo'  => 'warning',
    'texto' => "🗑️ Empleado {$empleado['nombre']} {$empleado['apellido']} eliminado correctamente."
];

header('Location: index.php');
exit;
