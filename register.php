<?php
// Файл: register.php
// Назначение: Страница регистрации с отображением ошибок валидации

session_start();

// Получаем ошибки из сессии, если они есть
$errors = $_SESSION['registration_errors'] ?? [];
$old_data = $_SESSION['old_data'] ?? [];

// Очищаем данные сессии после получения
unset($_SESSION['registration_errors']);
unset($_SESSION['old_data']);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Корочки.есть</title>
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
        max-width: 500px;
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
        border-color: #4CAF50;
        box-shadow: 0 0 5px rgba(76, 175, 80, 0.2);
    }

    .btn-register {
        width: 100%;
        padding: 14px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-register:hover {
        background-color: #45a049;
    }

    .login-link {
        text-align: center;
        margin-top: 25px;
        color: #666;
    }

    .login-link a {
        color: #4CAF50;
        text-decoration: none;
        font-weight: bold;
    }

    .login-link a:hover {
        text-decoration: underline;
    }

    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 12px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .error-message ul {
        margin-left: 20px;
    }

    .error-message li {
        margin-bottom: 5px;
    }

    .hint {
        color: #888;
        font-size: 12px;
        margin-top: 5px;
        font-style: italic;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Регистрация на портале "Корочки.есть"</h1>

        <?php if (!empty($errors)): ?>
        <div class="error-message">
            <strong>Ошибки при заполнении формы:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form action="register_handler.php" method="POST" id="registration-form">
            <div class="form-group">
                <label for="login">Логин *</label>
                <input type="text" id="login" name="login" required
                    placeholder="Только латиница и цифры, не менее 6 символов"
                    value="<?php echo htmlspecialchars($old_data['login'] ?? ''); ?>">
                <div class="hint">Только латиница и цифры, минимум 6 символов</div>
            </div>

            <div class="form-group">
                <label for="password">Пароль *</label>
                <input type="password" id="password" name="password" required placeholder="Минимум 8 символов">
                <div class="hint">Минимум 8 символов</div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Подтверждение пароля *</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                    placeholder="Повторите пароль">
            </div>

            <div class="form-group">
                <label for="full_name">ФИО *</label>
                <input type="text" id="full_name" name="full_name" required placeholder="Иванов Иван Иванович"
                    value="<?php echo htmlspecialchars($old_data['full_name'] ?? ''); ?>">
                <div class="hint">Только кириллица и пробелы</div>
            </div>

            <div class="form-group">
                <label for="phone">Телефон *</label>
                <input type="tel" id="phone" name="phone" required placeholder="8(999)123-45-67"
                    value="<?php echo htmlspecialchars($old_data['phone'] ?? ''); ?>">
                <div class="hint">Формат: 8(XXX)XXX-XX-XX</div>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required placeholder="example@mail.ru"
                    value="<?php echo htmlspecialchars($old_data['email'] ?? ''); ?>">
                <div class="hint">Введите корректный email адрес</div>
            </div>

            <button type="submit" class="btn-register">Зарегистрироваться</button>
        </form>

        <div class="login-link">
            Уже есть аккаунт? <a href="login.php">Войти в систему</a>
        </div>
    </div>
    <script>
    // Простая клиентская валидация для удобства пользователей
    document.getElementById('registration-form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirm_password').value;

        if (password !== confirm) {
            e.preventDefault();
            alert('Пароли не совпадают!');
        }
    });
    </script>
</body>

</html>