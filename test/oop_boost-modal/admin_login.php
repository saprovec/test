<?php
/**
 * Created by PhpStorm.
 * User: ura
 * Date: 18.09.17
 * Time: 2:24
 */

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include __DIR__ .'/classes/Admin.php';

$error = '';

if(isset($_POST['login']) && isset($_POST['psswd'])) {

    $data_array = $_POST;
    unset($_POST);

    $result = Admin::getUser($data_array);

    if(isset($result))
    {
        session_start([
            'cookie_lifetime' => 86400,
        ]);
        $_SESSION['info'] = $result;
        header("Location: admin_work.php");
        exit;
    }
    else {
        $error = 'Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
    <title>ТЕстовая страница2</title>
</head>
<body>
<div class="container">
    <form class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4 mx-auto mt-5" action="admin_login.php" method="post">
        <div class="form-group">
            <label for="login">Логин</label>
            <input type="text" class="form-control" id="login" placeholder="Логин" name="login" value="">
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" class="form-control " id="password" placeholder="Пароль" name="psswd" value="">
        </div>
        <button type="submit" class="btn btn-primary btn-lg btn-block">Войти</button>
        <small id="emailHelp" class="form-text text-danger"><?=$error?></small>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
</body>
