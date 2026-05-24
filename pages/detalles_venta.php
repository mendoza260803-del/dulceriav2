<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['id_usuario'])) {
    header("Location: /DULCERIAV2/index.php");
    exit();
}

if ($_SESSION['rol'] !== 'admin') {
    header("Location: /DULCERIAV2/pages/punto_venta.php");
    exit();
}

require_once __DIR__ . '/../includes/db.php';

if (!isset($_GET['fecha'])) {
    die("Fecha no proporcionada.");
}

$fecha_venta = $_GET['fecha'];

$query_detalles = "
    SELECT
        v.id AS id_venta,
        dv.nombre_producto,
        dv.cantidad,
        dv.precio_unitario,
        dv.total AS total_producto,
        u.id AS id_usuario,
        u.nombre AS nombre_usuario,
        u.apellidos AS apellidos_usuario
    FROM detalles_venta dv
    INNER JOIN ventas v ON dv.id_venta = v.id
    INNER JOIN usuarios u ON v.id_usuario = u.id
    WHERE DATE(v.fecha) = ?
";
$stmt_detalles = $conn->prepare($query_detalles);
$stmt_detalles->bind_param("s", $fecha_venta);
$stmt_detalles->execute();
$result_detalles = $stmt_detalles->get_result();

if (!$result_detalles) {
    die("Error en la consulta de detalles: " . $conn->error);
}

$detalles = [];
while ($detalle = $result_detalles->fetch_assoc()) {
    $detalles[] = $detalle;
}
$stmt_detalles->close();

$query_resumen_usuarios = "
    SELECT
        u.id AS id_usuario,
        u.nombre AS nombre_usuario,
        u.apellidos AS apellidos_usuario,
        SUM(dv.cantidad) AS total_productos,
        SUM(dv.total) AS total_ventas
    FROM detalles_venta dv
    INNER JOIN ventas v ON dv.id_venta = v.id
    INNER JOIN usuarios u ON v.id_usuario = u.id
    WHERE DATE(v.fecha) = ?
    GROUP BY u.id, u.nombre, u.apellidos
";
$stmt_resumen = $conn->prepare($query_resumen_usuarios);
$stmt_resumen->bind_param("s", $fecha_venta);
$stmt_resumen->execute();
$result_resumen = $stmt_resumen->get_result();

if (!$result_resumen) {
    die("Error en la consulta del resumen por usuario: " . $conn->error);
}

$resumenes = [];
while ($resumen = $result_resumen->fetch_assoc()) {
    $resumenes[] = $resumen;
}
$stmt_resumen->close();

$total_productos = array_sum(array_map('intval', array_column($resumenes, 'total_productos')));
$total_ventas = array_sum(array_map('floatval', array_column($resumenes, 'total_ventas')));

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Ventas | Dulcería El Pingüinito</title>
    <link rel="stylesheet" href="/DULCERIAV2/css/app.css">
    <link rel="stylesheet" href="/DULCERIAV2/css/module-layout.css">
</head>
<body class="module-page">
    <header class="mobile-header">
        <div class="menu-icon" onclick="toggleMenu()">☰</div>
        <div class="brand">
            <img src="/DULCERIAV2/assets/logo.png" alt="Logo de Dulcería El Pingüinito">
            <span>Dulcería <b>"El Pingüinito"</b></span>
        </div>
    </header>

    <nav id="sidebar">
        <button class="close-btn" onclick="toggleMenu()">×</button>
        <div class="sidebar-brand">
            <img src="/DULCERIAV2/assets/logo.png" alt="Logo de Dulcería El Pingüinito">
            <div>
                <span>Dulcería</span>
                <strong>El Pingüinito</strong>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="/DULCERIAV2/pages/dashboard.php">Inicio</a></li>
            <li><a href="/DULCERIAV2/pages/inventario.php">Gestión de Inventarios</a></li>
            <li><a class="active" href="/DULCERIAV2/pages/analisis_finaciero.php">Análisis Financiero</a></li>
            <li><a href="/DULCERIAV2/pages/administrar_empleados.php">Administrar Empleados</a></li>
            <li><a href="/DULCERIAV2/pages/punto_venta.php">Punto de Venta</a></li>
        </ul>
        <ul class="sidebar-footer">
            <li><a href="/DULCERIAV2/actions/logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <main class="layout">
        <div class="page-header">
            <div>
                <h1>Detalles de Ventas</h1>
                <p>Ventas registradas el <?php echo htmlspecialchars($fecha_venta); ?>.</p>
            </div>
            <a class="btn btn-primary" href="/DULCERIAV2/pages/analisis_finaciero.php">Volver al análisis</a>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <strong><?php echo $total_productos; ?></strong>
                <span>Productos vendidos</span>
            </div>
            <div class="summary-card">
                <strong><?php echo '$' . number_format($total_ventas, 2); ?></strong>
                <span>Total vendido</span>
            </div>
        </div>

        <section class="panel" style="margin-bottom: 20px;">
            <div class="panel-header">
                <h2>Detalle por producto</h2>
            </div>
            <div class="table-wrap">
                <table class="module-table">
                    <tr>
                        <th>ID Venta</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Total producto</th>
                        <th>Vendedor</th>
                    </tr>
                    <?php foreach ($detalles as $detalle) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($detalle['id_venta']); ?></td>
                            <td><?php echo htmlspecialchars($detalle['nombre_producto']); ?></td>
                            <td><?php echo htmlspecialchars($detalle['cantidad']); ?></td>
                            <td><?php echo '$' . number_format($detalle['precio_unitario'], 2); ?></td>
                            <td><?php echo '$' . number_format($detalle['total_producto'], 2); ?></td>
                            <td><?php echo htmlspecialchars($detalle['nombre_usuario'] . ' ' . $detalle['apellidos_usuario']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </section>

        <section class="panel">
            <div class="panel-header">
                <h2>Resumen por vendedor</h2>
            </div>
            <div class="table-wrap">
                <table class="module-table">
                    <tr>
                        <th>ID Usuario</th>
                        <th>Vendedor</th>
                        <th>Total productos</th>
                        <th>Total ventas</th>
                    </tr>
                    <?php foreach ($resumenes as $resumen) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($resumen['id_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($resumen['nombre_usuario'] . ' ' . $resumen['apellidos_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($resumen['total_productos']); ?></td>
                            <td><?php echo '$' . number_format($resumen['total_ventas'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </section>
    </main>

    <script>
        function toggleMenu() {
            document.getElementById('sidebar').classList.toggle('active');
        }
    </script>
</body>
</html>
