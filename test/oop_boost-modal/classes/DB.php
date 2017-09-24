<?php
/**
 * Created by PhpStorm.
 * User: ura
 * Date: 16.09.17
 * Time: 23:37
 */

include __DIR__.'/Config.php';

class DB
{
    private static $dbh = false;

    public static function start()
    {
        $cfg = Config::$dbconfig;
        if (self::$dbh)
        {
            return self::$dbh;
        }
        try {
            $dbh = new PDO($cfg['dsn'], $cfg['username'], $cfg['passwd']);
        }catch (PDOException $e)
        {
            print "Error!: ".$e->getMessage()."<br/>";
            die();
        }

        self::$dbh = $dbh;

        return $dbh;
    }

    public static function execute($sql, $array)
    {
        if (!self::$dbh)
        {
            self::start();
        }
        $sth =  self::$dbh->prepare($sql);
        $sth->execute($array);

        return $sth;
    }
}