<?php
// Файл: logout.php
// Назначение: Выход пользователя из системы

session_start();

// Очищаем все данные сессии
$_SESSION = array();

// Уничтожаем сессию
session_destroy();

// Перенаправляем на страницу входа
header('Location: login.php');
exit();
?>