<?php

namespace Kanban;


class MyPDO
{

  /**
   * @var MyPDO
   */
  static $INSTANCE;

  private $pdo;
  private $database;

  private function __construct($database)
  {
    $this->database = $database;

    $dbOptions = [
      \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
    ];

    $this->pdo = new \PDO("mysql:host=" . Configurator::getEntry("DB_URL") .
      ";dbname=" . $database,
      Configurator::getEntry("DB_USERNAME"),
      Configurator::getEntry("DB_PASSWORD"),
      $dbOptions);;
  }

  private static function getInstance($database = null): MyPDO {
    if (MyPDO::$INSTANCE && (MyPDO::$INSTANCE->database === $database || (
      $database === null && MyPDO::$INSTANCE->database === Configurator::getEntry("DB_NAME")))) {
     return MyPDO::$INSTANCE;
    }

    if ($database === null) {
      $database = Configurator::getEntry("DB_NAME");
    }

    MyPDO::$INSTANCE = new MyPDO($database);
    return MyPDO::$INSTANCE;
  }

  public static function getPDO($database = null): \PDO
  {
    return self::getInstance($database)->pdo;
  }

  public static function getFieldValueById($table, $id, $fieldName) {
    return self::getFieldValueByCondition($table, $fieldName, 'id', $id);
  }

  public static function getFieldValueByCondition($table, $fieldName, $conditionName, $conditionValue) {
    $pdo = self::getPDO();
    if (is_int($conditionValue)) {
      $t = $pdo->prepare("SELECT $fieldName FROM $table WHERE $conditionName = $conditionValue;");
    } else {
      $t = $pdo->prepare("SELECT $fieldName FROM $table WHERE $conditionName = '$conditionValue';");
    }

    $t->execute();
    $r = $t->fetch();
    return $r === false ? null : $r->$fieldName;
  }

  public static function setFieldValueById($table, $id, $fieldName, $value) {
    $pdo = self::getPDO();
    if (is_int($value)) {
      $t = $pdo->prepare("UPDATE $table SET $fieldName = $value WHERE id = $id;");
    } else {
      $t = $pdo->prepare("UPDATE $table SET $fieldName = '$value' WHERE id = $id;");
    }
    $t->execute();
  }

  public static function removeRowById($table, $id) {
    self::getPDO()->prepare("DELETE FROM $table WHERE id = $id")->execute();
  }

}