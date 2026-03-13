<?php
require_once 'config.php';
session_start();

$db = getDB();
$id = intval($_GET['id'] ?? 0);

if (!$id) {
    header('Location: index.php');
    exit;
}

// Obtener empleado existente
$stmt = $db->prepare("SELECT * FROM empleados WHERE id = ? AND activo = 1");
$stmt->execute([$id]);
$empleado = $stmt->fetch();

if (!$empleado) {
    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Empleado no encontrado.'];
    header('Location: index.php');
    exit;
}

$errores = [];
$datos = $empleado; // Pre-llenar con datos actuales

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    if (!is_numeric($datos['salario']) || $datos['salario'] <= 0) $errores[] = 'El salario debe ser mayor a 0.';
    if (empty($datos['fecha_ingreso'])) $errores[] = 'La fecha de ingreso es requerida.';

    if (empty($errores)) {
        // Verificar email duplicado (excluyendo el actual)
        $check = $db->prepare("SELECT id FROM empleados WHERE email = ? AND id != ?");
        $check->execute([$datos['email'], $id]);
        if ($check->fetch()) {
            $errores[] = 'Ya existe otro empleado con ese email.';
        } else {
            $stmt = $db->prepare("
                UPDATE empleados
                SET nombre = :nombre,
                    apellido = :apellido,
                    email = :email,
                    departamento = :departamento,
                    salario = :salario,
                    fecha_ingreso = :fecha_ingreso
                WHERE id = :id
            ");
            $stmt->execute([
                ':nombre'       => $datos['nombre'],
                ':apellido'     => $datos['apellido'],
                ':email'        => $datos['email'],
                ':departamento' => $datos['departamento'],
                ':salario'      => $datos['salario'],
                ':fecha_ingreso'=> $datos['fecha_ingreso'],
                ':id'           => $id,
            ]);

            $_SESSION['mensaje'] = [
                'tipo'  => 'success',
                'texto' => "✅ Empleado {$datos['nombre']} {$datos['apellido']} actualizado correctamente."
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
    <title>Editar Empleado | Ejercicio Git</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container container-sm">
        <header class="header">
            <h1>✏️ Editar Empleado</h1>
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
                               value="<?= htmlspecialchars($datos['nombre']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido *</label>
                        <input type="text" id="apellido" name="apellido"
                               value="<?= htmlspecialchars($datos['apellido']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($datos['email']) ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="departamento">Departamento *</label>
                        <select id="departamento" name="departamento" required>
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
                               step="0.01" min="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="fecha_ingreso">Fecha de Ingreso *</label>
                    <input type="date" id="fecha_ingreso" name="fecha_ingreso"
                           value="<?= htmlspecialchars($datos['fecha_ingreso']) ?>" required>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn btn-ghost">Cancelar</a>
                    <button type="submit" class="btn btn-primary">💾 Actualizar Empleado</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
