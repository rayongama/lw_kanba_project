<?php

namespace Kanba\Entity;


use Faker\Factory;
use Kanba\MyPDO;

/**
 * Class Kanba
 * Définit un Kanba
 * @package Kanba\Entity
 */
class Kanba extends AbstractEntity
{

  const DEFAULT_TITLE = "Mon super Kanba";

  protected $ownerId;
  private $lists;
  protected $title;
  protected $private;


  /**
   * Kanba constructor.
   * @param string $id
   */
  public function __construct($id)
  {
    $this->id = $id;

    $this->lists = [];
  }

  /*
   * GETTERS
   */


  /**
   * Récupère le titre du Kanba.
   * @return string
   */
  public function getTitle(): string {
    return $this->getProperty('title');
  }

  /**
   * Récupère l'id de l'utilisateur propriétaire de ce Kanba.
   * @return int
   */
  public function getOwnerId(): int {
    return $this->getProperty('ownerId');

  }

  /**
   * Indique si le kanba est privé ou non.
   * @return bool
   */
  public function isPrivate(): bool {
    return $this->getProperty('private');

  }

  /**
   * Récupère le slug associé au Kanba.
   * Le slug est une chaîne de caractère composé du titre avec les caractères "'"
   * et les espaces remplacés par "-".
   * @return string
   */
  public function getSlug(): string
  {
    $id = $this->getId();
    $t = $this->getTitle();
    $t = str_replace(" ", "-", $t);
    $t = str_replace("'", "-", $t);
    return "$id-" . $t;
  }

  /**
   * Renvoie true si le Kanba appartient à l'utilisateur (par son id).
   * Sinon renvoie false.
   * @param $id
   * @return bool
   */
  public function belongsTo(int $id): bool {
    return $this->getOwnerId() === $id;
  }

  /*
   * ***************
   */


  /*
   * COMMANDES
   */
/*
  public static function createNewKanba() {
    $pdo = MyPDO::getPDO();
    $pdo->prepare("INSERT INTO kanba (title, ownerId, private) ")
  }
*/
  /**
   * Effectue une mise à jour de l'entité dans la base de donnée.
   * @param \PDO $pdo
   * @param \stdClass $data
   * @return void
   */
  public function update(\PDO $pdo, \stdClass $data): void
  {
    if (isset($data->title)) {
      $this->setProperty('title', $data->title);
    }
    if (isset($data->isPrivate)) {
      $this->setProperty('private', $data->isPrivate);
    }
  }

  /**
   * Charge réellement le Kanba (la liste de toute ses tâches).
   */
  public function loadKanba(): void
  {
    $pdo = MyPDO::getPDO();
    $id = $this->getId();
    $t = $pdo->prepare("SELECT * FROM todo_list WHERE kanba_id = $id");
    $t->execute();
    $p = MyPDO::getPDO();
    foreach ($t->fetchAll() as $l) {
      $list = new \stdClass();
      $list->title = $l->title;
      $list->id = $l->id;
      $list->todos = [];
      $t = $p->prepare("SELECT * FROM todo WHERE todoListId = $l->id");
      $t->execute();
      foreach ($t->fetchAll() as $r) {
        $r->time = substr($r->time, 0, -3);
        $list->todos[] = $r;
      }
      array_push($this->lists, $list);
    }
  }


  public function getLists()
  {
    return $this->lists;
  }

  /**
   * @inheritdoc
   */
  static function migrate(\PDO $pdo): void
  {
    $table = self::getTableName();
    $sql = <<<EOT
CREATE TABLE $table
(
    id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
    title text(30) NOT NULL,
    ownerId int NOT NULL,
    private bool NOT NULL
);
EOT;
    $pdo->prepare($sql)->execute();
  }

  /**
   * @inheritdoc
   */
  static function getTableName(): String
  {
    return "kanba";
  }

  /**
   * @inheritdoc
   */
  static function hydrate(\PDO $pdo, int $n = 10): void
  {
    $table = self::getTableName();
    $faker = Factory::create("fr_FR");
    foreach (User::getIds($pdo) as $owner) {
      for ($j = 0; $j < $n; $j += 1) {
        $title = addslashes($faker->jobTitle);
        $private = $faker->boolean() ? 1 : 0;
        $pdo->prepare("INSERT INTO $table (title, ownerId, private) VALUES ('$title', '$owner', '$private')")
          ->execute();
      }
    }
  }
}