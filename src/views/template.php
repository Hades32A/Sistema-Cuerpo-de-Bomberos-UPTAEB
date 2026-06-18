<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión - Cuerpo de Bomberos</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body class="app">
    <div class="app-shell">
        <?php require __DIR__ . '/components/sidebar.php'; ?>

        <div class="app-main">
            <?php require __DIR__ . '/components/header.php'; ?>

            <main class="app-content" id="main-content" role="main">
                <?= $contenidoModulo ?? '' ?>
            </main>
        </div>
    </div>

    <script src="public/js/main.js" defer></script>
</body>
</html>
