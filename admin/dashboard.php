<?php
// Файл: admin/dashboard.php
// Назначение: Панель администратора для управления заявками

session_start();

// Проверяем, авторизован ли пользователь и является ли администратором
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Подключаем функции для работы с заявками
require_once '../php/application_functions.php';

// Получаем все заявки
$applications = getAllApplications();

// Получаем сообщения из сессии
$success = $_SESSION['admin_success'] ?? '';
$error = $_SESSION['admin_error'] ?? '';

// Очищаем данные сессии
unset($_SESSION['admin_success']);
unset($_SESSION['admin_error']);

// Массив статусов для выпадающего списка
$statuses = ['Новая', 'Идет обучение', 'Обучение завершено'];
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора - Корочки.есть</title>
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
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        font-size: 24px;
        font-weight: bold;
        color: #dc3545;
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

    .nav a:hover {
        background-color: #dc3545;
    }

    .nav a.active {
        background-color: #dc3545;
    }

    .main-content {
        max-width: 1400px;
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

    .stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .stat-value {
        font-size: 32px;
        font-weight: bold;
        color: #333;
    }

    .stat-label {
        color: #666;
        margin-top: 5px;
    }

    .filters {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-group label {
        display: block;
        margin-bottom: 5px;
        color: #555;
        font-weight: bold;
        font-size: 14px;
    }

    .filter-group select,
    .filter-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }

    .btn-filter {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        align-self: flex-end;
    }

    .btn-filter:hover {
        background-color: #c82333;
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
        background-color: #dc3545;
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

    .status-form {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .status-select {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 5px;
        flex: 1;
        min-width: 120px;
    }

    .btn-update {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 13px;
        transition: background-color 0.3s;
    }

    .btn-update:hover {
        background-color: #218838;
    }

    .review-text {
        max-width: 200px;
        font-size: 13px;
        color: #555;
        font-style: italic;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 30px;
    }

    .pagination a {
        padding: 10px 15px;
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        text-decoration: none;
        color: #333;
        transition: background-color 0.3s;
    }

    .pagination a:hover {
        background-color: #dc3545;
        color: white;
        border-color: #dc3545;
    }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">Корочки.есть (Админ-панель)</div>
            <div class="nav">
                <a href="../my_applications.php">Мои заявки</a>
                <a href="../new_application.php">Новая заявка</a>
                <a href="dashboard.php" class="active">Панель администратора</a>
                <a href="../logout.php">Выход</a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <h1>Управление заявками на обучение</h1>

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

        <?php
        // Подсчет статистики
        $total = count($applications);
        $new = 0;
        $in_progress = 0;
        $completed = 0;
        
        foreach ($applications as $app) {
            if ($app['status'] === 'Новая') $new++;
            elseif ($app['status'] === 'Идет обучение') $in_progress++;
            elseif ($app['status'] === 'Обучение завершено') $completed++;
        }
        ?>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-value"><?php echo $total; ?></div>
                <div class="stat-label">Всего заявок</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $new; ?></div>
                <div class="stat-label">Новых</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $in_progress; ?></div>
                <div class="stat-label">В процессе</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $completed; ?></div>
                <div class="stat-label">Завершено</div>
            </div>
        </div>

        <div class="filters">
            <div class="filter-group">
                <label for="status-filter">Фильтр по статусу</label>
                <select id="status-filter" onchange="filterByStatus(this.value)">
                    <option value="all">Все статусы</option>
                    <option value="Новая">Новые</option>
                    <option value="Идет обучение">В процессе</option>
                    <option value="Обучение завершено">Завершенные</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="search">Поиск по ФИО или курсу</label>
                <input type="text" id="search" placeholder="Введите текст для поиска..."
                    onkeyup="searchTable(this.value)">
            </div>
            <button class="btn-filter" onclick="resetFilters()">Сбросить фильтры</button>
        </div>

        <?php if (empty($applications)): ?>
        <div style="text-align: center; padding: 40px; background-color: white; border-radius: 10px;">
            <p>В системе пока нет ни одной заявки</p>
        </div>
        <?php else: ?>
        <div class="applications-table">
            <table id="applications-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Пользователь</th>
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
                    <?php foreach ($applications as $app): ?>
                    <?php
                            $status_class = '';
                            if ($app['status'] === 'Новая') {
                                $status_class = 'status-new';
                            } elseif ($app['status'] === 'Идет обучение') {
                                $status_class = 'status-in-progress';
                            } elseif ($app['status'] === 'Обучение завершено') {
                                $status_class = 'status-completed';
                            }
                            ?>
                    <tr data-status="<?php echo $app['status']; ?>">
                        <td><?php echo $app['id']; ?></td>
                        <td>
                            <?php echo htmlspecialchars($app['user_name']); ?><br>
                            <small style="color: #666;"><?php echo htmlspecialchars($app['user_login']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($app['course_name']); ?></td>
                        <td><?php echo date('d.m.Y', strtotime($app['desired_start_date'])); ?></td>
                        <td><?php echo htmlspecialchars($app['payment_method_name']); ?></td>
                        <td>
                            <span class="status <?php echo $status_class; ?>">
                                <?php echo htmlspecialchars($app['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d.m.Y H:i', strtotime($app['created_at'])); ?></td>
                        <td class="review-text">
                            <?php
                                    if (!empty($app['review'])) {
                                        echo htmlspecialchars($app['review']);
                                    } else {
                                        echo '—';
                                    }
                                    ?>
                        </td>
                        <td>
                            <form action="update_status.php" method="POST" class="status-form">
                                <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                <select name="status" class="status-select">
                                    <?php foreach ($statuses as $status): ?>
                                    <option value="<?php echo $status; ?>"
                                        <?php echo ($app['status'] === $status) ? 'selected' : ''; ?>>
                                        <?php echo $status; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn-update">Обновить</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <script>
    function filterByStatus(status) {
        const table = document.getElementById('applications-table');
        if (!table) return;

        const tbody = table.getElementsByTagName('tbody')[0];
        const rows = tbody.getElementsByTagName('tr');

        for (let row of rows) {
            if (status === 'all') {
                row.style.display = '';
            } else {
                const rowStatus = row.getAttribute('data-status');
                if (rowStatus === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }
    }

    function searchTable(searchText) {
        const table = document.getElementById('applications-table');
        if (!table) return;

        const tbody = table.getElementsByTagName('tbody')[0];
        const rows = tbody.getElementsByTagName('tr');
        searchText = searchText.toLowerCase();

        for (let row of rows) {
            const userName = row.cells[1].textContent.toLowerCase();
            const courseName = row.cells[2].textContent.toLowerCase();

            if (userName.includes(searchText) || courseName.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }

    function resetFilters() {
        document.getElementById('status-filter').value = 'all';
        document.getElementById('search').value = '';
        filterByStatus('all');
    }
    </script>
</body>

</html>