<?php

namespace Kanba\Api;

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'load.php');

use Kanba\Kanba;
use Kanba\Todo;
use Kanba\TodoList;
use Kanba\User;

function error() {
    header('HTTP/1.0 403 Forbidden');
    echo 'You are forbidden!';
}

function transmitData($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    ob_end_flush();
}

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $q = htmlspecialchars($_GET['q']);
    switch($q) {
        case "kanba":
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                // TODO : L'utilisateur à le droit de charger ce kanba.
                $id = htmlspecialchars($_GET['id']);
                $k = Kanba::getKanbaById($id);
                $k->loadKanba();
                $d = new \stdClass();
                $d->title = $k->getTitle();
                $d->lists = $k->getLists();
                transmitData($d);
            } else {
                error();
            }
            break;
        case "user":
            if (isset($_GET['username']) && !empty($_GET['username'])
                && isset($_GET['password']) && !empty($_GET['password'])) {
                $username = $_GET['username'];
                $password = $_GET['password'];

                $user = new User($username, $password);
                transmitData($user->existsAndIsGood());

            } else {
                error();
            }
            break;
        default:
            error();
    }
} else if(isset($_POST['q']) && !empty($_POST['q'])) {
    $q = htmlspecialchars_decode($_POST['q']);
    switch ($q) {
        case "todo-edit":
            $user = new User($_SESSION['username'], $_SESSION['password']);
            // TODO: l'utilisateur à le droit de modifier cette tâche
            if ($user->existsAndIsGood() === true) {
                $data = new \stdClass();
                $data->id = isset($_POST['id']) ? intval($_POST['id']) : -1;
                $data->title = isset($_POST['title']) ? $_POST['title'] : '';
                $data->date = isset($_POST['date']) ? $_POST['date'] : '';
                $data->time = isset($_POST['time']) ? $_POST['time'] : '';
                $data->move = isset($_POST['move']) ? intval($_POST['move']) : -1;
                $data->list_id = isset($_POST['list_id']) ? intval($_POST['list_id']) : -1;
                if (isset($_POST['new']) && $_POST['new'] === "true") {
                    Todo::add($data);
                } else {
                    Todo::getById($data->id)->update($data);
                }

            }
    }
} else {
    error();
}

