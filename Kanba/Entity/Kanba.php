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
   * Charge la liste de toutes les taches associées à ce Kanba.
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

  /**
   * Retourne la liste des tâches.
   * @return array
   */

  public function getLists(): array
  {
    return $this->lists;
  }

  /**
   * Renvoie le nom d'utilisateur du Kanba associé.
   * @return string
   */
  public function getOwnerName() {
    return MyPDO::getFieldValueById(User::getTableName(), $this->getOwnerId(), 'username');
  }

  /**
   * Renvoie une liste contenant tout les kanbas publics de la base de donnée.
   * @return array
   */
  static function getPublicArray(): array {
    $t = MyPDO::getPDO()->prepare("SELECT * FROM kanba WHERE private = 0");
    $t->execute();
    $r = $t->fetchAll();
    return $r;
  }

  /**
   * Créer un nouveau Kanba
   * @param $ownerId l'utilisateur à qui appartient ce kanba
   * @return \stdClass
   */
  static function create(int $ownerId): \stdClass {
    $table = self::getTableName();
    $tableList = TodoList::getTableName();
    MyPDO::getPDO()
      ->prepare("INSERT INTO $table (title, ownerId, private) VALUES ('Nouveau kanba', $ownerId, 0)")
      ->execute();
    $t = MyPDO::getPDO()->prepare("SELECT MAX(id) as 'id' FROM $table");
    $t->execute();
    $r = $t->fetch();
    $k = new Kanba($r->id);
    $d = new \stdClass();
    $d->title = 'Nouveau kanba';
    $d->id = $k->getId();
    $d->slug = $k->getSlug();

    MyPDO::getPDO()->prepare("INSERT INTO todo_list (kanba_id, title) VALUES ($d->id, 'Stories')")->execute();
    MyPDO::getPDO()->prepare("INSERT INTO todo_list (kanba_id, title) VALUES ($d->id, 'Terminées')")->execute();

    return $d;
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