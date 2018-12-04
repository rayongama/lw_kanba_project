<?php

namespace Kanba;


class Kanba
{

    private $id;
    private $lists;
    private $title;
    private $private;

    public function __construct($id, $title, $private)
    {
        $this->id = $id;
        $this->title = $title;
        $this->private = $private;
        $this->lists = [];
    }

    public function loadKanba() {
        $pdo = MyPDO::getPDO();
        $t = $pdo->prepare("SELECT * FROM todo_list WHERE kanba_id = $this->id");
        $t->execute();
        $p = MyPDO::getPDO();
        foreach ($t->fetchAll() as $l) {
            $list = new \stdClass();
            $list->title = $l->title;
            $list->id = $l->id;
            $list->todos = [];
            $t = $p->prepare("SELECT * FROM todo WHERE list_id = $l->id");
            $t->execute();
            $list->todos = $t->fetchAll();
            array_push($this->lists, $list);
        }
    }

    public function getLists() {
        return $this->lists;
    }

    public function isPrivate() {
        return $this->private;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getId() {
        return $this->id;
    }

    public function getSlug() {
        $t = $this->title;
        $t = str_replace(" ", "-", $t);
        $t = str_replace("'", "-", $t);
        return "$this->id-" . $t;
    }

    public function addList($todoList) {
        array_push($this->lists, $todoList);
    }

    public static function getKanbaById($id) {
        $pdo = MyPDO::getPDO();
        $t = $pdo->prepare("SELECT * FROM kanbas WHERE id = '$id'");
        $t->execute();
        $r = $t->fetch();
        return new Kanba($r->id, $r->title, $r->is_private);
    }

}