<?php

namespace Kanban\Api;

use Kanban\Entity\Kanban;
use Kanban\Entity\Todo;
use Kanban\Entity\User;
use Kanban\MyPDO;

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "autoload.php");

$user = new User($_SESSION['username'], $_SESSION['password']);


function error()
{
  header('HTTP/1.0 403 Forbidden');
  echo 'You are forbidden!';
}

function transmitData($data)
{
  header('Content-Type: application/json');
  echo json_encode($data);
  ob_end_flush();
}

if (isset($_GET['q']) && !empty($_GET['q'])) {
  $q = htmlspecialchars($_GET['q']);
  switch ($q) {
    case "kanba-list":
      transmitData(Kanban::getPublicArray());
      break;
    case "kanba":
      if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = htmlspecialchars($_GET['id']);
        $k = new Kanban($id);
        $u = User::getUserBySession($_SESSION);
        if ($u->isCorrect()) {
          if ($k->isPrivate() && $k->belongsTo($u->getId()) || !$k->isPrivate()) {
            $k->loadKanba();
            $d = new \stdClass();
            $d->title = $k->getTitle();
            $d->lists = $k->getLists();
            $d->private = $k->isPrivate();
            $d->ownerId = $k->getOwnerId();
            transmitData($d);
          } else {
            error();
          }
        } else {
          error();
        }
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
        $data = new \stdClass();
        $data->isPresent = $user->isPresent();
        $data->isCorrect = $user->isCorrect();
        transmitData($data);

      } else {
        error();
      }
      break;
    case "kanba-edit":
      if (isset($_GET['id']) && !empty($_GET['id'])) {
        $data = new \stdClass();
        $data->id = htmlspecialchars($_GET['id']);
        if (isset($_GET['title']) && !empty($_GET['title'])) {
          $data->title = htmlspecialchars($_GET['title']);
        }
        if (isset($_GET['private']) && !empty($_GET['private'])) {
          $data->isPrivate = htmlspecialchars($_GET['private']) === "true" ? 1 : 0;
        }
        $k = new Kanban($data->id);
        $u = User::getUserBySession($_SESSION);
        if ($u->isCorrect() && $k->belongsTo($u->getId())) {
          $k->update(MyPDO::getPDO(), $data);
          transmitData($k->getSlug());
        } else {
          error();
        }
      } else {
        error();
      }
      break;
    case "kanba-remove":
      if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = htmlspecialchars($_GET['id']);
        $k = new Kanban($id);
        $u = User::getUserBySession($_SESSION);
        if ($u->isCorrect() && $k->belongsTo($u->getId())) {
          $k->remove();
        } else {
          error();
        }
      } else {
        error();
      }
      break;
    case "kanba-new":
      if (isset($_GET['ownerId']) && !empty($_GET['ownerId'])) {
        $ownerId = intval(htmlspecialchars($_GET['ownerId']));
        $u = User::getUserBySession($_SESSION);
        if ($u->isCorrect() && $u->getId() === $ownerId) {
          transmitData(Kanban::create($ownerId));
        } else {
          error();
        }
      }
      break;
    case "todo-remove":
      if (isset($_GET['id']) && !empty($_GET['id'])) {
        $todo = new Todo(htmlspecialchars($_GET['id']));
        if ($todo->isPresent() && $todo->getOwnerId() === User::getUserBySession($_SESSION)->getId()) {
          $todo->remove();
        } else {
          error();
        }

      } else {
        error();
      }
      break;
    default:
      error();
  }
} else if (isset($_POST['q']) && !empty($_POST['q'])) {
  $q = htmlspecialchars_decode($_POST['q']);
  switch ($q) {
    case "todo-edit":
      $u = User::getUserBySession($_SESSION);
      // TODO: l'utilisateur à le droit de modifier cette tâche
      if ($u->isCorrect()) {
        $data = new \stdClass();
        $data->id = isset($_POST['id']) ? intval($_POST['id']) : -1;
        $data->list_id = isset($_POST['list_id']) ? intval($_POST['list_id']) : -1;
        $data->title = isset($_POST['title']) ? $_POST['title'] : '';
        $data->date = isset($_POST['date']) ? $_POST['date'] : '';
        $data->time = isset($_POST['time']) ? $_POST['time'] : '';
        $data->move = isset($_POST['move']) ? intval($_POST['move']) : '';
        $data->description = isset($_POST['description']) ? $_POST['description'] : '';
        $todo = new Todo($data->id);
        if (isset($_POST['new']) && $_POST['new'] === "true") {
          $todo->add(MyPDO::getPDO(), $data);
        }else if ($todo->isPresent() && $todo->getOwnerId() === $u->getId()) {
          $pdo = MyPDO::getPDO();
          $todo->update($pdo, $data);
        } else {
          error();
        }
      }
  }
} else {
  error();
}

