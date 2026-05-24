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

if ($id > 0) {
    $query = "DELETE FROM productos WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      
        header("Location: /DULCERIAV2/pages/inventario.php?success=1&mensaje=Producto eliminado correctamente.");
        exit();
    } else {
        
        header("Location: /DULCERIAV2/pages/inventario.php?error=1&mensaje=Error al eliminar el producto.");
        exit();
    }

    $stmt->close();
} else {
    header("Location: /DULCERIAV2/pages/inventario.php");
    exit();
}
?>