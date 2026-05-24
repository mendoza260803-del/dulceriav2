<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $captchaValido = true;

    /*
    // reCAPTCHA v3 desactivado para la version demo.
    // Para activarlo, coloca tu clave secreta y descomenta este bloque.
    $recaptchaResponse = $_POST['recaptcha_response'] ?? '';
    $secretKey = 'TU_SECRET_KEY';

    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $secretKey,
        'response' => $recaptchaResponse,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $responseData = json_decode($response);
    $captchaValido = isset($responseData->success) && $responseData->success && $responseData->score >= 0.5;
    */

    if ($captchaValido) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, nombre, rol, password FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['usuario'] = $user['nombre'];
                $_SESSION['rol'] = $user['rol'];
                $_SESSION['id_usuario'] = $user['id'];
                header("Location: /DULCERIAV2/pages/dashboard.php");
                exit();
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "Correo electrónico no encontrado.";
        }
        $stmt->close();
    } else {
        $error = "reCAPTCHA no válido. Inténtalo de nuevo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Dulcería El Pingüinito</title>
    <link rel="stylesheet" href="/DULCERIAV2/css/styles.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- reCAPTCHA v3 desactivado para la version demo.
    Para activarlo, reemplaza TU_SITE_KEY y descomenta esta linea:
    <script src="https://www.google.com/recaptcha/api.js?render=TU_SITE_KEY"></script>
    -->
</head>
<body>
    <main class="login-container">
        <img src="/DULCERIAV2/assets/logo.png" alt="Logo de Dulcería El Pingüinito" class="logo">
        <h1>Dulcería "El Pingüinito"</h1>
        <p class="login-subtitle">Acceso al sistema</p>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="email">Correo</label>
            <input type="email" id="email" name="email" autocomplete="email" required>

            <label for="password">Contraseña</label>
            <div class="password-field">
                <input type="password" id="password" name="password" autocomplete="current-password" required>
                <i class="far fa-eye show-password" id="eye-icon" onclick="togglePasswordVisibility()"></i>
            </div>

            <!-- reCAPTCHA v3 desactivado para la version demo.
            Para activarlo, descomenta este campo:
            <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
            -->
            <button type="submit">Acceder</button>
        </form>
    </main>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById("password");
            const eyeIcon = document.getElementById("eye-icon");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            }
        }

        /*
        // reCAPTCHA v3 desactivado para la version demo.
        // Para activarlo, reemplaza TU_SITE_KEY y descomenta este bloque.
        grecaptcha.ready(function() {
            grecaptcha.execute('TU_SITE_KEY', {action: 'login'}).then(function(token) {
                document.getElementById('recaptchaResponse').value = token;
            });
        });
        */
    </script>
</body>
</html>
