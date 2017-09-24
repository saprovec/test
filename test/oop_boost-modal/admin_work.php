<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start([
    'cookie_lifetime' => 86400,
]);

if(isset($_POST['reset'])){
    session_destroy();
    header("Location: admin_login.php");
    exit;
}

if (!isset($_SESSION['info']['id']))
{
    exit('<strong>Ошибка!</strong> Доступ закрыт.');
}

include __DIR__.'/classes/Admin.php';

$array_get = $_GET;
unset($_GET);

$init_array = Admin::initData($array_get);
$search_sql = Admin::getSearchSql($array_get);
$execute_arr = Admin::executeArray($array_get);
$sth = DB::execute(Admin::getSqlTable($search_sql,$init_array),$execute_arr);
$result = DB::execute(Admin::getSqlPage($search_sql),$execute_arr);
$pageCount = Admin::getPageCount($result,$init_array['limit']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
    <title>ТЕстовая страница</title>
</head>
<body>
<nav class="navbar navbar-inverse bg-primary">
    <h4 class="mb-0"><span class="badge badge-success">Администрирование</span></h4>
    <form class="form-inline" action="admin_work.php" method="post">
        <div class="d-flex justify-content-end">
            <div class="p-2"><span class="badge badge-pill badge-secondary">Имя: <?=$_SESSION['info']['user_name']?></span></div>
            <div class="p-2"><span class="badge badge-pill badge-secondary">Логтн: <?=$_SESSION['info']['user_login']?></span></div>
            <div class="p-2"><span class="badge badge-pill badge-secondary">Статус: <?=$_SESSION['info']['user_status']?></span></div>
        </div>
        <input class="btn btn-secondary float-right" type="submit" name="reset" value="ВЫХОД" data-toggle="tooltip" data-placement="bottom" title="Выход из администрирования">
    </form>
</nav>
<div class="container">
    <div class="admin">
        <div class="table-search">
            <form action="admin_work.php" class="form-inline m-4" method="get">
                <label class="mr-2" for="search_id">ID:</label>
                <input type="text" class="form-control col-1 mr-5" id="search_id" name="id" value="<?=$init_array['id']?>" placeholder="ID">
                <label class="mr-2" for="search_name">Имя:</label>
                <input type="text" class="form-control col-2 mr-5" id="search_name" name="name" value="<?=$init_array['name']?>" placeholder="имя">
                <label class="mr-2" for="search_date">Дата:</label>
                <input type="text" class="form-control col-2 mr-5" id="search_date" name="date" value="<?=$init_array['date']?>" placeholder="ГГ-мм-мм чч:мм:сс">
                <button type="submit" class="btn btn-primary mr-5">ИСКАТЬ</button>
                <button type="button" onclick="location.href='admin_work.php'" class="btn btn-primary">СБРОСИТЬ</button>
            </form>
        </div>
        <div class="table">
            <table class='table table-striped'>
                <thead><tr>
                    <th><a href="admin_work.php?interval=<?=$init_array['interval']?>&column=id<?=($init_array['column'] === 'id' && $init_array['search'] === 'ASC') ? '&search=DESC' : '&search=ASC'?><?=$init_array['column_str']?>">№</a><?=Admin::getClassSort('id',$init_array['column'],$init_array['search'])?></th>
                    <th><a href="admin_work.php?interval=<?=$init_array['interval']?>&column=name<?=($init_array['column'] === 'name' && $init_array['search'] === 'ASC') ? '&search=DESC' : '&search=ASC'?><?=$init_array['column_str']?>">Имя</a><?=Admin::getClassSort('name',$init_array['column'],$init_array['search'])?></th>
                    <th>Телефон</th><th>Email</th><th>Сообщение</th>
                    <th><a href="admin_work.php?interval=<?=$init_array['interval']?>&column=date<?=($init_array['column'] === 'date' && $init_array['search'] === 'ASC') ? '&search=DESC' : '&search=ASC'?><?=$init_array['column_str']?>">Дата</a><?=Admin::getClassSort('date',$init_array['column'],$init_array['search'])?></th>
                </tr></thead>
                <tbody>
                <?php
                foreach($sth as $row) {
                    echo '<tr><td>'.$row['id'].'</td><td>'.$row['name'].'</td><td>'.$row['phone'].'</td><td>'.$row['email'].'</td><td>'.$row['message'].'</td><td>'.$row['date'].'</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">
        <nav aria-label="">
            <ul class="pagination justify-content-center">
            <?php
            if($init_array['interval'] == 0){
                echo '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">Назад</a></li>';
            }else{
                echo '<li class="page-item"><a class="page-link" href="admin_work.php?interval='.($init_array['interval']-1).$init_array['result_str'].'">Назад</a></li>';
            }
            for($i=0; $i<$pageCount; $i++) {
                if($init_array['interval'] == $i){
                    echo '<li class="page-item active"><a class="page-link" href="#">'.($i+1).'</a></li>';
                }else {
                    echo '<li class="page-item"><a class="page-link" href="admin_work.php?interval='.$i.$init_array['result_str'].'" title="'.($i+1).'">'.($i+1).'</a></li>';
                }
            }
            if($init_array['interval'] == ($pageCount-1)){
                echo '<li class="page-item disabled"><a class="page-link" href="#">Вперед</a></li>';
            }else{
                echo '<li class="page-item"><a class="page-link" href="admin_work.php?interval='.($init_array['interval']+1).$init_array['result_str'].'">Вперед</a></li>';
            }
            ?>
            </ul>
        </nav>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
</body>
</html>