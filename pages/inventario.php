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

$mensaje = "";
$clase_mensaje = "";
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $mensaje = isset($_GET['mensaje']) ? $_GET['mensaje'] : "Operación exitosa.";
    $clase_mensaje = "success";
} elseif (isset($_GET['error']) && $_GET['error'] == 1) {
    $mensaje = isset($_GET['mensaje']) ? $_GET['mensaje'] : "Error en la operación.";
    $clase_mensaje = "error";
}

$query = "SELECT * FROM productos";
$result = $conn->query($query);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}

$total_productos = count($productos);
$stock_total = array_sum(array_map('intval', array_column($productos, 'stock')));

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventarios | Dulcería El Pingüinito</title>
    <link rel="stylesheet" href="/DULCERIAV2/css/app.css">
    <style>
        :root {
            --pink: #f06aa6;
            --pink-dark: #b82f72;
            --ink: #3b2330;
            --muted: #7d6370;
            --line: #f3c7d9;
            --soft: #fff2f7;
            --surface: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            color: var(--ink);
            background: #fff8fb;
        }

        .mobile-header {
            display: none;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            background: linear-gradient(90deg, #f06aa6, #ff9ec7);
            color: #fff;
            box-shadow: 0 8px 24px rgba(184, 47, 114, 0.18);
            position: sticky;
            top: 0;
            z-index: 5;
        }

        .menu-icon {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.2);
            font-size: 24px;
            cursor: pointer;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
        }

        .brand img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
        }

        #sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 280px;
            padding: 24px 18px;
            background: linear-gradient(180deg, #f06aa6 0%, #ff9ec7 100%);
            box-shadow: 0 8px 24px rgba(184, 47, 114, 0.18);
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        .close-btn {
            display: none;
            position: absolute;
            right: 12px;
            top: 12px;
            border: 0;
            background: none;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 8px;
            margin-bottom: 28px;
            color: #fff;
            text-shadow: 0 1px 2px rgba(95, 43, 70, 0.25);
        }

        .sidebar-brand img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #fff;
        }

        .sidebar-brand span {
            display: block;
            font-size: 13px;
            font-weight: 700;
        }

        .sidebar-brand strong {
            display: block;
            font-size: 18px;
        }

        .sidebar-menu,
        .sidebar-footer {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu {
            flex: 1;
        }

        .sidebar-menu li,
        .sidebar-footer li {
            padding: 6px 0;
        }

        .sidebar-menu a,
        .sidebar-footer a {
            display: block;
            padding: 12px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: #4c2637;
            font-weight: 800;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.42);
            color: #9d225f;
        }

        .sidebar-footer a {
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.7);
            color: #9d225f;
            text-align: center;
        }

        .layout {
            width: min(1480px, calc(100% - 320px));
            margin-left: 300px;
            padding: 32px 28px 48px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            margin-bottom: 22px;
        }

        .page-header h1 {
            margin: 0 0 8px;
            color: var(--pink-dark);
            font-size: clamp(30px, 4vw, 44px);
            line-height: 1.05;
        }

        .page-header p {
            margin: 0;
            color: var(--muted);
        }

        .add-product {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 12px 18px;
            border-radius: 8px;
            background: linear-gradient(90deg, #f06aa6, #c9c9cf);
            color: #fff;
            text-decoration: none;
            font-weight: 800;
            box-shadow: 0 10px 22px rgba(168, 61, 114, 0.14);
            white-space: nowrap;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .summary-card {
            padding: 18px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface);
            box-shadow: 0 12px 30px rgba(168, 61, 114, 0.06);
        }

        .summary-card strong {
            display: block;
            color: var(--pink-dark);
            font-size: 30px;
            line-height: 1;
            margin-bottom: 6px;
        }

        .summary-card span {
            color: var(--muted);
            font-weight: 700;
        }

        .mensaje {
            padding: 14px 16px;
            margin-bottom: 18px;
            border-radius: 8px;
            font-weight: 700;
        }

        .mensaje.success {
            background: #e9f8ee;
            color: #155724;
            border: 1px solid #bfe8ca;
        }

        .mensaje.error {
            background: #fff0f2;
            color: #721c24;
            border: 1px solid #f3b9c0;
        }

        .cerrar-mensaje {
            float: right;
            cursor: pointer;
            font-weight: 900;
        }

        .inventory-panel {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface);
            overflow: hidden;
            box-shadow: 0 14px 34px rgba(168, 61, 114, 0.07);
        }

        .inventory-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 18px;
            border-bottom: 1px solid var(--line);
            background: #fffafb;
        }

        .inventory-toolbar h2 {
            margin: 0;
            color: var(--pink-dark);
        }

        .search-bar {
            width: min(420px, 100%);
            height: 42px;
            padding: 0 14px;
            font-size: 15px;
            border: 1px solid var(--line);
            border-radius: 8px;
            outline: none;
        }

        .search-bar:focus {
            border-color: var(--pink);
            box-shadow: 0 0 0 3px rgba(240, 106, 166, 0.14);
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        th,
        td {
            padding: 15px 18px;
            text-align: left;
            border-bottom: 1px solid #f4dbe5;
        }

        th {
            color: #8b174f;
            background: #fff8fb;
            font-size: 13px;
            text-transform: uppercase;
        }

        td {
            color: var(--ink);
        }

        tr:hover td {
            background: #fffafd;
        }

        .buttons {
            text-align: right;
            white-space: nowrap;
        }

        .buttons button {
            min-height: 36px;
            padding: 8px 12px;
            margin-left: 6px;
            border: 1px solid transparent;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 800;
        }

        .edit {
            background: #eee9ff;
            border-color: #d7cffb;
            color: #6554c9;
        }

        .delete {
            background: #ffe1ea;
            border-color: #f6c2d3;
            color: #b82f5f;
        }

        @media (max-width: 900px) {
            .mobile-header {
                display: flex;
            }

            #sidebar {
                left: -280px;
                transition: left 0.3s;
            }

            #sidebar.active {
                left: 0;
            }

            .close-btn {
                display: block;
            }

            .layout {
                width: min(100% - 28px, 1480px);
                margin: 0 auto;
                padding: 24px 0 42px;
            }

            .page-header,
            .inventory-toolbar {
                align-items: stretch;
                flex-direction: column;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .add-product,
            .search-bar {
                width: 100%;
            }
        }
    </style>
</head>
<body>
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
            <li><a class="active" href="/DULCERIAV2/pages/inventario.php">Gestión de Inventarios</a></li>
            <li><a href="/DULCERIAV2/pages/analisis_finaciero.php">Análisis Financiero</a></li>
            <li><a href="/DULCERIAV2/pages/administrar_empleados.php">Administrar Empleados</a></li>
            <li><a href="/DULCERIAV2/pages/punto_venta.php">Punto de Venta</a></li>
        </ul>
        <ul class="sidebar-footer">
            <li><a href="/DULCERIAV2/actions/logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <main class="layout" id="main-content">
        <div class="page-header">
            <div>
                <h1>Gestión de Inventarios</h1>
                <p>Hola, <?php echo htmlspecialchars($_SESSION['usuario']); ?>. Controla productos, stock y precios de venta.</p>
            </div>
            <a href="/DULCERIAV2/pages/agregar_producto.php" class="add-product">Agregar nuevo producto</a>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <strong><?php echo $total_productos; ?></strong>
                <span>Productos registrados</span>
            </div>
            <div class="summary-card">
                <strong><?php echo $stock_total; ?></strong>
                <span>Piezas en inventario</span>
            </div>
        </div>

        <?php if (!empty($mensaje)) : ?>
            <div class="mensaje <?php echo $clase_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
                <span class="cerrar-mensaje" onclick="this.parentElement.style.display='none';">×</span>
            </div>
        <?php endif; ?>

        <section class="inventory-panel">
            <div class="inventory-toolbar">
                <h2>Inventario</h2>
                <input type="text" class="search-bar" placeholder="Buscar producto..." oninput="filterProducts()">
            </div>

            <div class="table-wrap">
                <table id="inventory-table">
                    <tr>
                        <th>Nombre del producto</th>
                        <th>Stock</th>
                        <th>Precio en MXN</th>
                        <th>Acción</th>
                    </tr>
                    <?php foreach ($productos as $row) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($row['stock']); ?></td>
                            <td><?php echo '$' . number_format($row['precio'], 2); ?></td>
                            <td class="buttons">
                                <a href="/DULCERIAV2/pages/editar_producto.php?id=<?php echo $row['id']; ?>">
                                    <button class="edit">Editar</button>
                                </a>
                                <a href="/DULCERIAV2/actions/eliminar_producto.php?id=<?php echo $row['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este producto?');">
                                    <button class="delete">Eliminar</button>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </section>
    </main>

    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        function filterProducts() {
            const input = document.querySelector('.search-bar');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('inventory-table');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[0];
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
                }
            }
        }
    </script>
</body>
</html>
