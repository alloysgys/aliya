<?php
// Файл: admin/update_status.php
// Назначение: Обработчик изменения статуса заявки

session_start();

// Проверяем, авторизован ли пользователь и является ли администратором
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Подключаем функции для работы с заявками
require_once '../php/application_functions.php';

// Проверяем, была ли отправлена форма методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $application_id = $_POST['application_id'] ?? 0;
    $new_status = $_POST['status'] ?? '';

    // Валидация
    if (!$application_id || empty($new_status)) {
        $_SESSION['admin_error'] = 'Не указан ID заявки или статус';
        header('Location: dashboard.php');
        exit();
    }

    // Обновляем статус
    $result = updateApplicationStatus($application_id, $new_status);

    if ($result === true) {
        $_SESSION['admin_success'] = "Статус заявки #$application_id успешно изменён на '$new_status'";
    } else {
        $_SESSION['admin_error'] = $result['error'] ?? 'Ошибка при обновлении статуса';
    }

    header('Location: dashboard.php');
    exit();

} else {
    header('Location: dashboard.php');
    exit();
}
?>