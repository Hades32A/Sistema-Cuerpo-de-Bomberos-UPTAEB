<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cuerpo de Bomberos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body.login-page {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, #f8c8c4 0%, #dfe8e2 55%, #c8d6cf 100%);
            font-family: system-ui, -apple-system, sans-serif;
        }
        .login-card-wrap {
            position: relative;
            width: 100%;
            max-width: 420px;
            padding: 0 1rem;
        }
        .login-avatar {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: #e53935;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.4rem;
            position: absolute;
            top: -44px;
            left: 50%;
            transform: translateX(-50%);
            box-shadow: 0 8px 20px rgba(229, 57, 53, 0.35);
            z-index: 2;
        }
        .login-card {
            background: #fff;
            border: none;
            border-radius: 28px;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.12);
            padding: 3.5rem 2rem 2rem;
            margin-top: 44px;
        }
        .login-input-group {
            background: #f1f3f5;
            border-radius: 999px;
            padding: 0.35rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            margin-bottom: 1rem;
        }
        .login-input-group i {
            color: #868e96;
            font-size: 1.1rem;
        }
        .login-input-group input {
            border: none;
            background: transparent;
            width: 100%;
            padding: 0.55rem 0;
            outline: none;
        }
        .login-btn {
            background: #e53935;
            border: none;
            color: #fff;
            font-weight: 700;
            letter-spacing: 0.08em;
            border-radius: 999px;
            padding: 0.85rem;
            width: 100%;
            margin-top: 0.5rem;
        }
        .login-btn:hover {
            background: #c62828;
            color: #fff;
        }
        .login-extra {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            margin: 0.5rem 0 1.25rem;
        }
        .login-extra a {
            color: #e53935;
            text-decoration: none;
        }
        .login-title {
            text-align: center;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body class="login-page">
    <div class="login-card-wrap">
        <div class="login-avatar">
            <i class="bi bi-person-fill"></i>
        </div>
        <div class="card login-card">
            <h1 class="login-title h4">Sistema Bomberos</h1>

            <?php if (!empty($errorLogin)): ?>
                <div class="alert alert-danger py-2 small" role="alert">
                    <?= htmlspecialchars($errorLogin, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?url=login" autocomplete="on">
                <div class="login-input-group">
                    <i class="bi bi-person"></i>
                    <input type="text" name="usuario" id="usuario" placeholder="Usuario" required autocomplete="username">
                </div>
                <div class="login-input-group">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Contraseña" required autocomplete="current-password">
                </div>

                <div class="login-extra">
                    <label class="d-flex align-items-center gap-1 mb-0">
                        <input type="checkbox" name="recordarme" value="1">
                        Recordarme
                    </label>
                    <a href="#">¿Olvidó su contraseña?</a>
                </div>

                <button type="submit" class="login-btn">LOGIN</button>
            </form>
        </div>
    </div>
</body>
</html>
