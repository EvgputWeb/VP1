<?php
// Модуль для подключения к базе
// На выходе:  $dbh

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

try {
    // data source name
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    // Подключаемся к базе
    $dbh = new PDO($dsn, $user, $password, $opt);
    // Всё нормально - отдаём $dbh
    return $dbh;
} catch (PDOException $e) {
    return false;
}
