<?php

namespace Kanba;


class Todo
{

    private $id;

    private $title;
    private $done;
    private $date;
    private $time;
    private $list_id;

    public function __construct($id, $title, $date, $time, $list_id)
    {
        $this->id = $id;

        $this->title = $title;
        $this->done = false;
        $this->date = $date;
        $this->time = $time;
        $this->list_id = $list_id;
    }

    public function toggle() {
        $this->done = !$this->done;
        return $this;
    }

    public function update($data)
    {
        $pdo = MyPDO::getPDO();
        $sets = [];
        if ($data->title) {
            array_push($sets, "title = '$data->title'");
        }
        if ($data->date) {
            array_push($sets, "date = '$data->date'");
        }
        if ($data->time) {
            array_push($sets, "time = '$data->time'");
        }
        if ($data->move) {
            array_push($sets, "list_id = '$data->move'");
        }
        $pdo->prepare("UPDATE todo SET " . join(",", $sets) . "WHERE id = $this->id")
            ->execute();
    }

    public static function getById($id) {
        $pdo = MyPDO::getPDO();
        $t = $pdo->prepare("SELECT * FROM todo WHERE id = $id");
        $t->execute();
        $r = $t->fetch();
        return new Todo($r->id, $r->title, $r->date, $r->time, $r->list_id);
    }

    public static function add($data) {
        $pdo = MyPDO::getPDO();
        $t = $pdo->prepare("INSERT INTO todo (title, date, time, list_id) VALUES('$data->title', '$data->date', '$data->time', $data->list_id)");
        $t->execute();
    }

}