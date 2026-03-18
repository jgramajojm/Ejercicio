<?php
require_once 'config.php';
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

$db = getDB();

$stmt = $db->prepare("SELECT * FROM empleados WHERE id = :id AND activo = 1");
$stmt->execute([':id' => $id]);
$empleado = $stmt->fetch();

if (!$empleado) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => 'Empleado no encontrado.'
    ];
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Empleado</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container container-sm">
        <header class="header">
            <h1>👤 Detalle del Empleado</h1>
            <a href="index.php" class="btn btn-ghost">← Volver al listado</a>
        </header>

        <div class="card">
            <div class="form">

                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre</label>
                        <p><?= htmlspecialchars($empleado['nombre']) ?></p>
                    </div>
                    <div class="form-group">
                        <label>Apellido</label>
                        <p><?= htmlspecialchars($empleado['apellido']) ?></p>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <p><?= htmlspecialchars($empleado['email']) ?></p>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Departamento</label>
                        <p><span class="tag"><?= htmlspecialchars($empleado['departamento']) ?></span></p>
                    </div>
                    <div class="form-group">
                        <label>Salario</label>
                        <p><strong>Q <?= number_format($empleado['salario'], 2) ?></strong></p>
                    </div>
                </div>

                <div class="form-group">
                    <label>Fecha de Ingreso</label>
                    <p><?= date('d/m/Y', strtotime($empleado['fecha_ingreso'])) ?></p>
                </div>

                <div class="form-actions">
                    <a href="editar.php?id=<?= $empleado['id'] ?>" class="btn btn-warning">✏️ Editar</a>
                    <a href="index.php" class="btn btn-ghost">← Volver</a>
                </div>

            </div>
        </div>
    </div>
</body>
</html>