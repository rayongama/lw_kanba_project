<?php

namespace Kanba;


class TodoList
{

    private $todos;

    public function __construct()
    {
        $this->todos = [];
    }

    public function addTodo($todo) {
        array_push($this->todos, $todo);
        return $this;
    }

    public static function getTodoListByKanbaId($kanbaId) {
        $pdo = MyPDO::getPDO();
        $t = $pdo->prepare("SELECT * FROM todo_list WHERE kanba_id = $kanbaId");
        $t -> execute();
        $r = $t->fetchAll();
        var_dump($r);
    }

}