<?php
/**
 * Created by PhpStorm.
 * User: ura
 * Date: 21.09.17
 * Time: 1:51
 */

include __DIR__.'/DB.php';

class Admin
{
    public static function stripData($text)
    {
        $quotes = ["\x27", "\x22", "\x60", "\t", "\n", "\r", "*", "%", "<", ">", "?", "!"];
        $text = trim( strip_tags( $text ) );
        $text = str_replace( $quotes, '', $text );
        $text = addslashes($text);
        return $text;
    }

    public static function executeArray($array)
    {
        $execute_array = [];
        foreach ($array as $key => $value){
            switch ($key) {
                case 'id':
                    if($value) {
                        $execute_array[$key] = (int)self::stripData($value);
                    }
                    break;
                case 'name':
                    if($value) {
                        $execute_array[$key] = '%'.self::stripData($value).'%';
                    }
                    break;
                case 'date':
                case 'date_from':
                case 'date_before':
                    if($value) {
                        $execute_array[$key] = self::stripData($value);
                    }
                    break;
            }
        }
        return $execute_array;
    }

    public static function search(&$array)
    {
        $search_array = [];
        foreach ($array as $key => $value) {
            switch ($key) {
                case 'date':
                    if($value){
                        if(strlen($value) < 19){
                            $array['date_from'] = $value.substr('2000-01-01 00:00:00', strlen($value),19);
                            $array['date_before'] = $value.substr('9999-12-31 23:59:59', strlen($value),19);
                            $search_array[] = '`'.$key.'` BETWEEN :date_from AND :date_before';
                            unset($array[$key]);
                        }else{
                            $search_array[] = '`'.$key.'` = :'.$key;
                        }
                    }
                    break;
                case 'name':
                    if($value){
                        $search_array[] = '`'.$key.'` LIKE :'.$key;
                    }
                    break;
                case 'id':
                    if($value){
                        $search_array[] = '`'.$key.'` = :'.$key;
                    }
                    break;
            }
        }
        return $search_array;
    }

    public static function initData($array)
    {
        $init_array = [
            'limit' => 10,        // количество записей
            'interval' => 0,      // интервал
            'column' => 'id',     // сортировак по умолчанию
            'column_str' => '',   // запрос по колонке
            'result_str' => '',   // запрос по page
            'search' => 'ASC',    // сортировка по умолчанию
            'id' => '',
            'name' => '',
            'date' => ''
        ];

        foreach ($array as $key => $value) {
            switch ($key){
                case 'interval':
                    $init_array['interval'] = self::stripData($value);
                    break;
                case 'column':
                case 'search':
                    $init_array[$key] = self::stripData($value);
                    $init_array['result_str'] .= '&'.$key.'='.$init_array[$key];
                    break;
                case 'id':
                case 'name':
                case 'date':
                    if($value){
                        $init_array[$key] = self::stripData($value);
                        $init_array['result_str'] .= '&'.$key.'='.$init_array[$key];
                        $init_array['column_str'] .= '&'.$key.'='.$init_array[$key];
                    }
                    break;
            }
        }
        return $init_array;
    }

    public static function getSqlTable($where,$array)
    {
        $sql = 'SELECT  `id`,`name`,`phone`,`email`,`message`,`date` FROM `application`'.
            $where.
            ' ORDER BY `'. $array['column'].'`'.$array['search'].
            ' LIMIT '.$array['interval']*$array['limit'].','.$array['limit'];

        return $sql;
    }

    public static function getSqlPage($where)
    {
        $sql = 'SELECT COUNT(*) as `count` FROM `application`'.$where;

        return $sql;
    }

    public static function getPageCount($result,$limit)
    {
        $count = $result->fetch(PDO::FETCH_OBJ);
        $pageCount = (isset($count->count)) ? ceil($count->count / $limit) : 1;

        return $pageCount;
    }

    public static function getSearchSql(&$array)
    {
        foreach (array('id','name','date') as $key){
            if(array_key_exists($key, $array)){
                if ($array[$key]){
                    return ' WHERE ' . implode(" AND ", self::search($array));
                }
            }
        }
        return '';
    }

    public static function getClassSort($column,$column_only,$sort)
    {
        if($column === $column_only)
        {
            if ($sort === 'DESC')
            {
                return '&#9650';
            }
            else return '&#9660';
        }
        else return '';
    }

    public static function getUser($array)
    {
        $psswd = Admin::stripData($array['psswd']);
        $array['login'] = Admin::stripData($array['login']);
        unset($array['psswd']);

        $sql = 'SELECT id,user_name,user_login,user_psswd,user_status FROM `user` WHERE user_login = :login';
        $sth = DB::execute($sql,$array);
        $result = $sth->fetchAll();

        if(!count($result))
        {
            return null;
        }
        if (!password_verify($psswd, $result[0]['user_psswd']))
        {
            return null;
        }
        return  $result[0];
    }
}