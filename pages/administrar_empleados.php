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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar'])) {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);
    $rol = $_POST['rol'];

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
    if (empty($contrasena)) {
        $errores[] = "La contraseña es requerida.";
    } else {
        if (strlen($contrasena) < 8) {
            $errores[] = "La contraseña debe tener al menos 8 caracteres.";
        }
        if (!preg_match("/[A-Z]/", $contrasena)) {
            $errores[] = "La contraseña debe contener al menos una letra mayúscula.";
        }
        if (!preg_match("/[0-9]/", $contrasena)) {
            $errores[] = "La contraseña debe contener al menos un número.";
        }
    }

    if (!empty($errores)) {
        $mensaje = implode(" ", $errores);
        $clase_mensaje = "error";
    } else {
        $sql_check_email = "SELECT id FROM usuarios WHERE email = ?";
        $stmt_check_email = $conn->prepare($sql_check_email);
        if ($stmt_check_email) {
            $stmt_check_email->bind_param("s", $correo);
            $stmt_check_email->execute();
            $stmt_check_email->store_result();
            if ($stmt_check_email->num_rows > 0) {
                $mensaje = "El correo electrónico ya está registrado.";
                $clase_mensaje = "error";
            } else {
                $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);
                $sql_insert = "INSERT INTO usuarios (nombre, apellidos, email, password, rol) VALUES (?, ?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                if ($stmt_insert) {
                    $stmt_insert->bind_param("sssss", $nombre, $apellidos, $correo, $hashed_password, $rol);
                    if ($stmt_insert->execute()) {
                        $mensaje = "Empleado registrado correctamente.";
                        $clase_mensaje = "success";
                    } else {
                        $mensaje = "Error al registrar el empleado: " . $stmt_insert->error;
                        $clase_mensaje = "error";
                    }
                    $stmt_insert->close();
                } else {
                    $mensaje = "Error en la preparación de la consulta de inserción: " . $conn->error;
                    $clase_mensaje = "error";
                }
            }
            $stmt_check_email->close();
        } else {
            $mensaje = "Error en la preparación de la consulta de verificación de correo: " . $conn->error;
            $clase_mensaje = "error";
        }
    }
}

if (isset($_GET['eliminar'])) {
    $empleado_id_eliminar = intval($_GET['eliminar']);
    if ($empleado_id_eliminar > 0) {
        $sql_eliminar = "DELETE FROM usuarios WHERE id = ?";
        $stmt_eliminar = $conn->prepare($sql_eliminar);
        if ($stmt_eliminar) {
            $stmt_eliminar->bind_param("i", $empleado_id_eliminar);
            if ($stmt_eliminar->execute()) {
                $mensaje = "Empleado eliminado correctamente.";
                $clase_mensaje = "success";
            } else {
                $mensaje = "Error al eliminar el empleado: " . $stmt_eliminar->error;
                $clase_mensaje = "error";
            }
            $stmt_eliminar->close();
        } else {
            $mensaje = "Error al preparar la consulta de eliminación: " . $conn->error;
            $clase_mensaje = "error";
        }
    }
}

$sql_select_empleados = "SELECT id, nombre, apellidos, email, rol FROM usuarios";
$result_empleados = $conn->query($sql_select_empleados);
$empleados = [];
if ($result_empleados && $result_empleados->num_rows > 0) {
    while ($row = $result_empleados->fetch_assoc()) {
        $empleados[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Empleados | Dulcería El Pingüinito</title>
    <link rel="stylesheet" href="/DULCERIAV2/css/app.css">
    <link rel="stylesheet" href="/DULCERIAV2/css/module-layout.css">
    <style>
        .employee-grid {
            display: grid;
            grid-template-columns: minmax(340px, 0.7fr) minmax(0, 1.3fr);
            gap: 18px;
            align-items: start;
        }

        .form-panel {
            padding: 18px;
        }

        .password-field {
            position: relative;
        }

        .show-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--muted);
        }

        @media (max-width: 1100px) {
            .employee-grid {
                grid-template-columns: 1fr;
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
                <h1>Administrar Empleados</h1>
                <p>Hola, <?php echo htmlspecialchars($_SESSION['usuario']); ?>. Registra y gestiona cuentas del equipo.</p>
            </div>
        </div>

        <?php if ($mensaje) : ?>
            <div class="message <?php echo htmlspecialchars($clase_mensaje); ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <div class="employee-grid">
            <section class="panel form-panel">
                <h2>Registrar empleado</h2>
                <form class="form-grid" action="/DULCERIAV2/pages/administrar_empleados.php" method="POST" style="margin-top: 16px;">
                    <input type="text" name="nombre" placeholder="Nombre" required>
                    <input type="text" name="apellidos" placeholder="Apellidos" required>
                    <input class="full" type="email" name="correo" placeholder="Correo electrónico" required>
                    <div class="password-field full">
                        <input type="password" id="contrasena" name="contrasena" placeholder="Contraseña" required>
                        <span class="show-password" id="eye-icon" onclick="togglePasswordVisibility()">
                            <i class="far fa-eye"></i>
                        </span>
                    </div>
                    <select class="full" name="rol" required>
                        <option value="empleado">Empleado</option>
                        <option value="admin">Administrador</option>
                    </select>
                    <button class="btn btn-primary full" type="submit" name="registrar">Registrar</button>
                </form>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <h2>Lista de empleados</h2>
                </div>
                <?php if (empty($empleados)) : ?>
                    <div style="padding: 18px; color: var(--muted);">No se encontraron empleados.</div>
                <?php else : ?>
                    <div class="table-wrap">
                        <table class="module-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Apellidos</th>
                                    <th>Correo</th>
                                    <th>Rol</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($empleados as $empleado) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($empleado['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($empleado['apellidos']); ?></td>
                                        <td><?php echo htmlspecialchars($empleado['email']); ?></td>
                                        <td><?php echo htmlspecialchars($empleado['rol']); ?></td>
                                        <td class="actions-cell">
                                            <button class="btn btn-edit" onclick="editarEmpleado(<?php echo $empleado['id']; ?>)">Editar</button>
                                            <button class="btn btn-delete" onclick="eliminarEmpleado(<?php echo $empleado['id']; ?>)">Eliminar</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <script>
        function toggleMenu() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        function editarEmpleado(id) {
            window.location.href = "/DULCERIAV2/pages/editar_empleado.php?id=" + id;
        }

        function eliminarEmpleado(id) {
            if (confirm("¿Estás seguro de eliminar este empleado?")) {
                window.location.href = "/DULCERIAV2/pages/administrar_empleados.php?eliminar=" + id;
            }
        }

        function togglePasswordVisibility() {
            const contrasenaInput = document.getElementById('contrasena');
            const eyeIcon = document.querySelector('#eye-icon i');
            if (contrasenaInput.type === 'password') {
                contrasenaInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                contrasenaInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</body>
</html>
