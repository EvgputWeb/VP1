<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

//=========================================================================================
// ВХОДНОЙ КОНТРОЛЬ
// Надо проверить, что поля email и phone точно есть (т.к. в базе они помечены NOT NULL)

if ((empty($_REQUEST['email'])) || (empty($_REQUEST['phone']))) {
    // Как лучше всего обработать ошибку ???
    header("Location: error.php?errcode=4000");
    return;
}

//=========================================================================================
// Подключаемся к базе
$dbh = require_once 'dbconnect.php';

if ($dbh === false) {
    header("Location: error.php?errcode=4001");
    return;
}

//=========================================================================================
// Фаза 1: Регистрация или "авторизация" пользователя
// По окончании этой фазы имеем в наличии userId

$email = $_REQUEST['email'];

try {
    $sth = $dbh->prepare('SELECT id FROM users WHERE email = :email');
    $sth->execute(array('email' => $email));
    $userId = $sth->fetchColumn();
} catch (PDOException $e) {
    // Как лучше всего обработать ошибку ???
    header("Location: error.php?errcode=4003");
    return;
}

if ($userId === false) {
    // Нет такого пользователя. Создаём.
    try {
        $sth = $dbh->prepare("INSERT INTO users(name, email, phone) VALUES (:fname, :femail, :fphone)");
        $sth->execute(array(
            "fname" => $_REQUEST['name'],
            "femail" => $_REQUEST['email'],
            "fphone" => $_REQUEST['phone']
        ));
        $userId = $dbh->lastInsertId();
    } catch (PDOException $e) {
        // Как лучше всего обработать ошибку ???
        header("Location: error.php?errcode=4004");
        return;
    }
}


//=========================================================================================
// Фаза 2: Оформление заказа
// Записываем данные заказа в таблицу orders
// По окончании этой фазы имеем в наличии orderId

((!empty($_REQUEST['payment'])) && ($_REQUEST['payment'] == 'card')) ? ($payment = 1) : ($payment = 0);
((!empty($_REQUEST['callback'])) && ($_REQUEST['callback'] == 'on')) ? ($callback = 1) : ($callback = 0);

$sql = "INSERT INTO orders" .
    "(user_id, street, home, part, appt, floor, comment, payment, callback) " .
    "VALUES " .
    "(:fuser_id, :fstreet, :fhome, :fpart, :fappt, :ffloor, :fcomment, :fpayment, :fcallback)";

try {
    $sth = $dbh->prepare($sql);
    $sth->execute(array(
        "fuser_id" => $userId,
        "fstreet" => $_REQUEST['street'],
        "fhome" => $_REQUEST['home'],
        "fpart" => $_REQUEST['part'],
        "fappt" => $_REQUEST['appt'],
        "ffloor" => $_REQUEST['floor'],
        "fcomment" => $_REQUEST['comment'],
        "fpayment" => $payment,
        "fcallback" => $callback
    ));
    $orderId = $dbh->lastInsertId();
} catch (PDOException $e) {
    // Как лучше всего обработать ошибку ???
    header("Location: error.php?errcode=4005");
    return;
}


//=========================================================================================
// Фаза 3: "Письмо" пользователю
//

// Подключаем полезные функции
require_once 'utils.php';


// Функция для получения номера заказа указанного пользователя
// Возвращает строку. Например: "первый", "12-й"
function getOrderNumber(PDO $dbh, $userId)
{
    try {
        $sth = $dbh->prepare('SELECT count(*) AS count FROM orders WHERE user_id = :userId');
        $sth->execute(array('userId' => $userId));
        $count = $sth->fetchColumn();
    } catch (PDOException $e) {
        // Как лучше всего обработать ошибку ???
        header("Location: error.php?errcode=4006");
        return null;
    }
    if ($count == 1) {
        return "первый";
    } else {
        return "$count-й";
    }
}

//-----------------------------------------------------
// Создаём папка для писем, если её нет
$emailsFolder = __DIR__ . DIRECTORY_SEPARATOR . '_emails_';
if (!file_exists($emailsFolder)) {
    try {
        mkdir($emailsFolder, 0777);
    } catch (ErrorException $e) {
        header("Location: error.php?errcode=4007");
        return;
    }
}
//-----------------------------------------------------

// Файл для сохранения текста письма
$emailFileName = $emailsFolder . DIRECTORY_SEPARATOR . date('Y-m-d__H-i-s') . '.txt';

// Получаем адрес и номер заказа данного пользователя
$userAddress = makeBeautyAddress($_REQUEST['street'], $_REQUEST['home'], $_REQUEST['part'], $_REQUEST['appt'], $_REQUEST['floor']);
$userOrderNum = getOrderNumber($dbh, $userId);

// Текст письма
$mailText = "Заказ № $orderId\n\n";
$mailText .= "Ваш заказ будет доставлен по адресу:\n";
$mailText .= $userAddress . "\n\n";
$mailText .= "Содержимое заказа:\n";
$mailText .= "DarkBeefBurger за 500 рублей, 1 шт\n\n";
$mailText .= "Спасибо!\n";
$mailText .= "Это Ваш " . $userOrderNum . " заказ!\n";

// Пишем в файл
file_put_contents($emailFileName, $mailText);

//=========================================================================================

// Работа выполнена. Возвращаемся обратно - на главную страницу
header("Location: index.html");
