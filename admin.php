<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

//=========================================================================================
// Подключаемся к базе: параметры подключения
$host = '127.0.0.1';
$db = 'Burger';
$user = 'root';
$pass = '';
$charset = 'utf8';

try {
    // data source name
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    // Подключаемся к базе
    $dbh = new PDO($dsn, $user, $pass, $opt);
} catch (PDOException $e) {
    // Как лучше всего обработать ошибку ???
    header("Location: error.php?errcode=4002");
    return;
}

try {
    echo 'ПОЛЬЗОВАТЕЛИ:' . "<br>\n";
    $sth = $dbh->query('SELECT * FROM users');
    while ($row = $sth->fetch()) {
        echo $row['name'] . ' ' . $row['email'] . ' ' . $row['phone'] . "<br>\n";
    }
} catch (PDOException $e) {
    // Как лучше всего обработать ошибку ???
    header("Location: error.php?errcode=4003");
    return;
}

echo "<br><br>\n";

try {
    echo 'ЗАКАЗЫ:' . "<br>\n";
    $sth = $dbh->query('SELECT orders.*, users.name as username FROM orders,users WHERE orders.user_id=users.id');
    while ($row = $sth->fetch()) {
        echo $row['username'] . ' ' . $row['street'] . ' ' . $row['home'] . ' ' . $row['part'] . ' ' . $row['appt'] . ' ';
        echo $row['floor'] . ' ' . $row['payment'] . ' ' . $row['callback'] . "<br>\n";
    }
} catch (PDOException $e) {
    // Как лучше всего обработать ошибку ???
    header("Location: error.php?errcode=4003");
    return;
}
