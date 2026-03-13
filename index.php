<?php
require_once 'config.php';

$db = getDB();

// Filtro de búsqueda
$busqueda = trim($_GET['buscar'] ?? '');
$departamento_filtro = $_GET['departamento'] ?? '';

$sql = "SELECT * FROM empleados WHERE activo = 1";
$params = [];

if ($busqueda) {
    $sql .= " AND (nombre LIKE :buscar OR apellido LIKE :buscar OR email LIKE :buscar)";
    $params[':buscar'] = "%$busqueda%";
}

if ($departamento_filtro) {
    $sql .= " AND departamento = :depto";
    $params[':depto'] = $departamento_filtro;
}

$sql .= " ORDER BY creado_en DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$empleados = $stmt->fetchAll();

// Obtener departamentos para el filtro
$departamentos = $db->query("SELECT DISTINCT departamento FROM empleados WHERE activo = 1 ORDER BY departamento")->fetchAll();

// Mensaje de sesión (para confirmaciones)
session_start();
$mensaje = $_SESSION['mensaje'] ?? null;
unset($_SESSION['mensaje']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados | Ejercicio Git</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="header-top">
                <h1>👥 Gestión de Empleados</h1>
                <span class="badge">Ejercicio Git - Hogares ISN</span>
            </div>
            <p class="subtitle">Sistema de práctica para control de versiones</p>
        </header>

        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $mensaje['tipo'] ?>">
                <?= htmlspecialchars($mensaje['texto']) ?>
            </div>
        <?php endif; ?>

        <div class="toolbar">
            <form method="GET" class="search-form">
                <input
                    type="text"
                    name="buscar"
                    placeholder="Buscar por nombre, apellido o email..."
                    value="<?= htmlspecialchars($busqueda) ?>"
                    class="input-search"
                >
                <select name="departamento" class="select-filter">
                    <option value="">Todos los departamentos</option>
                    <?php foreach ($departamentos as $d): ?>
                        <option value="<?= htmlspecialchars($d['departamento']) ?>"
                            <?= $departamento_filtro === $d['departamento'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['departamento']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-secondary">🔍 Buscar</button>
                <?php if ($busqueda || $departamento_filtro): ?>
                    <a href="index.php" class="btn btn-ghost">✕ Limpiar</a>
                <?php endif; ?>
            </form>

            <a href="crear.php" class="btn btn-primary">+ Agregar Empleado</a>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Departamento</th>
                        <th>Salario</th>
                        <th>Fecha Ingreso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($empleados)): ?>
                        <tr>
                            <td colspan="7" class="empty-state">
                                No se encontraron empleados
                                <?= $busqueda ? "con \"$busqueda\"" : '' ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($empleados as $emp): ?>
                            <tr>
                                <td><?= $emp['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($emp['nombre'] . ' ' . $emp['apellido']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($emp['email']) ?></td>
                                <td>
                                    <span class="tag"><?= htmlspecialchars($emp['departamento']) ?></span>
                                </td>
                                <td>Q <?= number_format($emp['salario'], 2) ?></td>
                                <td><?= date('d/m/Y', strtotime($emp['fecha_ingreso'])) ?></td>
                                <td class="actions">
                                    <a href="editar.php?id=<?= $emp['id'] ?>" class="btn btn-sm btn-warning">✏️ Editar</a>
                                    <a href="eliminar.php?id=<?= $emp['id'] ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('¿Seguro que deseas eliminar a <?= htmlspecialchars($emp['nombre']) ?>?')">
                                        🗑️ Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <footer class="footer">
            <p>Total de empleados: <strong><?= count($empleados) ?></strong></p>
        </footer>
    </div>
</body>
</html>
