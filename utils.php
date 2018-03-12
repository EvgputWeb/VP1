<?php
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

//===========================================================
// Функция для формирования адреса в удобочитаемом виде

function makeBeautyAddress($street, $home, $part, $appt, $floor)
{
    $addrPart = ['street', 'home', 'part', 'appt', 'floor'];
    $addrPrefix = ['ул. ', 'д. ', 'корп. ', 'кв. ', 'этаж '];
    $address = '';
    for ($i = 0; $i < count($addrPart); $i++) {
        if (!empty(${$addrPart[$i]})) {
            $address .= $addrPrefix[$i] . ${$addrPart[$i]} . ', ';
        }
    }
    $address = trim($address); // удаляем пробелы вначале и в конце строки
    if (mb_strlen($address) > 1) {
        $lastChar = mb_substr($address, -1, 1); // последний символ строки
        if ($lastChar == ',') {
            $address = mb_substr($address, 0, mb_strlen($address) - 1);
        }
    }
    return $address;
}
