<?php

// Файл: php/application_functions.php
// Назначение: функции для работы с заявками (создание, получение, обновление)

// Подключаем конфигурационный файл с настройками БД
require_once 'config.php';

/**
* Функция создания новой заявки
*
* @param int $user_id ID пользователя, создающего заявку
* @param string $course_name Наименование курса
* @param string $start_date Желаемая дата начала (в формате YYYY-MM-DD)
* @param int $payment_method_id ID способа оплаты
* @return array|bool Возвращает true при успехе или массив с ошибкой
*/
function createApplication($user_id, $course_name, $start_date, $payment_method_id) {
    global $db_conn;

    // Проверяем подключение к БД
    if (!$db_conn) {
        return ['error' => 'Ошибка подключения к базе данных'];
    }

    // Экранируем специальные символы для безопасности
    $user_id = (int)$user_id;
    $course_name = pg_escape_string($db_conn, $course_name);
    $start_date = pg_escape_string($db_conn, $start_date);
    $payment_method_id = (int)$payment_method_id;

    // Статус по умолчанию - 'Новая'
    $status = 'Новая';

    // Формируем SQL-запрос для вставки новой заявки
    $query = "INSERT INTO applications (user_id, course_name, desired_start_date, payment_method_id, status)
        VALUES ($user_id, '$course_name', '$start_date', $payment_method_id, '$status')";

    // Выполняем запрос
    $result = pg_query($db_conn, $query);

    if ($result) {
        // Успешное создание заявки
        return true;
    } else {
        // Ошибка при выполнении запроса
        $error = pg_last_error($db_conn);
        return ['error' => 'Ошибка при создании заявки: ' . $error];
    }
}

/**
* Функция получения всех заявок конкретного пользователя
*
* @param int $user_id ID пользователя
* @return array Массив заявок пользователя
*/
function getUserApplications($user_id) {
    global $db_conn;

    $applications = [];

    if (!$db_conn) {
        return $applications;
    }

    $user_id = (int)$user_id;
    // Запрос с JOIN к таблице payment_methods для получения названия способа оплаты
    $query = "SELECT
                        a.id,
                        a.course_name,
                        a.desired_start_date,
                        a.status,
                        a.created_at,
                        a.review,
                        pm.name as payment_method_name
                    FROM applications a
                    JOIN payment_methods pm ON a.payment_method_id = pm.id
                    WHERE a.user_id = $user_id
                    ORDER BY a.created_at DESC";

    $result = pg_query($db_conn, $query);

    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $applications[] = $row;
        }

        pg_free_result($result);
    }

    return $applications;
}


/**
* Функция получения всех заявок (для администратора)
*
* @return array Массив всех заявок с данными пользователей
*/
function getAllApplications() {
    global $db_conn;

    $applications = [];

    if (!$db_conn) {
        return $applications;
    }

    // Запрос с JOIN к таблицам users и payment_methods
    $query = "SELECT
                        a.id,
                        a.course_name,
                        a.desired_start_date,
                        a.status,
                        a.created_at,
                        a.review,
                        u.id as user_id,
                        u.full_name as user_name,
                        u.login as user_login,
                        u.email as user_email,
                        pm.name as payment_method_name
                    FROM applications a
                    JOIN users u ON a.user_id = u.id
                    JOIN payment_methods pm ON a.payment_method_id = pm.id
                    ORDER BY a.created_at DESC";

    $result = pg_query($db_conn, $query);

    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $applications[] = $row;
        }

        pg_free_result($result);
    }

    return $applications;
}

/** 
* Функция обновления статуса заявки
*
* @param int $application_id ID заявки
* @param string $new_status Новый статус
* @return array|bool Возвращает true при успехе или массив с ошибкой
*/
function updateApplicationStatus($application_id, $new_status) {
    global $db_conn;

    if (!$db_conn) {
        return ['error' => 'Ошибка подключения к базе данных'];
    }

    $application_id = (int)$application_id;
    $new_status = pg_escape_string($db_conn, $new_status);

    // Проверяем, что статус допустимый
    $allowed_statuses = ['Новая', 'Идет обучение', 'Обучение завершено'];
    if (!in_array($new_status, $allowed_statuses)) {
        return ['error' => 'Недопустимый статус'];
    }

    $query = "UPDATE applications SET status = '$new_status' WHERE id = $application_id";
    $result = pg_query($db_conn, $query);

    if ($result) {
        return true;
    } else {
        $error = pg_last_error($db_conn);
        return ['error' => 'Ошибка при обновлении статуса: ' . $error];
    }
}

/**
* Функция добавления отзыва к заявке
*
* @param int $application_id ID заявки
* @param string $review Текст отзыва
* @return array|bool Возвращает true при успехе или массив с ошибкой
*/
function addReview($application_id, $review) {
    global $db_conn;

    if (!$db_conn) {
        return ['error' => 'Ошибка подключения к базе данных'];
    }

    $application_id = (int)$application_id;
    $review = pg_escape_string($db_conn, $review);

    $query = "UPDATE applications SET review = '$review' WHERE id = $application_id";
    $result = pg_query($db_conn, $query);

    if ($result) {
        return true;
    } else {
        $error = pg_last_error($db_conn);
        return ['error' => 'Ошибка при добавлении отзыва: ' . $error];
    }
}


/**
* Функция получения всех способов оплаты
*
* @return array Массив способов оплаты
*/
function getPaymentMethods() {
    global $db_conn;

    $methods = [];

    if (!$db_conn) {
        return $methods;
    }

    $query = "SELECT * FROM payment_methods ORDER BY id";
    $result = pg_query($db_conn, $query);

    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $methods[] = $row;
        }

        pg_free_result($result);
    }

    return $methods;
}

/**
* Функция проверки, может ли пользователь оставить отзыв (статус "Обучение завершено")
*
*
* @param int $application_id ID заявки
* @param int $user_id ID пользователя
* @return bool Возвращает true, если можно оставить отзыв
*/
function canAddReview($application_id, $user_id) {
    global $db_conn;

    if (!$db_conn) {
        return false;
    }

    $application_id = (int)$application_id;
    $user_id = (int)$user_id;

    $query = "SELECT id FROM applications
        WHERE id = $application_id
        AND user_id = $user_id
        AND status = 'Обучение завершено'
        AND (review IS NULL OR review = '')";

    $result = pg_query($db_conn, $query);

    return ($result && pg_num_rows($result) > 0);
}

?>