<?php
// Файл: register_handler.php
// Назначение: Обработка данных формы регистрации

// Запускаем сессию для хранения сообщений об ошибках
session_start();

// Подключаем файл с функциями для работы с пользователями
require_once 'php/user_functions.php';

// Функция для валидации данных формы
function validateRegistrationData($login, $password, $confirm_password, $full_name, $phone, $email) {
    $errors = [];

    // Проверка логина (только латиница и цифры, не менее 6 символов)
    if (strlen($login) < 6) {
        $errors[] = 'Логин должен содержать не менее 6 символов';
    }

    if (!preg_match('/^[a-zA-Z0-9]+$/', $login)) {
        $errors[] = 'Логин может содержать только латинские буквы и цифры';
    }

    // Проверка пароля (минимум 8 символов)
    if (strlen($password) < 8) {
        $errors[] = 'Пароль должен содержать не менее 8 символов';
    }

    // Проверка совпадения паролей
    if ($password !== $confirm_password) {
        $errors[] = 'Пароли не совпадают';
    }

    // Проверка ФИО (кириллица и пробелы)
    if (!preg_match('/^[а-яА-ЯёЁ\s]+$/u', $full_name)) {
        $errors[] = 'ФИО может содержать только буквы кириллицы и пробелы';
    }

    // Проверка телефона (формат: 8(XXX)XXX-XX-XX)
    if (!preg_match('/^8\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $phone)) {
        $errors[] = 'Телефон должен быть в формате 8(XXX)XXX-XX-XX';
    }

    // Проверка email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email адрес';
    }

    // Проверка уникальности логина
    if (userExists($login)) {
        $errors[] = 'Пользователь с таким логином уже существует';
    }

    return $errors;
}

// Проверяем, была ли отправлена форма методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Получаем данные из формы
    $login = $_POST['login'] ?? "";
    $password = $_POST['password'] ?? "";
    $confirm_password = $_POST['confirm_password'] ?? "";
    $full_name = $_POST['full_name'] ?? "";
    $phone = $_POST['phone'] ?? "";
    $email = $_POST['email'] ?? "";

    // Валидируем данные
    $errors = validateRegistrationData($login, $password, $confirm_password, $full_name, $phone, $email);

    // Если есть ошибки, сохраняем их в сессию и возвращаемся на форму регистрации 
    if (!empty($errors)) {
        $_SESSION['registration_errors'] = $errors;
        $_SESSION['old_data'] = [
            'login' => $login,
            'full_name' => $full_name,
            'phone' => $phone,
            'email' => $email
        ];
        header('Location: register.php');
        exit();
    }

    // Если ошибок нет, пытаемся зарегистрировать пользователя
    $result = registerUser($login, $password, $full_name, $phone, $email);

    if ($result === true) {
        // Успешная регистрация
        $_SESSION['registration_success'] = 'Регистрация прошла успешно! Теперь вы можете войти в систему.';
        header('Location: login.php');
        exit();
    } else {
        // Ошибка при регистрации (например, дубликат логина)
        $_SESSION['registration_errors'] = [$result['error']];
        $_SESSION['old_data'] = [
            'login' => $login,
            'full_name' => $full_name,
            'phone' => $phone,
            'email' => $email
        ];
        header('Location: register.php');
        exit();
    }

} else {
    // Если кто-то пытается зайти на обработчик напрямую, перенаправляем на форму регистрации
    header('Location: register.php');
    exit();
}
?>