<?php
require_once 'config.php';
session_start();

$errores = [];
$datos = [
    'nombre' => '',
    'apellido' => '',
    'email' => '',
    'departamento' => '',
    'salario' => '',
    'fecha_ingreso' => date('Y-m-d'),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar y validar
    $datos['nombre']       = trim($_POST['nombre'] ?? '');
    $datos['apellido']     = trim($_POST['apellido'] ?? '');
    $datos['email']        = trim($_POST['email'] ?? '');
    $datos['departamento'] = trim($_POST['departamento'] ?? '');
    $datos['salario']      = trim($_POST['salario'] ?? '');
    $datos['fecha_ingreso']= trim($_POST['fecha_ingreso'] ?? '');

    if (empty($datos['nombre']))       $errores[] = 'El nombre es requerido.';
    if (empty($datos['apellido']))     $errores[] = 'El apellido es requerido.';
    if (empty($datos['email']))        $errores[] = 'El email es requerido.';
    if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) $errores[] = 'El email no es válido.';
    if (empty($datos['departamento'])) $errores[] = 'El departamento es requerido.';
    if (!is_numeric($datos['salario']) || $datos['salario'] <= 0) $errores[] = 'El salario debe ser un número mayor a 0.';
    if (empty($datos['fecha_ingreso'])) $errores[] = 'La fecha de ingreso es requerida.';

    if (empty($errores)) {
        $db = getDB();

        // Verificar email duplicado
        $check = $db->prepare("SELECT id FROM empleados WHERE email = ?");
        $check->execute([$datos['email']]);
        if ($check->fetch()) {
            $errores[] = 'Ya existe un empleado con ese email.';
        } else {
            $stmt = $db->prepare("
                INSERT INTO empleados (nombre, apellido, email, departamento, salario, fecha_ingreso)
                VALUES (:nombre, :apellido, :email, :departamento, :salario, :fecha_ingreso)
            ");
            $stmt->execute($datos);

            $_SESSION['mensaje'] = [
                'tipo'  => 'success',
                'texto' => "✅ Empleado {$datos['nombre']} {$datos['apellido']} creado correctamente."
            ];
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Empleado | Ejercicio Git</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container container-sm">
        <header class="header">
            <h1>➕ Agregar Empleado</h1>
            <a href="index.php" class="btn btn-ghost">← Volver al listado</a>
        </header>

        <?php if (!empty($errores)): ?>
            <div class="alert alert-error">
                <strong>Por favor corrige los siguientes errores:</strong>
                <ul>
                    <?php foreach ($errores as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card">
            <form method="POST" class="form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre *</label>
                        <input type="text" id="nombre" name="nombre"
                               value="<?= htmlspecialchars($datos['nombre']) ?>"
                               placeholder="Ej: Carlos" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido *</label>
                        <input type="text" id="apellido" name="apellido"
                               value="<?= htmlspecialchars($datos['apellido']) ?>"
                               placeholder="Ej: Mendoza" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($datos['email']) ?>"
                           placeholder="empleado@hogaresisn.com" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="departamento">Departamento *</label>
                        <select id="departamento" name="departamento" required>
                            <option value="">Seleccionar...</option>
                            <?php
                            $deptos = ['Desarrollo', 'Ventas', 'Contabilidad', 'Recursos Humanos', 'Operaciones', 'Gerencia'];
                            foreach ($deptos as $d):
                                $selected = $datos['departamento'] === $d ? 'selected' : '';
                            ?>
                                <option value="<?= $d ?>" <?= $selected ?>><?= $d ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="salario">Salario (Q) *</label>
                        <input type="number" id="salario" name="salario"
                               value="<?= htmlspecialchars($datos['salario']) ?>"
                               placeholder="0.00" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="fecha_ingreso">Fecha de Ingreso *</label>
                    <input type="date" id="fecha_ingreso" name="fecha_ingreso"
                           value="<?= htmlspecialchars($datos['fecha_ingreso']) ?>" required>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn btn-ghost">Cancelar</a>
                    <button type="submit" class="btn btn-primary">✅ Guardar Empleado</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
