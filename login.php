<?php
// Файл: login.php
// Назначение: Страница авторизации с отображением ошибок

session_start();

// Получаем сообщения из ceccuu
$error = $_SESSION['login_error'] ?? '';
$success = $_SESSION['registration_success'] ?? '';

// Очищаем данные ceccuu
unset($_SESSION['login_error']);
unset($_SESSION['registration_success']);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Корочки.есть</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
    }

    .container {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        padding: 40px;
        width: 100%;
        max-width: 400px;
    }

    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 30px;
        font-size: 24px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: #555;
        font-weight: bold;
        font-size: 14px;
    }

    input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        transition: border-color 0.3s;
    }

    input:focus {
        outline: none;
        border-color: #2196F3;
        box-shadow: 0 0 5px rgba(33, 150, 243, 0.2);
    }

    .btn-login {
        width: 100%;
        padding: 14px;
        background-color: #2196F3;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-login:hover {
        background-color: #1976D2;
    }

    .register-link {
        text-align: center;
        margin-top: 25px;
        color: #666;
    }

    .register-link a {
        color: #2196F3;
        text-decoration: none;
        font-weight: bold;
    }

    .register-link a:hover {
        text-decoration: underline;
    }

    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 12px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 14px;
        text-align: center;
    }

    .success-message {
        background-color: #d4edda;
        color: #155724;
        padding: 12px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 14px;
        text-align: center;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Вход в систему "Корочки.есть"</h1>

        <?php if (!empty($success)): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form action="login_handler.php" method="POST" id="login-form">
            <div class="form-group">
                <label for="login">Логин</label>
                <input type="text" id="login" name="login" required placeholder="Введите ваш логин">
            </div>

            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required placeholder="Введите ваш пароль">
            </div>

            <button type="submit" class="btn-login">Войти</button>
        </form>

        <div class="register-link">
            Еще не зарегистрированы? <a href="register.php">Создать аккаунт</a>
        </div>
    </div>
</body>

</html>