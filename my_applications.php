<?php
// Файл: my_applications.php
// Назначение: Страница просмотра заявок текущего пользователя

session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Подключаем функции для работы с заявками
require_once 'php/application_functions.php';
require_once 'php/user_functions.php';

// Получаем данные пользователя
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? 'user';

// Получаем заявки пользователя
$applications = getUserApplications($user_id);

// Получаем сообщения из сессии
$success = $_SESSION['application_success'] ?? '';
$error = $_SESSION['application_error'] ?? '';

// Очищаем данные сессии
unset($_SESSION['application_success'], $_SESSION['application_error']);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заявки - Корочки.есть</title>
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
        transition: background-color 0.3s;
    }

    .nav a:hover,
    .nav a.active {
        background-color: #4CAF50;
    }

    .main-content {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }

    h1 {
        color: #333;
        margin-bottom: 30px;
        font-size: 28px;
    }

    .success-message {
        background-color: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 25px;
        border: 1px solid #c3e6cb;
    }

    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 25px;
        border: 1px solid #f5c6cb;
    }

    .applications-table {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background-color: #4CAF50;
        color: white;
        padding: 15px;
        text-align: left;
        font-weight: bold;
    }

    td {
        padding: 15px;
        border-bottom: 1px solid #ddd;
    }

    tr:hover {
        background-color: #f5f5f5;
    }

    .status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: bold;
        text-align: center;
    }

    .status-new {
        background-color: #ffc107;
        color: #333;
    }

    .status-in-progress {
        background-color: #17a2b8;
        color: white;
    }

    .status-completed {
        background-color: #28a745;
        color: white;
    }

    .review-text {
        max-width: 200px;
        font-size: 13px;
        color: #555;
        font-style: italic;
    }

    .btn-review,
    .btn-cancel {
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 13px;
        transition: background-color 0.3s;
    }

    .btn-review {
        background-color: #28a745;
        color: white;
    }

    .btn-review:hover {
        background-color: #218838;
    }

    .btn-cancel {
        background-color: #6c757d;
        color: white;
    }

    .btn-cancel:hover {
        background-color: #5a6268;
    }

    .review-form {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 5px;
    }

    .review-input {
        flex: 1;
        min-width: 150px;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 13px;
    }

    .no-applications {
        text-align: center;
        padding: 40px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .no-applications p {
        color: #666;
        margin-bottom: 20px;
        font-size: 18px;
    }

    .btn-create {
        display: inline-block;
        background-color: #4CAF50;
        color: white;
        text-decoration: none;
        padding: 12px 30px;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s;
    }

    .btn-create:hover {
        background-color: #45a049;
    }

    .admin-link {
        margin-bottom: 20px;
    }

    .admin-link a {
        background-color: #dc3545;
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 5px;
        display: inline-block;
        transition: background-color 0.3s;
    }

    .admin-link a:hover {
        background-color: #c82333;
    }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">Корочки.есть</div>
            <div class="nav">
                <a href="my_applications.php" class="active">Мои заявки</a>
                <a href="new_application.php">Новая заявка</a>
                <?php if ($user_role == 'admin'): ?>
                <a href="admin/dashboard.php">Панель администратора</a>
                <?php endif; ?>
                <a href="logout.php">Выход</a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <?php if ($user_role == 'admin'): ?>
        <div class="admin-link">
            <a href="admin/dashboard.php">Перейти в панель администратора</a>
        </div>
        <?php endif; ?>

        <h1>Мои заявки на обучение</h1>

        <?php if (!empty($success)): ?>
        <div class="success-message"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (empty($applications)): ?>
        <div class="no-applications">
            <p>У вас пока нет ни одной заявки на обучение</p>
            <a href="new_application.php" class="btn-create">Создать первую заявку</a>
        </div>
        <?php else: ?>
        <div class="applications-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Курс</th>
                        <th>Дата начала</th>
                        <th>Способ оплаты</th>
                        <th>Статус</th>
                        <th>Дата подачи</th>
                        <th>Отзыв</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): 
                            $status_class = match($app['status']) {
                                'Новая' => 'status-new',
                                'Идет обучение' => 'status-in-progress',
                                'Обучение завершено' => 'status-completed',
                                default => ''
                            };
                        ?>
                    <tr>
                        <td><?= $app['id'] ?></td>
                        <td><?= htmlspecialchars($app['course_name']) ?></td>
                        <td><?= date('d.m.Y', strtotime($app['desired_start_date'])) ?></td>
                        <td><?= htmlspecialchars($app['payment_method_name']) ?></td>
                        <td><span class="status <?= $status_class ?>"><?= htmlspecialchars($app['status']) ?></span>
                        </td>
                        <td><?= date('d.m.Y H:i', strtotime($app['created_at'])) ?></td>
                        <td class="review-text"><?= !empty($app['review']) ? htmlspecialchars($app['review']) : '—' ?>
                        </td>
                        <td>
                            <?php if ($app['status'] == 'Обучение завершено' && empty($app['review'])): ?>
                            <button class="btn-review" onclick="showReviewForm(<?= $app['id'] ?>)">Оставить
                                отзыв</button>
                            <div id="review-form-<?= $app['id'] ?>" style="display:none;">
                                <form action="review_handler.php" method="POST" class="review-form">
                                    <input type="hidden" name="application_id" value="<?= $app['id'] ?>">
                                    <input type="text" name="review" class="review-input" placeholder="Ваш отзыв..."
                                        maxlength="500" required>
                                    <button type="submit" class="btn-review">Отправить</button>
                                    <button type="button" class="btn-cancel"
                                        onclick="hideReviewForm(<?= $app['id'] ?>)">Отмена</button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <script>
    function showReviewForm(id) {
        document.getElementById('review-form-' + id).style.display = 'block';
    }

    function hideReviewForm(id) {
        document.getElementById('review-form-' + id).style.display = 'none';
    }
    </script>
</body>

</html>