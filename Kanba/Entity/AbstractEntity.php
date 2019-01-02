<?php

namespace Kanba\Entity;

use Kanba\MyPDO;
use Loader;

require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "autoload.php");


/**
 * Class AbstractEntity
 * Définit une entité abstraite.
 * @package Kanba\Entity
 */
abstract class AbstractEntity implements IEntity
{

  private const EXT = ".php";

  private const ENTITY_DIR = "Entity";

  private const SPECIAL_FILES = [".", "..", "AbstractEntity.php", "IEntity.php"];

  private const ID_FIELD = 'id';

  /**
   * @var int
   */
  protected $id;

  /**
   * Renvoie un int ou une chaîne de caractère selon la valeur du paramètre.
   * @param string $v
   * @return int|string
   */
  private static function unifyValue(string $v) {
    if (is_numeric($v)) {
      return intval($v);
    }
    return strval($v);
  }

  /**
   * Recupère n'importe qu'elle propriété de l'entité à partir de son id.
   * @param string $property
   * @return int|null
   */
  public function getProperty(string $property) {
    if ($property !== AbstractEntity::ID_FIELD) {
      $table = $this->getTableName();
      $id = $this->getId();
      if ($id === null) {
        throw new \AssertionError("Vous ne pouvez pas charger une propriété si l'id de l'entité est NULL.");
      }

      $c = get_class($this);
      if (property_exists($this, $property)) {
        $p = $this->$property;
        if ($p === null) {
          $v = MyPDO::getFieldValueById($table, $id, $property);
          if ($v === null) {
            throw new \AssertionError("Aucune donnée trouvée pour la propriété $property de l'entité $c avec comme id $id");
          }
          $this->$property = static::unifyValue($v);
          return $this->$property;
        }
        return $this->$property;
      }
    } else {
      return property_exists($this, AbstractEntity::ID_FIELD) ? $this->id : null;
    }
  }

  /**
   * Permet de definir n'importe quelle propriété de l'entité.
   * @param string $property
   * @param string|int $value
   */
  public function setProperty(string $property, $value): void {
    $p = $this->getProperty($property);
    if ($p !== $value) {
      $id = $this->getId();
      if ($id === null) {
        throw new \AssertionError("Vous ne pouvez pas mettre à jour une propriété si l'id de l'entité est NULL.");
      }
      MyPDO::setFieldValueById(static::getTableName(), $id, $property, $value);
      $this->$property = $value;
    }
  }

  /**
   * Récupère l'identifiant unique du Kanba.
   * @return int
   */
  public function getId(): int {
    return $this->getProperty('id');
  }


  /**
   * Charge un champ de l'entité à partir de son id dans la base de donnée.
   * @param \PDO $pdo
   * @param string $fieldName
   * @return mixed
   */
  protected function loadField(\PDO $pdo, string $fieldName) {
    $table = $this->getTableName();
    $id = self::getId();
    $t = $pdo->prepare("SELECT $fieldName FROM $table WHERE id = $id");
    $t->execute();
    $r = $t->fetch();
    if ($r === false) {
      throw new \AssertionError("Le champs $fieldName dans la table $table pour l'id $id est introuvable.");
    }
    return $r->$fieldName;
  }

  /**
   * Met à jour un champ de l'entité dans la base de donnée à partir de son id.
   * @param \PDO $pdo
   * @param string $fieldName
   * @param mixed $value
   */
  protected function updateField(\PDO $pdo, string $fieldName, $value): void {
    $table = $this->getTableName();
    $id = self::getId();
    if (is_int($value)) {
      $pdo->prepare("UPDATE $table SET $fieldName = $value WHERE id = $id")->execute();
    } else {
      $pdo->prepare("UPDATE $table SET $fieldName = '$value' WHERE id = $id")->execute();
    }
  }

  /**
   * Permet de savoir si une entité existe dans la base de donnée selon son id.
   * @return bool
   */
  public function isPresent() {
    return MyPDO::getFieldValueById($this->getTableName(), $this->getId(), AbstractEntity::ID_FIELD) !== null;
  }

  /**
   * Supprime une ligne dans la table.
   */
  public function remove() {
    $id = $this->getId();
    if ($id === null) {
      throw new \AssertionError("Vous ne pouvez pas supprimer une entité qui n'existe pas (id vaut NULL).");
    }
    MyPDO::removeRowById(static::getTableName(), $id);
  }


  /**
   * Récupère la liste des entités en scannant le dossier correspondant.
   * @see AbstractEntity::ENTITY_DIR
   * @return String[]
   */
  static function getList() : array
  {
    $files = [];
    foreach (scandir(Loader::resolvePath(static::ENTITY_DIR)) as $file) {
      if (!in_array($file, static::SPECIAL_FILES)) {
        $files[] = basename($file, static::EXT);
      }
    }
    return $files;
  }

  /**
   * Récupère un tableau contenant tout les ids de l'entité.
   * @param \PDO $pdo
   * @return int[]
   */
  static function getIds(\PDO $pdo) : array
  {
    $table = static::getTableName();
    $t = $pdo->prepare("SELECT id FROM $table");
    $t->execute();
    $r = [];
    foreach ($t->fetchAll() as $item) {
      $r[] = intval($item->id);
    }
    return $r;
  }


  /**
   * Récupère une instance de Faker dans la bonne localisation.
   * @return \Faker\Generator
   */
  protected static function getLocalFaker(): \Faker\Generator
  {
    return \Faker\Factory::create("fr_FR");
  }

}