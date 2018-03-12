<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

//====================================================
// Подключаемся к базе
$dbh = require_once 'dbconnect.php';

if ($dbh === false) {
    header("Location: error.php?errcode=4001");
    return;
}

//====================================================
// Получаем данные из базы
try {
    // Пользователи:
    $sth_users = $dbh->query('SELECT * FROM users');
} catch (PDOException $e) {
    header("Location: error.php?errcode=4003");
    return;
}

try {
    // Заказы:
    $sth_orders = $dbh->query('SELECT orders.*, users.name as username FROM orders,users WHERE orders.user_id=users.id');
} catch (PDOException $e) {
    header("Location: error.php?errcode=4003");
    return;
}

//====================================================
// Отображаем данные:
// admin_view работает только с $sth_users и $sth_orders
require_once 'admin_view.php';
