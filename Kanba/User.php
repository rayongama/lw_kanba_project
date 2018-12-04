<?php

namespace Kanba;

class User
{

    private $username;
    private $password;

    private $kanbasIsLoaded;
    private $kanbas;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        $this->kanbasIsLoaded = false;
        $this->kanbas = [];
    }

    public function existsAndIsGood() {
        $dbOptions = [
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ];
        $pdo = new \PDO("mysql:host=localhost:3306;dbname=kanba", 'root', 'root', $dbOptions );
        $t = $pdo->prepare("SELECT password FROM user WHERE username = '$this->username'");
        $t->execute();
        $r = $t->fetch();
        if (!$r) {
            return false;
        }
        if ($r->password === $this->password) {

            return true;
        }
        return "EXISTS";
    }

    public function insert() {
        $pdo = MyPDO::getPDO();
        $t = $pdo->prepare("INSERT INTO user (username, password) VALUES ('$this->username', '$this->password')");
        $t->execute();
    }

    public function getName() {
        return $this->username;
    }

    private function getId() {
        $pdo = MyPDO::getPDO();
        $t = $pdo->prepare("SELECT id FROM user WHERE username = '$this->username' AND password = '$this->password'");
        $t->execute();
        return $t->fetch()->id;
    }

    public function getKanbas($private) {
        if (!$this->kanbasIsLoaded) {
            $this->getId();
            $pdo = MyPDO::getPDO();
            $id = $this->getId();
            $t = $pdo->prepare("SELECT * FROM kanbas WHERE user_id = $id");
            $t->execute();
            foreach ($t->fetchAll() as $kanba) {
                array_push($this->kanbas, new Kanba($kanba->id, $kanba->title, $kanba->is_private));
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

}