<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id_usuario'])) {
    header("Location: /DULCERIAV2/index.php");
    exit();
}

require_once __DIR__ . '/../includes/db.php';

$mensaje = "";
$clase_mensaje = "";
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $mensaje = "Venta registrada exitosamente.";
    $clase_mensaje = "success";
} elseif (isset($_GET['error']) && $_GET['error'] == 1) {
    $mensaje = "Error al registrar la venta. Revise que no existan campos vacíos.";
    $clase_mensaje = "error";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productos_venta = json_decode($_POST['productos_venta'], true);

    if (empty($productos_venta)) {
        header("Location: /DULCERIAV2/pages/punto_venta.php?error=1");
        exit();
    }

    $total_venta = 0;
    foreach ($productos_venta as $producto) {
        $total_venta += $producto['total'];
    }

    $conn->begin_transaction();

    try {
        $query_venta = "INSERT INTO ventas (id_usuario, fecha, total) VALUES (?, NOW(), ?)";
        $stmt_venta = $conn->prepare($query_venta);
        $stmt_venta->bind_param("id", $_SESSION['id_usuario'], $total_venta);
        $stmt_venta->execute();
        $id_venta = $stmt_venta->insert_id;
        $stmt_venta->close();

        $query_detalles = "INSERT INTO detalles_venta (id_venta, nombre_producto, cantidad, precio_unitario, total) VALUES (?, ?, ?, ?, ?)";
        $stmt_detalles = $conn->prepare($query_detalles);

        foreach ($productos_venta as $producto) {
            $stmt_detalles->bind_param("isidd", $id_venta, $producto['nombre'], $producto['cantidad'], $producto['precio'], $producto['total']);
            $stmt_detalles->execute();

            $query_actualizar_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
            $stmt_stock = $conn->prepare($query_actualizar_stock);
            $stmt_stock->bind_param("ii", $producto['cantidad'], $producto['id']);
            $stmt_stock->execute();
            $stmt_stock->close();
        }

        $stmt_detalles->close();
        $conn->commit();

        header("Location: /DULCERIAV2/pages/punto_venta.php?success=1");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: /DULCERIAV2/pages/punto_venta.php?error=1");
        exit();
    }
}

$query_productos = "SELECT * FROM productos WHERE stock > 0";
$result_productos = $conn->query($query_productos);

if (!$result_productos) {
    die("Error en la consulta de productos: " . $conn->error);
}

$productos = [];
while ($producto = $result_productos->fetch_assoc()) {
    $productos[] = $producto;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta | Dulcería El Pingüinito</title>
    <link rel="stylesheet" href="/DULCERIAV2/css/app.css">
    <link rel="stylesheet" href="/DULCERIAV2/css/module-layout.css">
    <style>
        .pos-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 18px;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 380px);
            gap: 18px;
            align-items: start;
        }

        .quantity-input {
            width: 72px;
            min-height: 36px;
            padding: 0 8px;
            text-align: center;
        }

        .cart-panel .table-wrap {
            min-height: 230px;
        }

        .products-panel .table-wrap {
            max-height: 430px;
            overflow: auto;
        }

        .products-panel .module-table th {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .empty-cart {
            padding: 26px;
            text-align: center;
            color: var(--muted);
        }

        .quote-card {
            position: sticky;
            top: 24px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 14px 34px rgba(184, 47, 114, 0.08);
            overflow: hidden;
        }

        .quote-header {
            padding: 18px;
            background: #fffafb;
            border-bottom: 1px solid var(--line);
        }

        .quote-header h2 {
            margin: 0;
            color: var(--pink-dark);
        }

        .quote-body {
            padding: 18px;
        }

        .quote-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--muted);
            font-weight: 700;
            margin-bottom: 14px;
        }

        .quote-total {
            border-top: 1px solid var(--line);
            margin-top: 10px;
            padding-top: 18px;
            color: var(--pink-dark);
        }

        .quote-total strong {
            display: block;
            font-size: clamp(34px, 4vw, 48px);
            line-height: 1;
            text-align: right;
        }

        @media (max-width: 1100px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }

            .quote-card {
                position: static;
            }
        }
    </style>
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
            <?php if ($_SESSION['rol'] === 'admin') : ?>
                <li><a href="/DULCERIAV2/pages/inventario.php">Gestión de Inventarios</a></li>
                <li><a href="/DULCERIAV2/pages/analisis_finaciero.php">Análisis Financiero</a></li>
                <li><a href="/DULCERIAV2/pages/administrar_empleados.php">Administrar Empleados</a></li>
            <?php endif; ?>
            <li><a class="active" href="/DULCERIAV2/pages/punto_venta.php">Punto de Venta</a></li>
        </ul>
        <ul class="sidebar-footer">
            <li><a href="/DULCERIAV2/actions/logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <main class="layout">
        <div class="page-header">
            <div>
                <h1>Punto de Venta</h1>
                <p>Hola, <?php echo htmlspecialchars($_SESSION['usuario']); ?>. Busca productos, arma la venta y confirma el cobro.</p>
            </div>
        </div>

        <?php if (!empty($mensaje)) : ?>
            <div class="message <?php echo $clase_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <div class="pos-grid">
            <section class="panel products-panel">
                <div class="panel-header">
                    <h2>Productos</h2>
                    <input type="text" class="search-bar" placeholder="Buscar producto..." oninput="filterProducts()">
                </div>
                <div class="table-wrap">
                    <table class="module-table" id="productos-disponibles">
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Cantidad</th>
                            <th>Acción</th>
                        </tr>
                        <?php foreach ($productos as $producto) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                <td><?php echo '$' . number_format($producto['precio'], 2); ?></td>
                                <td><?php echo htmlspecialchars($producto['stock']); ?></td>
                                <td>
                                    <input type="number" id="cantidad-<?php echo $producto['id']; ?>" class="quantity-input" min="1" max="<?php echo $producto['stock']; ?>" value="1">
                                </td>
                                <td class="actions-cell">
                                    <button class="btn btn-success" onclick="agregarProducto(<?php echo $producto['id']; ?>, '<?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES); ?>', <?php echo $producto['precio']; ?>, <?php echo $producto['stock']; ?>)">Agregar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </section>

            <div class="checkout-grid">
                <section class="panel cart-panel">
                    <div class="panel-header">
                        <h2>Productos agregados</h2>
                    </div>
                    <div class="table-wrap">
                        <table class="module-table" id="venta-actual">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio unit.</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </table>
                        <div class="empty-cart" id="empty-cart">Aún no hay productos agregados.</div>
                    </div>
                </section>

                <aside class="quote-card">
                    <div class="quote-header">
                        <h2>Cotización</h2>
                    </div>
                    <div class="quote-body">
                        <div class="quote-row">
                            <span>Artículos</span>
                            <strong id="total-articulos">0</strong>
                        </div>
                        <div class="quote-row">
                            <span>Subtotal</span>
                            <strong>$<span id="subtotal-venta">0.00</span></strong>
                        </div>
                        <div class="quote-row quote-total">
                            <span>Total a cobrar</span>
                            <strong>$<span id="total-venta">0.00</span></strong>
                        </div>
                        <button class="btn btn-primary" style="width: 100%; margin-top: 18px;" onclick="confirmarVenta()">Confirmar compra</button>
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <script>
        function toggleMenu() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        let ventaActual = [];
        let totalVenta = 0;

        function agregarProducto(id, nombre, precio, stock) {
            const cantidadInput = document.getElementById(`cantidad-${id}`);
            const cantidad = parseInt(cantidadInput.value);

            if (isNaN(cantidad) || cantidad < 1 || cantidad > stock) {
                alert("Cantidad inválida. Asegúrate de que sea un número válido y no exceda el stock disponible.");
                return;
            }

            const productoExistente = ventaActual.find(p => p.id === id);
            const cantidadTotal = productoExistente ? productoExistente.cantidad + cantidad : cantidad;

            if (cantidadTotal > stock) {
                alert("La cantidad solicitada excede el stock disponible.");
                return;
            }

            if (productoExistente) {
                productoExistente.cantidad += cantidad;
                productoExistente.total = productoExistente.cantidad * precio;
            } else {
                ventaActual.push({ id, nombre, precio, cantidad, total: cantidad * precio });
            }

            actualizarVentaActual();
        }

        function actualizarVentaActual() {
            const tablaVenta = document.getElementById('venta-actual');
            const tbody = tablaVenta.getElementsByTagName('tbody')[0];
            const emptyCart = document.getElementById('empty-cart');
            tbody.innerHTML = '';
            totalVenta = 0;
            let totalArticulos = 0;

            ventaActual.forEach(producto => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${producto.nombre}</td>
                    <td>${producto.cantidad}</td>
                    <td>$${producto.precio.toFixed(2)}</td>
                    <td>$${producto.total.toFixed(2)}</td>
                    <td class="actions-cell">
                        <button class="btn btn-delete" onclick="eliminarProducto(${producto.id})">Quitar</button>
                    </td>
                `;
                tbody.appendChild(row);
                totalVenta += producto.total;
                totalArticulos += producto.cantidad;
            });

            emptyCart.style.display = ventaActual.length === 0 ? 'block' : 'none';
            document.getElementById('total-articulos').textContent = totalArticulos;
            document.getElementById('subtotal-venta').textContent = totalVenta.toFixed(2);
            document.getElementById('total-venta').textContent = totalVenta.toFixed(2);
        }

        function eliminarProducto(id) {
            ventaActual = ventaActual.filter(p => p.id !== id);
            actualizarVentaActual();
        }

        function confirmarVenta() {
            if (ventaActual.length === 0) {
                alert("No hay productos en la venta actual.");
                return;
            }

            const formData = new FormData();
            formData.append('productos_venta', JSON.stringify(ventaActual));

            fetch('/DULCERIAV2/pages/punto_venta.php', {
                method: 'POST',
                body: formData
            })
            .then(() => {
                window.location.href = "/DULCERIAV2/pages/punto_venta.php?success=1";
            })
            .catch(error => {
                console.error('Error:', error);
                window.location.href = "/DULCERIAV2/pages/punto_venta.php?error=1";
            });
        }

        function filterProducts() {
            const input = document.querySelector('.search-bar');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('productos-disponibles');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[0];
                if (!td) continue;
                const txtValue = td.textContent || td.innerText;
                tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
            }
        }
    </script>
</body>
</html>
