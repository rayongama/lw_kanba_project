<?php

namespace Kanban\Entity;

use Kanban\MyPDO;

class Todo extends AbstractEntity
{

  protected $title;
  protected $date;
  protected $time;
  protected $todoListId;
  protected $description;

  private $ownerId;
  private $kanbaId;

  public function __construct($id)
  {
    $this->id = $id;
  }


  /*
   * GETTERS
   */

  /**
   * Récupère le titre de la tâche.
   * @return string
   */
  public function getTitle(): string {
    $this->getProperty('title');
  }

  /**
   * Récupère la date de la tâche.
   * @return string
   */
  public function getDate(): string {
    $this->getProperty('date');
  }

  /**
   * Récupère l'heure de la tâche.
   * @return string
   */
  public function getTime(): string {
    $t = $this->getProperty('time');
    return substr($t, -3);
  }

  public function getOwnerId(): int {
    if ($this->kanbaId === null) {
      $this->kanbaId = intval(MyPDO::getFieldValueById(TodoList::getTableName(), $this->getListId(), 'kanba_id'));
    }
    if ($this->ownerId === null) {
      $this->ownerId = intval(MyPDO::getFieldValueById(Kanban::getTableName(), $this->kanbaId, 'ownerId'));
    }
    return $this->ownerId;
  }


  /**
   * Récupère la description de la tâche.
   * @return string
   */
  public function getDescription(): string {
    $this->getProperty('description');
  }

  /**
   * Récupère l'id de la liste associée à la tâche.
   * @return string
   */
  public function getListId(): string {
    return $this->getProperty('todoListId');
  }


  public function update(\PDO $pdo, \stdClass $data)
  {
    if ($data->title) {
      $this->setProperty('title', $data->title);
    }
    if ($data->date) {
      $this->setProperty('date', $data->date);
    }
    if ($data->time) {
      $this->setProperty('time', $data->time);
    }
    if ($data->move) {
      $this->setProperty('todoListId', $data->move);
    }
    if ($data->description) {
      $this->setProperty('description', $data->description);
    }
  }

  public function add(\PDO $pdo, \stdClass $data)
  {
    $table = self::getTableName();
    $t = $pdo->prepare("INSERT INTO $table (title, description, todoListId, date, time) VALUES('$data->title', '$data->description', $data->list_id, '$data->date', '$data->time')");
    $t->execute();
  }

  static function migrate(\PDO $pdo)
  {
    $table = self::getTableName();
    $sql = <<<EOT
CREATE TABLE $table
(
    id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
    title text(20) NOT NULL,
    description text,
    todoListId int,
    date date,
    time time
);
EOT;
    $pdo->prepare($sql)->execute();
  }

  static function getTableName(): String
  {
    return "todo";
  }

  static function hydrate(\PDO $pdo, int $n = 10)
  {
    $f = static::getLocalFaker();
    $table = static::getTableName();
    foreach (TodoList::getIds($pdo) as $id) {
      $m = $f->numberBetween(0, $n);
      for ($i = 0; $i < $m; $i += 1) {
        $title = addslashes($f->company());
        $description = addslashes($f->catchPhrase());
        $date = $f->date();
        $time = $f->time('H:i');
        $pdo->prepare("INSERT INTO $table (title, description, todoListId, date, time) VALUES ('$title', '$description', $id, '$date', '$time')")
          ->execute();
      }
    }

  }

}