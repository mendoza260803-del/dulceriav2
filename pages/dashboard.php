<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id_usuario'])) {
    header("Location: /DULCERIAV2/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel | Dulcería El Pingüinito</title>
    <link rel="stylesheet" href="/DULCERIAV2/css/app.css">
    <style>
        :root {
            --pink: #f06aa6;
            --pink-dark: #b82f72;
            --ink: #3b2330;
            --muted: #7d6370;
            --surface: #ffffff;
            --soft: #fff2f7;
            --line: #f3c7d9;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: var(--ink);
            background: #fff8fb;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 18px;
            gap: 10px;
        }

        .logo img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
        }

        main {
            width: min(1480px, calc(100% - 64px));
            max-width: none;
            margin: 0 auto;
            padding: 34px 0 48px;
        }

        .welcome {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            margin-bottom: 28px;
            padding: 24px;
            border: 1px solid #f1d4df;
            border-radius: 8px;
            background:
                linear-gradient(135deg, rgba(255, 242, 247, 0.96), rgba(255, 255, 255, 0.9)),
                #fff;
            box-shadow: 0 18px 40px rgba(184, 47, 114, 0.1);
        }

        .welcome h1 {
            font-size: clamp(28px, 5vw, 44px);
            line-height: 1.05;
            margin: 0 0 8px;
            color: var(--pink-dark);
        }

        .welcome p {
            margin: 0;
            color: var(--muted);
        }

        .welcome-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .welcome-brand img {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 10px 24px rgba(184, 47, 114, 0.15);
        }

        .welcome-brand span {
            display: block;
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
        }

        .welcome-brand strong {
            display: block;
            color: var(--pink-dark);
            font-size: 20px;
        }

        .welcome-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 14px;
        }

        .quick-stats {
            display: grid;
            grid-template-columns: minmax(130px, 1fr);
            gap: 10px;
            min-width: 170px;
        }

        .quick-stat {
            padding: 12px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.68);
            border: 1px solid #f3c7d9;
        }

        .quick-stat strong {
            display: block;
            color: var(--pink-dark);
            font-size: 18px;
        }

        .quick-stat span {
            color: var(--muted);
            font-size: 13px;
        }

        .logout-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 10px 16px;
            border-radius: 8px;
            background: linear-gradient(90deg, #f06aa6, #c9c9cf);
            color: #fff;
            text-decoration: none;
            font-weight: 800;
            box-shadow: 0 12px 26px rgba(184, 47, 114, 0.18);
            white-space: nowrap;
        }

        .logout-button:hover {
            filter: brightness(1.03);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .module-card {
            display: flex;
            flex-direction: column;
            min-height: 210px;
            background: var(--surface);
            border: 1px solid #f1d4df;
            border-radius: 8px;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 18px 40px rgba(184, 47, 114, 0.11);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .module-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 22px 48px rgba(184, 47, 114, 0.18);
        }

        .module-image {
            width: 100%;
            aspect-ratio: 16 / 7.5;
            object-fit: cover;
            object-position: center;
            background: var(--soft);
        }

        .module-body {
            padding: 20px;
        }

        .module-kicker {
            display: inline-flex;
            width: fit-content;
            margin-bottom: 10px;
            padding: 6px 10px;
            border-radius: 999px;
            background: var(--soft);
            color: var(--pink-dark);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .module-card h2 {
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 22px;
        }

        .module-card p {
            margin: 0 0 18px;
            color: var(--muted);
            line-height: 1.5;
        }

        .module-action {
            margin-top: auto;
            color: var(--pink-dark);
            font-weight: 800;
        }

        .module-card.simple {
            justify-content: space-between;
        }

        .module-card.simple .module-body {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        @media (max-width: 850px) {
            main {
                width: min(100% - 28px, 1480px);
                padding-top: 22px;
            }

            .welcome {
                align-items: start;
                flex-direction: column;
            }

            .welcome-actions {
                align-items: stretch;
                width: 100%;
            }

            .quick-stats {
                min-width: 0;
                width: 100%;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main id="main-content">
        <div class="welcome">
            <div>
                <div class="welcome-brand">
                    <img src="/DULCERIAV2/assets/logo.png" alt="Logo">
                    <div>
                        <span>Dulcería</span>
                        <strong>El Pingüinito</strong>
                    </div>
                </div>
                <h1>Hola, <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
                <p>Administra las ventas, inventario y equipo desde un solo lugar.</p>
            </div>
            <div class="welcome-actions">
                <div class="quick-stats">
                    <div class="quick-stat">
                        <strong><?php echo htmlspecialchars($_SESSION['rol']); ?></strong>
                        <span>Perfil actual</span>
                    </div>
                </div>
                <a class="logout-button" href="/DULCERIAV2/actions/logout.php">Cerrar Sesión</a>
            </div>
        </div>

        <div class="dashboard-grid">
            <?php if ($_SESSION['rol'] === 'admin') : ?>
                <a class="module-card simple" href="/DULCERIAV2/pages/inventario.php">
                    <img class="module-image" src="/DULCERIAV2/assets/gestion_inventario.png" alt="Gestión de inventarios">
                    <div class="module-body">
                        <span class="module-kicker">Inventario</span>
                        <h2>Gestión de Inventarios</h2>
                        <p>Registra nuevos productos y actualiza la información existente.</p>
                        <span class="module-action">Abrir inventario</span>
                    </div>
                </a>

                <a class="module-card simple" href="/DULCERIAV2/pages/analisis_finaciero.php">
                    <img class="module-image" src="/DULCERIAV2/assets/ventas.png" alt="Análisis financiero de ventas">
                    <div class="module-body">
                        <span class="module-kicker">Ventas</span>
                        <h2>Análisis Financiero</h2>
                        <p>Consulta fechas y ventas por períodos.</p>
                        <span class="module-action">Ver análisis</span>
                    </div>
                </a>
            <?php endif; ?>

            <a class="module-card simple" href="/DULCERIAV2/pages/punto_venta.php">
                <img class="module-image" src="/DULCERIAV2/assets/caja.png" alt="Punto de venta">
                <div class="module-body">
                    <span class="module-kicker">Caja</span>
                    <h2>Punto de Venta</h2>
                    <p>Registra las ventas diarias.</p>
                    <span class="module-action">Ir a ventas</span>
                </div>
            </a>

            <?php if ($_SESSION['rol'] === 'admin') : ?>
                <a class="module-card simple" href="/DULCERIAV2/pages/administrar_empleados.php">
                    <img class="module-image" src="/DULCERIAV2/assets/equipo.png" alt="Administración de empleados">
                    <div class="module-body">
                        <span class="module-kicker">Equipo</span>
                        <h2>Administrar Empleados</h2>
                        <p>Gestiona los empleados de la dulcería.</p>
                        <span class="module-action">Administrar</span>
                    </div>
                </a>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>
