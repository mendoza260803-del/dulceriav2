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
$empleado_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$empleado = null;

if ($empleado_id <= 0) {
    header("Location: /DULCERIAV2/pages/administrar_empleados.php?error=empleado_invalido");
    exit();
}

$sql_select = "SELECT id, nombre, apellidos, email, rol FROM usuarios WHERE id = ?";
$stmt_select = $conn->prepare($sql_select);
if ($stmt_select) {
    $stmt_select->bind_param("i", $empleado_id);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();
    if ($result_select->num_rows == 1) {
        $empleado = $result_select->fetch_assoc();
    } else {
        header("Location: /DULCERIAV2/pages/administrar_empleados.php?error=empleado_no_encontrado");
        exit();
    }
    $stmt_select->close();
} else {
    $mensaje = "Error al preparar la consulta para obtener el empleado: " . $conn->error;
    $clase_mensaje = "error";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar']) && $empleado) {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $correo = trim($_POST['correo']);
    $rol = $_POST['rol'];
    $nueva_contrasena = trim($_POST['nueva_contrasena']);
    $confirmar_contrasena = trim($_POST['confirmar_contrasena']);

    $errores = [];

    if (empty($nombre) || !preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $nombre)) {
        $errores[] = "El nombre es requerido y solo puede contener letras y espacios.";
    }
    if (empty($apellidos) || !preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $apellidos)) {
        $errores[] = "Los apellidos son requeridos y solo pueden contener letras y espacios.";
    }
    if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico es inválido.";
    }

    if (!empty($nueva_contrasena)) {
        if (strlen($nueva_contrasena) < 8) {
            $errores[] = "La nueva contraseña debe tener al menos 8 caracteres.";
        }
        if (!preg_match("/[A-Z]/", $nueva_contrasena)) {
            $errores[] = "La nueva contraseña debe contener al menos una letra mayúscula.";
        }
        if (!preg_match("/[0-9]/", $nueva_contrasena)) {
            $errores[] = "La nueva contraseña debe contener al menos un número.";
        }
        if ($nueva_contrasena !== $confirmar_contrasena) {
            $errores[] = "Las contraseñas no coinciden.";
        }
    }

    if (!empty($errores)) {
        $mensaje = implode(" ", $errores);
        $clase_mensaje = "error";
    } else {
        $sql_check_email = "SELECT id FROM usuarios WHERE email = ? AND id != ?";
        $stmt_check_email = $conn->prepare($sql_check_email);
        if ($stmt_check_email) {
            $stmt_check_email->bind_param("si", $correo, $empleado_id);
            $stmt_check_email->execute();
            $stmt_check_email->store_result();
            if ($stmt_check_email->num_rows > 0) {
                $mensaje = "El correo electrónico ya está registrado por otro empleado.";
                $clase_mensaje = "error";
            } else {
                $sql_update = "UPDATE usuarios SET nombre = ?, apellidos = ?, email = ?, rol = ?";
                $params = [$nombre, $apellidos, $correo, $rol];
                $param_types = "ssss";

                if (!empty($nueva_contrasena)) {
                    $hashed_password = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
                    $sql_update .= ", password = ?";
                    $params[] = $hashed_password;
                    $param_types .= "s";
                }

                $sql_update .= " WHERE id = ?";
                $params[] = $empleado_id;
                $param_types .= "i";

                $stmt_update = $conn->prepare($sql_update);
                if ($stmt_update) {
                    $stmt_update->bind_param($param_types, ...$params);
                    if ($stmt_update->execute()) {
                        $mensaje = "Empleado actualizado correctamente.";
                        $clase_mensaje = "success";
                        $empleado['nombre'] = $nombre;
                        $empleado['apellidos'] = $apellidos;
                        $empleado['email'] = $correo;
                        $empleado['rol'] = $rol;
                    } else {
                        $mensaje = "Error al actualizar el empleado: " . $stmt_update->error;
                        $clase_mensaje = "error";
                    }
                    $stmt_update->close();
                } else {
                    $mensaje = "Error al preparar la consulta de actualización: " . $conn->error;
                    $clase_mensaje = "error";
                }
            }
            $stmt_check_email->close();
        } else {
            $mensaje = "Error al preparar la consulta de verificación de correo: " . $conn->error;
            $clase_mensaje = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empleado | Dulcería El Pingüinito</title>
    <link rel="stylesheet" href="/DULCERIAV2/css/app.css">
    <link rel="stylesheet" href="/DULCERIAV2/css/module-layout.css">
    <style>
        .edit-panel {
            max-width: 820px;
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
            <li><a href="/DULCERIAV2/pages/inventario.php">Gestión de Inventarios</a></li>
            <li><a href="/DULCERIAV2/pages/analisis_finaciero.php">Análisis Financiero</a></li>
            <li><a class="active" href="/DULCERIAV2/pages/administrar_empleados.php">Administrar Empleados</a></li>
            <li><a href="/DULCERIAV2/pages/punto_venta.php">Punto de Venta</a></li>
        </ul>
        <ul class="sidebar-footer">
            <li><a href="/DULCERIAV2/actions/logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <main class="layout">
        <div class="page-header">
            <div>
                <h1>Editar Empleado</h1>
                <p>Actualiza los datos de acceso y perfil del empleado seleccionado.</p>
            </div>
            <a class="btn btn-primary" href="/DULCERIAV2/pages/administrar_empleados.php">Volver a empleados</a>
        </div>

        <?php if ($mensaje) : ?>
            <div class="message <?php echo htmlspecialchars($clase_mensaje); ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <?php if ($empleado) : ?>
            <section class="panel edit-panel">
                <div class="panel-header">
                    <h2><?php echo htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellidos']); ?></h2>
                </div>
                <form class="form-grid" action="/DULCERIAV2/pages/editar_empleado.php?id=<?php echo $empleado['id']; ?>" method="POST" style="padding: 18px;">
                    <input type="text" name="nombre" id="nombre" placeholder="Nombre" value="<?php echo htmlspecialchars($empleado['nombre']); ?>" required>
                    <input type="text" name="apellidos" id="apellidos" placeholder="Apellidos" value="<?php echo htmlspecialchars($empleado['apellidos']); ?>" required>
                    <input class="full" type="email" name="correo" id="correo" placeholder="Correo electrónico" value="<?php echo htmlspecialchars($empleado['email']); ?>" required>
                    <input type="password" name="nueva_contrasena" id="nueva_contrasena" placeholder="Nueva contraseña (opcional)">
                    <input type="password" name="confirmar_contrasena" id="confirmar_contrasena" placeholder="Confirmar nueva contraseña">
                    <select class="full" name="rol" id="rol" required>
                        <option value="empleado" <?php echo ($empleado['rol'] == 'empleado') ? 'selected' : ''; ?>>Empleado</option>
                        <option value="admin" <?php echo ($empleado['rol'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                    </select>
                    <button class="btn btn-primary full" type="submit" name="actualizar">Guardar cambios</button>
                </form>
            </section>
        <?php else : ?>
            <div class="message error">No se pudo cargar la información del empleado.</div>
        <?php endif; ?>
    </main>

    <script>
        function toggleMenu() {
            document.getElementById('sidebar').classList.toggle('active');
        }
    </script>
</body>
</html>
