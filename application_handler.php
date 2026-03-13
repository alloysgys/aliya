<?php
// Файл: application_handler.php
// Назначение: Обработка данных формы создания заявки

session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Подключаем функции для работы с заявками
require_once __DIR__ . '/php/application_functions.php';

// Проверяем, была ли отправлена форма методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Получаем данные из формы
    $course_name = trim($_POST['course_name'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $user_id = $_SESSION['user_id'];

    // Сохраняем введённые данные в сессию на случай ошибки
    $_SESSION['old_application_data'] = [
        'course_name' => $course_name,
        'start_date' => $start_date,
        'payment_method' => $payment_method
    ];

    // Валидация данных
    $errors = [];

    if (empty($course_name)) {
        $errors[] = 'Укажите наименование курса';
    }

    if (empty($start_date)) {
        $errors[] = 'Укажите желаемую дату начала';
    } else {
        // Проверяем, что дата не в прошлом
        $today = date('Y-m-d');
        if ($start_date < $today) {
            $errors[] = 'Дата начала не может быть в прошлом';
        }
    }
    if (empty($payment_method)) {
        $errors[] = 'Выберите способ оплаты';
    }

    // Если есть ошибки, возвращаем на форму
    if (!empty($errors)) {
        $_SESSION['application_error'] = implode('<br>', $errors);
        header('Location: new_application.php');
        exit();
    }

    // Создаём заявку
    $result = createApplication($user_id, $course_name, $start_date, $payment_method);

    if ($result === true) {
        // Успешное создание заявки
        unset($_SESSION['old_application_data']); // Очищаем сохранённые данные
        $_SESSION['application_success'] = 'Заявка успешно создана!';
        header('Location: my_applications.php');
        exit();
    } else {
        // Ошибка при создании
        $_SESSION['application_error'] = $result['error'];
        header('Location: new_application.php');
        exit();
    }

} else {
    // Если кто-то пытается зайти на обработчик напрямую
    header('Location: new_application.php');
    exit();
}
?>