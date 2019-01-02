<?php

namespace Kanban\Entity;


use Kanban\MyPDO;

class TodoList extends AbstractEntity
{

  private $todos;

  public function __construct()
  {
    $this->todos = [];
  }

  public function addTodo($todo)
  {
    array_push($this->todos, $todo);
    return $this;
  }

  public static function getTodoListByKanbaId($kanbaId)
  {
    $pdo = MyPDO::getPDO();
    $t = $pdo->prepare("SELECT * FROM todo_list WHERE kanba_id = $kanbaId");
    $t->execute();
    $r = $t->fetchAll();
    var_dump($r);
  }

  static function migrate(\PDO $pdo)
  {
    $table = self::getTableName();
    $sql = <<<EOT
CREATE TABLE $table
(
    id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
    kanba_id int NOT NULL,
    title text(20)
);
EOT;
    $pdo->prepare($sql)->execute();
  }

  static function getTableName(): String
  {
    return "todo_list";
  }

  static function hydrate(\PDO $pdo, int $n = 10)
  {
    foreach (Kanban::getIds($pdo) as $kanba_id) {
      $pdo->prepare("INSERT INTO todo_list (kanba_id, title) VALUES ($kanba_id, 'Stories')")->execute();
      $pdo->prepare("INSERT INTO todo_list (kanba_id, title) VALUES ($kanba_id, 'Terminées')")->execute();
    }
  }

  /**
   * Récupère l'identifiant unique de l'entité.
   * @return int
   */
  public function getId(): int
  {
    // TODO: Implement getId() method.
  }

  /**
   * Effectue une mise à jour de l'entité dans la base de donnée.
   * @param \PDO $pdo
   * @param \stdClass $data
   * @return void
   */
  public function update(\PDO $pdo, \stdClass $data)
  {
    // TODO: Implement update() method.
  }
}