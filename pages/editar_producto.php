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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$producto = null;
if ($id > 0) {
    $query = "SELECT * FROM productos WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc();
    $stmt->close();
}

if (!$producto) {
    header("Location: /DULCERIAV2/pages/inventario.php");
    exit();
}

$mensaje = "";
$clase_mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $stock = intval($_POST['stock']);
    $precio = floatval($_POST['precio']);

    if (empty($nombre) || $stock < 0 || $precio <= 0) {
        $mensaje = "Por favor, complete todos los campos correctamente.";
        $clase_mensaje = "error";
    } else {
        $query = "UPDATE productos SET nombre = ?, stock = ?, precio = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sidi", $nombre, $stock, $precio, $id);

        if ($stmt->execute()) {
            $mensaje = "Producto actualizado correctamente.";
            $clase_mensaje = "success";
        } else {
            $mensaje = "Error al actualizar el producto: " . $stmt->error;
            $clase_mensaje = "error";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - Dulcería El Pingüinito</title>
    <link rel="stylesheet" href="/DULCERIAV2/css/app.css">
    <style>
        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .form-container {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            text-align: center;
            color: #d14778;
            margin-bottom: 20px;
        }
        .form-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .form-container input[type="text"],
        .form-container input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: purple;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background-color: #6a1b9a;
        }
        .btn-cancelar {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #FF0000;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .btn-cancelar:hover {
            background-color: rgb(88, 116, 113);
        }
        .mensaje {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .mensaje.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .mensaje.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .cerrar-mensaje {
            float: right;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="inventario.php" class="btn-cancelar">Salir</a>

        <div class="form-container">
            <h2>Editar Producto</h2>
            <?php if (!empty($mensaje)) : ?>
                <div class="mensaje <?php echo $clase_mensaje; ?>">
                    <?php echo $mensaje; ?>
                    <span class="cerrar-mensaje" onclick="this.parentElement.style.display='none';">×</span>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="nombre">Nombre del Producto:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>

                <label for="stock">Stock (cantidad en piezas):</label>
                <input type="number" id="stock" name="stock" min="0" value="<?php echo htmlspecialchars($producto['stock']); ?>" required>

                <label for="precio">Precio en MXN:</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0.01" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>

                <input type="submit" value="Actualizar Producto">
            </form>
        </div>
    </div>
</body>
</html>