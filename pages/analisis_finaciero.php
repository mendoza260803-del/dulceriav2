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

$query_ventas = "SELECT DATE(fecha) AS fecha, SUM(total) AS total_dia FROM ventas GROUP BY DATE(fecha) ORDER BY fecha DESC";
$result_ventas = $conn->query($query_ventas);

if (!$result_ventas) {
    die("Error en la consulta de ventas: " . $conn->error);
}

$ventas = [];
while ($venta = $result_ventas->fetch_assoc()) {
    $ventas[] = $venta;
}

$dias_con_ventas = count($ventas);
$total_general = array_sum(array_map('floatval', array_column($ventas, 'total_dia')));

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis Financiero | Dulcería El Pingüinito</title>
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
                <h1>Análisis Financiero</h1>
                <p>Hola, <?php echo htmlspecialchars($_SESSION['usuario']); ?>. Consulta ventas por fecha y revisa sus detalles.</p>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <strong><?php echo $dias_con_ventas; ?></strong>
                <span>Días con ventas</span>
            </div>
            <div class="summary-card">
                <strong><?php echo '$' . number_format($total_general, 2); ?></strong>
                <span>Ventas acumuladas</span>
            </div>
        </div>

        <section class="panel">
            <div class="panel-header">
                <h2>Ventas por fecha</h2>
                <input type="date" class="search-bar" id="fecha-busqueda" oninput="filtrarPorFecha()">
            </div>

            <div class="table-wrap">
                <table class="module-table" id="tabla-ventas">
                    <tr>
                        <th>Fecha</th>
                        <th>Venta en MXN</th>
                        <th>Detalles</th>
                    </tr>
                    <?php foreach ($ventas as $venta) : ?>
                        <tr class="fila-venta">
                            <td><?php echo htmlspecialchars($venta['fecha']); ?></td>
                            <td><?php echo '$' . number_format($venta['total_dia'], 2); ?></td>
                            <td class="actions-cell">
                                <a class="btn btn-success" href="/DULCERIAV2/pages/detalles_venta.php?fecha=<?php echo urlencode($venta['fecha']); ?>">Ver detalles</a>
                            </td>
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

        function filtrarPorFecha() {
            const fechaBusqueda = document.getElementById('fecha-busqueda').value;
            const filasVenta = document.querySelectorAll('.fila-venta');

            filasVenta.forEach(fila => {
                const fechaVentaElement = fila.getElementsByTagName('td')[0];
                if (!fechaVentaElement) return;
                const fechaVenta = fechaVentaElement.textContent.trim();
                fila.style.display = !fechaBusqueda || fechaVenta === fechaBusqueda ? '' : 'none';
            });
        }
    </script>
</body>
</html>
