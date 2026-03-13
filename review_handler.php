<?php
// Файл: review_handler.php
// Назначение: Обработка добавления отзыва к заявке

session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Подключаем функции для работы с заявками
require_once 'php/application_functions.php';

// Проверяем, была ли отправлена форма методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $application_id = $_POST['application_id'] ?? 0;
    $review = trim($_POST['review'] ?? '');
    $user_id = $_SESSION['user_id'];

    // Валидация
    if (empty($review)) {
        $_SESSION['application_error'] = 'Отзыв не может быть пустым';
        header('Location: my_applications.php');
        exit();
    }

    if (strlen($review) > 500) {
        $_SESSION['application_error'] = 'Отзыв не может быть длиннее 500 символов';
        header('Location: my_applications.php');
        exit();
    }

    // Проверяем, может ли пользователь оставить отзыв
    if (!canAddReview($application_id, $user_id)) {
        $_SESSION['application_error'] = 'Вы не можете оставить отзыв для этой заявки';
        header('Location: my_applications.php');
        exit();
    }

    // Добавляем отзыв
    $result = addReview($application_id, $review);

    if ($result === true) {
        $_SESSION['application_success'] = 'Спасибо! Ваш отзыв сохранён.';
    } else {
        $_SESSION['application_error'] = $result['error'] ?? 'Ошибка при добавлении отзыва';
    }

    header('Location: my_applications.php');
    exit();

} else {
    header('Location: my_applications.php');
    exit();
}