<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

//====================================================
// Файл с параметрами подключения
$connectParamsFilename = __DIR__ . DIRECTORY_SEPARATOR . ".connect_params";
if (!file_exists($connectParamsFilename)) {
    return false;
}
// Открываем файл
$connectParamsFile = fopen($connectParamsFilename, "rb");

// Читаем первую строку с названиями параметров
$str = stream_get_line($connectParamsFile, 1024, "\n");
$varNames = explode(',', $str);

// Читаем вторую строку со значениями параметров
$str = stream_get_line($connectParamsFile, 1024, "\n");
$varValues = explode(',', $str);

fclose($connectParamsFile);

for ($i = 0; $i < count($varNames); $i++) {
    ${$varNames[$i]} = $varValues[$i];
}

//---------------------------------------------------------------------------
try {
    // Подключаемся
    $dbh = new PDO('mysql:host='. $host, $user, $password);
} catch (PDOException $e) {
    echo 'Не удалось подключиться.<br>';
    echo 'Проверьте настройки подключения в файле ".connect_params"<br><br>';
    die("DB ERROR: ". $e->getMessage());
}

//---------------------------------------------------------------------------
try {
    // Создаём базу
    $dbh->exec(
        "CREATE DATABASE IF NOT EXISTS `$dbname`;"
    )
    or die(print_r($dbh->errorInfo(), true));

    echo "База данных $dbname создана<br>";
    
    // Подключаемся к базе
    $dbh = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $password);

} catch (PDOException $e) {
    die("DB ERROR: ". $e->getMessage());
}

//---------------------------------------------------------------------------
try {
    // Создаем таблицу users
    $dbh->exec(
        "CREATE TABLE IF NOT EXISTS `users` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(50) NULL DEFAULT NULL,
                    `email` VARCHAR(50) NOT NULL,
                    `phone` VARCHAR(20) NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE INDEX `email` (`email`)
                )
                COLLATE='utf8_general_ci'
                ENGINE=InnoDB;"
    );

    echo "Таблица `users` создана<br>";

} catch (PDOException $e) {
    die("DB ERROR: ". $e->getMessage());
}

//---------------------------------------------------------------------------
try {
    // Создаем таблицу orders
    $dbh->exec(
        "CREATE TABLE IF NOT EXISTS `orders` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
            `user_id` INT(11) NOT NULL DEFAULT '0',
            `street` VARCHAR(50) NULL DEFAULT NULL,
            `home` VARCHAR(10) NULL DEFAULT NULL,
            `part` VARCHAR(10) NULL DEFAULT NULL,
            `appt` VARCHAR(10) NULL DEFAULT NULL,
            `floor` VARCHAR(3) NULL DEFAULT NULL,
            `comment` VARCHAR(200) NULL DEFAULT NULL,
            `payment` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
            `callback` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`),
            INDEX `FK_orders_users` (`user_id`),
            CONSTRAINT `FK_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        )
        COLLATE='utf8_general_ci'
        ENGINE=InnoDB;"
    );

    echo "Таблица `orders` создана<br>";

} catch (PDOException $e) {
    die("DB ERROR: ". $e->getMessage());
}

//---------------------------------------------------------------------------

echo "<br>Работа скрипта завершена успешно!<br>";