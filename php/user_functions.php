<?php
// Файл: php/user_functions.php
// Назначение: Функции для работы с пользователями (регистрация, поиск и т.д.)

// Подключаем конфигурационный файл с настройками БД
require_once 'config.php';

/**
 * Функция регистрации нового пользователя
 * 
 * @param string $login Логин пользователя
 * @param string $password Пароль пользователя (нехэшированный)
 * @param string $full_name ФИО пользователя
 * @param string $phone Телефон пользователя
 * @param string $email Email пользователя
 * @return array|bool Возвращает true при успехе или массив с ошибкой
 */
function registerUser($login, $password, $full_name, $phone, $email) {
    global $db_conn;

    // Проверяем подключение к БД
    if (!$db_conn) {
        return ['error' => 'Ошибка подключения к базе данных'];
    }

    // Хэшируем пароль
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Экранируем специальные символы для безопасности 
    $login = pg_escape_string($db_conn, $login);
    $full_name = pg_escape_string($db_conn, $full_name);
    $phone = pg_escape_string($db_conn, $phone);
    $email = pg_escape_string($db_conn, $email);

    // Формируем SQL-запрос для вставки нового пользователя
    $query = "
        INSERT INTO users (login, password, full_name, phone, email, role)
        VALUES ('$login', '$hashed_password', '$full_name', '$phone', '$email', 'user')";
    
    // Выполняем запрос
    $result = pg_query($db_conn, $query);

    if ($result) {
        // Успешная регистрация
        return true;
    } else {
        // Ошибка при выполнении запроса
        $error = pg_last_error($db_conn);

        // Проверяем, является ли ошибка нарушением уникальности логина
        if (strpos($error, 'duplicate key') !== false && strpos($error, 'users_login_key') !== false) {
            return ['error' => 'Пользователь с таким логином уже существует'];
        } else {
            return ['error' => 'Ошибка при регистрации: ' . $error];
        }
    }
}

/**
 * Функция поиска пользователя по логину
 * 
 * @param string $login Логин пользователя
 * @return array|false Возвращает массив с данными пользователя или false, если пользователь не найден
 */
function findUserByLogin($login) {
    global $db_conn;

    if (!$db_conn) {
        return false;
    }

    $login = pg_escape_string($db_conn, $login);

    $query = "SELECT * FROM users WHERE login = '$login'";
    $result = pg_query($db_conn, $query);

    if ($result && pg_num_rows($result) > 0) {
        return pg_fetch_assoc($result);
    }

    return false;
}

/**
 * Функция проверки существования пользователя по логину
 * 
 * @param string $login Логин пользователя
 * @return bool Возвращает true, если пользователь существует
 */
function userExists($login) {
    global $db_conn;

    if (!$db_conn) {
        return false;
    }

    $login = pg_escape_string($db_conn, $login);
    
    $query = "SELECT id FROM users WHERE login = '$login'";
    $result = pg_query($db_conn, $query);

    return ($result && pg_num_rows($result) > 0);
}
?>