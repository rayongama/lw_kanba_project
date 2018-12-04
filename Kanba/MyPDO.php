<?php
/**
 * Created by PhpStorm.
 * User: Benoit
 * Date: 26/11/2018
 * Time: 17:16
 */

namespace Kanba;


class MyPDO
{
    public static function getPDO()
    {
        $dbOptions = [
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ];
        return new \PDO("mysql:host=" . Configurator::getEntry("DB_URL") .
            ";dbname=" . Configurator::getEntry("DB_NAME"),
            Configurator::getEntry("DB_USERNAME"),
            Configurator::getEntry("DB_PASSWORD"),
            $dbOptions );
    }

}