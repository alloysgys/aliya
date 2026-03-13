<?php
// Файл: new_application.php
// Назначение: Страница создания новой заявки на обучение

session_start();

// Проберяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    // Если не авторизован, перенаправляем на страницу входа
    header('Location: login.php');
    exit();
}

// Подключаем функции для работы с заявками
require_once 'php/application_functions.php';

// Получаем список способов оплаты из БД
$payment_methods = getPaymentMethods();

// Получаем сообщения из сессии (если есть)
$error = $_SESSION['application_error'] ?? '';
$old_data = $_SESSION['old_application_data'] ?? [];

// Очищаем данные сессии
unset($_SESSION['application_error']);
unset($_SESSION['old_application_data']);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новая заявка - Корочки.есть</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
        min-height: 100vh;
    }

    .header {
        background-color: #333;
        color: white;
        padding: 15px 0;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .header-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        font-size: 24px;
        font-weight: bold;
        color: #4CAF50;
    }

    .nav {
        display: flex;
        gap: 20px;
    }

    .nav a {
        color: white;
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 5px;
        transition: background-color 0.35s;
    }

    .nav a:hover {
        background-color: #4CAF50;
    }

    .nav a.active {
        background-color: #4CAF50;
    }

    .main-content {
        max-width: 800px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .container {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        padding: 40px;
    }

    h1 {
        color: #333;
        margin-bottom: 30px;
        font-size: 28px;
        text-align: center;
    }

    .form-group {
        margin-bottom: 25px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: #555;
        font-weight: bold;
        font-size: 14px;
    }

    input[type="text"],
    input[type="date"],
    select {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        transition: border-color 0.35s;
    }

    input[type="text"]:focus,
    input[type="date"]:focus,
    select:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 5px rgba(76, 175, 80, 0.2);
    }

    .radio-group {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
        padding: 10px 0;
    }

    .radio-option {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .radio-option input[type="radio"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .radio-option label {
        margin-bottom: 0;
        cursor: pointer;
        font-weight: normal;
    }

    .btn-submit {
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

    .btn-submit:hover {
        background-color: #45a049;
    }

    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 25px;
        font-size: 14px;
        border: 1px solid #f5c6cb;
    }

    .info-text {
        color: #666;
        font-size: 14px;
        margin-top: 20px;
        text-align: center;
    }

    .info-text a {
        color: #4CAF50;
        text-decoration: none;
    }

    .info-text a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">Корочки.есть</div>
            <div class="nav">
                <a href="my_applications.php">Мои заявки</a>
                <a href="new_application.php" class="active">Новая заявка</a>
                <a href="logout.php">Выход</a>
            </div>
        </div>
    </div>
    <div class="main-content">
        <div class="container">
            <h1>Создание новой заявки на обучение</h1>
            <?php if (!empty($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            <form action="application_handler.php" method="POST">
                <div class="form-group">
                    <label for="course_name">Название курса *</label>
                    <input type="text" id="course_name" name="course_name" required placeholder="Введите название курса"
                        value="<?php echo htmlspecialchars($old_data['course_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="start_date">Желаемая дата начала обучения *</label>
                    <input type="date" id="start_date" name="start_date" required
                        value="<?php echo htmlspecialchars($old_data['start_date'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Способ оплаты *</label>
                    <div class="radio-group">
                        <?php foreach ($payment_methods as $method): ?>
                        <div class="radio-option">
                            <input type="radio" id="payment_<?php echo $method['id']; ?>" name="payment_method"
                                value="<?php echo $method['id']; ?>"
                                <?php echo (isset($old_data['payment_method']) && $old_data['payment_method'] == $method['id']) ? 'checked' : ''; ?>
                                required>
                            <label for="payment_<?php echo $method['id']; ?>">
                                <?php echo htmlspecialchars($method['name']); ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="submit" class="btn-submit">Отправить заявку</button>
            </form>
            <div class="info-text">
                <a href="my_applications.php">Вернуться к списку моих заявок</a>
            </div>
        </div>
    </div>
</body>

</html>