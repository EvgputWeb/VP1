<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

//=========================================================================================
foreach ($_REQUEST as $key => $value) {
    echo $key . '=' . $value . '<br>';
}

/*
 *  ВХОДНОЙ КОНТРОЛЬ
 *
 *  Надо проверить, что поля email и phone точно есть
 *
 *
 */




//=========================================================================================
// Подключаемся к базе: параметры подключения
$host = '127.0.0.1';
$db = 'Burger';
$user = 'root';
$pass = '';
$charset = 'utf8';

// data source name
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

// Подключаемся к базе
$dbh = new PDO($dsn, $user, $pass, $opt);

//=========================================================================================
// Фаза 1: Регистрация или "авторизация" пользователя
// По окончании этой фазы имеем в наличии userId

$email = $_REQUEST['email'];

$sth = $dbh->prepare('SELECT id FROM users WHERE email = :email');
$sth->execute(array('email' => $email));
$userId = $sth->fetchColumn();

if ($userId === false) {
    echo 'Создаём пользователя <br>';
    // Нет такого пользователя. Создаём.
    $sth = $dbh->prepare("INSERT INTO users(name, email, phone) VALUES (:fname, :femail, :fphone)");
    $sth->execute(array(
        "fname" => $_REQUEST['name'],
        "femail" => $_REQUEST['email'],
        "fphone" => $_REQUEST['phone']
    ));
    $userId = $dbh->lastInsertId();
    var_dump($userId);
} else {
    echo 'Пользователь уже есть <br>';
    var_dump($userId);
}

//=========================================================================================
// Фаза 2: Оформление заказа
// Записываем данные заказа в таблицу orders






//=========================================================================================
// Фаза 3: Письмо пользователю



/*
header("Location: index.html");
die;
*/
