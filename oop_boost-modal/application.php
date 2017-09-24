<?php

include __DIR__.'/classes/Admin.php';

// массив ?,?,?
$values = [];

// массив наименования колонок
$column = [];

// получаем данные от клиента
$data_array = $_POST;
unset($_POST);

//добавляем дату
$data_array['date'] = date("Y-m-d H:i:s");

// массив с ошибками
$error = [];

// проверка валидности
function validate($key,$value){
    switch ($key) {
        case 'name':
            return $value;
        case 'phone':
            return preg_match( '/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/', $value);
        case 'email':
            return (filter_var($value, FILTER_VALIDATE_EMAIL) !== false);
        default:
            return true;
    }
}

// формируем массивы и строки для запроса
foreach ($data_array as $key => $value) {
    if(validate($key,$value)){
        $error[$key] = true;
        $values[] = ':'.$key;
        $column[] = '`'.$key.'`';
    } else {
        $error[$key] = false;
    }
}

if(!in_array(false, $error, true)) {
    foreach ($data_array as $key => $value) {
        $data_array[$key] = Admin::stripData($value);
    }
    $sql = "INSERT INTO application (" . implode(",", $column) . ") VALUES (" . implode(",", $values) . ")";
    $stmt = DB::execute($sql,$data_array);
    header('Content-Type: application/json;');
    echo json_encode('successfully');

} else {
    // отправляем отчет об ошибках
    header('Content-Type: application/json;');
    echo json_encode($error);
}
