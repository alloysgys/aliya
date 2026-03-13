<?php
session_start();
require_once 'php/user_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $_SESSION['login_error'] = 'Заполните все поля формы';
        header('Location: login.php');
        exit();
    }

    $user = findUserByLogin($login);

    if (!$user) {
        $_SESSION['login_error'] = 'Неверный логин или пароль';
        header('Location: login.php');
        exit();
    }

    // Если это админ
    if ($login === 'Admin') {
        if ($password !== 'KorokNET') {
            $_SESSION['login_error'] = 'Неверный пароль для администратора';
            header('Location: login.php');
            exit();
        }
    } else {
        // Для обычных пользователей проверяем хеш
        if (!password_verify($password, $user['password'])) {
            $_SESSION['login_error'] = 'Неверный логин или пароль';
            header('Location: login.php');
            exit();
        }
    }

    // Авторизация успешна
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_login'] = $user['login'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_role'] = $user['role'];

    header('Location: my_applications.php');
    exit();

} else {
    header('Location: login.php');
    exit();
}
?>