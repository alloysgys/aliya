<?php
// Файл: login_handler.php
// Назначение: Обработка данных формы авторизации

// Запускаем сессию
session_start();

// Подключаем файл с функциями для работы с пользователями
require_once 'php/user_functions.php';

// Проверяем, была ли отправлена форма методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Получаем данные из формы
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    // Базовая проверка на пустые поля
    if (empty($login) || empty($password)) {
        $_SESSION['login_error'] = 'Заполните все поля формы';
        header('Location: login.php');
        exit();
    }

    // Ищем пользователя в базе данных
    $user = findUserByLogin($login);

    if (!$user) {
        // Пользователь не найден
        $_SESSION['login_error'] = 'Неверный логин или пароль';
        header('Location: login.php');
        exit();
    }

    // Проверяем пароль
    if (password_verify($password, $user['password'])) {
        // Пароль верный - авторизуем пользователя

        // Для администратора проверяем специальный пароль из задания
        if ($login === 'Admin' && $password !== 'KorokNET') {
            $_SESSION['login_error'] = 'Неверный пароль для администратора';
            header('Location: login.php');
            exit();
        }

        // Сохраняем данные пользователя в сессию
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_login'] = $user['login'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];

        // Перенаправляем на личный кабинет (пока на тестовую страницу)
        header('Location: profile.php');
        exit();
    } else {
        // Неверный пароль
        $_SESSION['login_error'] = 'Неверный логин или пароль';
        header('Location: login.php');
        exit();
    }

} else {
    // Если кто-то пытается зайти на обработчик напрямую
    header('Location: login.php');
    exit();
}
?>