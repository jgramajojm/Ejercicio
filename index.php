<?php
require_once 'config.php';

$db = getDB();

// Filtro de búsqueda
$busqueda = trim($_GET['buscar'] ?? '');
$departamento_filtro = $_GET['departamento'] ?? '';

//jgramajo
$salario_min = $_GET['salario_min'] ?? '';
$salario_max = $_GET['salario_max'] ?? '';
$por_pagina = 10;
$pagina = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($pagina - 1) * $por_pagina;

//  Validación de rango salario (ANTES de todo)
if ($salario_min !== '' && $salario_max !== '' && $salario_min > $salario_max) {
    $temp = $salario_min;
    $salario_min = $salario_max;
    $salario_max = $temp;
}

//  Configuración paginación
$por_pagina = 10;
$pagina = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($pagina - 1) * $por_pagina;


// =========================
// QUERY COUNT (TOTAL)
// =========================
$sql_count = "SELECT COUNT(*) FROM empleados WHERE activo = 1";
$params_count = [];

if ($busqueda) {
    $sql_count .= " AND (nombre LIKE :buscar OR apellido LIKE :buscar OR email LIKE :buscar)";
    $params_count[':buscar'] = "%$busqueda%";
}

if ($departamento_filtro) {
    $sql_count .= " AND departamento = :depto";
    $params_count[':depto'] = $departamento_filtro;
}

if ($salario_min !== '') {
    $sql_count .= " AND salario >= :salario_min";
    $params_count[':salario_min'] = $salario_min;
}

if ($salario_max !== '') {
    $sql_count .= " AND salario <= :salario_max";
    $params_count[':salario_max'] = $salario_max;
}

$stmt_count = $db->prepare($sql_count);
$stmt_count->execute($params_count);
$total_registros = $stmt_count->fetchColumn();

$total_paginas = ceil($total_registros / $por_pagina);

// evitar página inválida
if ($pagina > $total_paginas && $total_paginas > 0) {
    $pagina = $total_paginas;
    $offset = ($pagina - 1) * $por_pagina;
}


// =========================
// QUERY PRINCIPAL
// =========================
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

if ($salario_min !== '') {
    $sql .= " AND salario >= :salario_min";
    $params[':salario_min'] = $salario_min;
}

if ($salario_max !== '') {
    $sql .= " AND salario <= :salario_max";
    $params[':salario_max'] = $salario_max;
}

$sql .= " ORDER BY id ASC LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($sql);


foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->bindValue(':limit', (int)$por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

$stmt->execute();
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
                <input
                    type="number"
                    name="salario_min"
                    placeholder="Q Salario mínimo"
                    value="<?= htmlspecialchars($salario_min) ?>"
                    class="input-search"
                >

                <input
                    type="number"
                    name="salario_max"
                    placeholder="Q Salario máximo"
                    value="<?= htmlspecialchars($salario_max) ?>"
                    class="input-search"
                >
                </select>
                <button type="submit" class="btn btn-secondary">🔍 Buscar</button>

                <?php if ($busqueda || $departamento_filtro || $salario_min !== '' || $salario_max !== ''): ?>
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
                                    <a href="detalle.php?id=<?= $emp['id'] ?>" class="btn btn-sm btn-warning">🔍 Ver Detalle</a>
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
            <?php if ($total_paginas > 1): ?>
                <div class="pagination">

                    <!-- Botón Anterior -->
                    <?php if ($pagina > 1): ?>
                        <a href="?page=<?= $pagina - 1 ?>&buscar=<?= urlencode($busqueda) ?>&departamento=<?= urlencode($departamento_filtro) ?>&salario_min=<?= $salario_min ?>&salario_max=<?= $salario_max ?>" class="btn btn-sm">← Anterior</a>
                    <?php endif; ?>

                    <!-- Números -->
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <a href="?page=<?= $i ?>&buscar=<?= urlencode($busqueda) ?>&departamento=<?= urlencode($departamento_filtro) ?>&salario_min=<?= $salario_min ?>&salario_max=<?= $salario_max ?>"
                           class="btn btn-sm <?= $i == $pagina ? 'btn-primary' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <!-- Botón Siguiente -->
                    <?php if ($pagina < $total_paginas): ?>
                        <a href="?page=<?= $pagina + 1 ?>&buscar=<?= urlencode($busqueda) ?>&departamento=<?= urlencode($departamento_filtro) ?>&salario_min=<?= $salario_min ?>&salario_max=<?= $salario_max ?>" class="btn btn-sm">Siguiente →</a>
                    <?php endif; ?>

                </div>
            <?php endif; ?>
            <p>Total de empleados: <strong><?= count($empleados) ?></strong></p>
        </footer>
    </div>
</body>
</html>
