<?php

namespace Kanban\Entity;

require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "autoload.php");

use Faker\Factory;
use Kanban\MyPDO;
use Kanban\Type\IntOrNull;
use Kanban\Type\StringOrNull;

class User extends AbstractEntity
{

  private $username;
  private $password;
  private $passwordBdd;

  private $kanbasIsLoaded;
  private $kanbas;

  public function __construct($username, $password)
  {
    $this->username = $username;
    $this->password = $password;

    $pdo = MyPDO::getPDO();
    $table = self::getTableName();
    $t = $pdo->prepare("SELECT id, password FROM $table WHERE username = '$username'");
    $t->execute();
    $r = $t->fetch();
    if ($r === null) {
      $this->passwordBdd = null;
    } else {
      $this->id = $r->id;
      $this->passwordBdd = $r->password;
    }
    $this->kanbasIsLoaded = false;
    $this->kanbas = [];
  }


  public function isPresent() {
    return $this->passwordBdd !== null;
  }

  public function isCorrect() {
    return $this->isPresent() && $this->passwordBdd === $this->password;
  }

  public function insert()
  {
    $pdo = MyPDO::getPDO();
    $t = $pdo->prepare("INSERT INTO user (username, password) VALUES ('$this->username', '$this->password')");
    $t->execute();
  }

  public function getName()
  {
    return $this->username;
  }

  /**
   * Renvoie la liste des kanbas.
   * S'ils sont privés ou non.
   * @param $private
   * @return Kanban[]
   */
  public function getKanbas($private): array
  {
    $table = Kanban::getTableName();
    if (!$this->kanbasIsLoaded) {
      $pdo = MyPDO::getPDO();
      $id = $this->getId();
      $t = $pdo->prepare("SELECT id FROM $table WHERE ownerId = $id");
      $t->execute();
      foreach ($t->fetchAll() as $kanba) {
        array_push($this->kanbas, new Kanban($kanba->id));
      }
      $this->kanbasIsLoaded = true;
    }
    $r = [];
    foreach ($this->kanbas as $k) {
      if ($k->isPrivate() && $private) {
        array_push($r, $k);
      } else if (!$k->isPrivate() && !$private) {
        array_push($r, $k);
      }
    }
    return $r;

  }

  static function getUserBySession(array $session): User {
    if (isset($session['username']) && isset($session['password'])) {
      return new User($session['username'], $session['password']);
    }
    return null;
  }

  static function migrate(\PDO $pdo): void
  {
    $table = self::getTableName();
    $sql = <<<EOT
CREATE TABLE $table
(
    id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
    username text(20),
    password text(64)
);
EOT;
    $pdo->prepare($sql)->execute();
  }

  static function getTableName(): String
  {
    return "user";
  }

  static function hydrate(\PDO $pdo, int $n = 10): void
  {
    $table = self::getTableName();
    $faker = Factory::create("fr_FR");
    for ($i = 0; $i < $n; $i += 1) {
      $username = $faker->userName();
      $password = self::getFakePasswordHash();
      $pdo->prepare("INSERT INTO $table (username, password) VALUES ('$username', '$password')")
        ->execute();
    }
  }

  static function getFakeUser(\PDO $pdo)
  {
    $table = self::getTableName();
    $password = self::getFakePasswordHash();
    $t = $pdo->prepare("SELECT username FROM $table WHERE password = '$password'");
    $t->execute();
    return $t->fetchAll();
  }

  static function getFakePasswordHash()
  {
    return "b5d54c39e66671c9731b9f471e585d8262cd4f54963f0c93082d8dcf334d4c78";
  }

  /**
   * Effectue une mise à jour de l'entité dans la base de donnée.
   * @param \PDO $pdo
   * @param \stdClass $data
   * @return void
   */
  public function update(\PDO $pdo, \stdClass $data): void
  {
    // TODO: Implement update() method.
  }
}