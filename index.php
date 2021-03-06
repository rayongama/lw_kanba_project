<?php

use Kanban\Entity\User;

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "autoload.php");

$id = "";
if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
  $user = User::getUserBySession($_SESSION);
}
$isLogged = false;
if (isset($user) && $user->isCorrect()) {
  $isLogged = true;
  $id = $user->getId();
}

if (!$isLogged) {
  header("Location: se-connecter.php");
}

$c = 0;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= \Kanban\Configurator::getEntry("TITLE") ?> - Page d'accueil</title>
  <link href="/css/mymd.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
        rel="stylesheet">
  <link href="/css/index.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body data-owner="<?= $id ?>">
<div class="side-nav">
  <a href="/" class="side-nav-main-title"><?= \Kanban\Configurator::getEntry("TITLE") ?></a>
  <span class="side-nav-title">Vos tableaux privés</span>
  <ul class="side-nav-section">
    <?php foreach ($user->getKanbas(true) as $k): $c++;?>
      <li><a onclick="kanbaNav('<?= $k->getSlug() ?>')" class="side-nav-link"><?= $k->getTitle() ?></a></li>
    <?php endforeach; ?>
  </ul>
  <span class="side-nav-title">Vos tableaux public</span>
  <ul class="side-nav-section">
    <?php foreach ($user->getKanbas(false) as $k): $c++;?>
      <li><a onclick="kanbaNav('<?= $k->getSlug() ?>')" class="side-nav-link"><?= $k->getTitle() ?></a></li>
    <?php endforeach; ?>
  </ul>
</div>
<header class="top-bar">
  <i title="Menu" class="hamburger material-icons">menu</i>
  <div class="kanba-edit">
    <span>Bonjour <?= $user->getName() ?></span>
    <label for="is-private" class="checkbox">
    <span class="switch">
      <input type="checkbox" id="is-private" class="checkbox" data-checked="false">
      <span class="switch-container">
        <span class="switch-no">Public</span>
        <span class="switch-mid"></span>
        <span class="switch-yes">Privé</span>
      </span>
    </span>
    </label>
    <button class="btn-warning">Supprimer</button>
  </div>
  <a href="/logout.php"><i title="Se déconnecter" class="float-right material-icons">clear</i></a>
</header>
<div class="todo-edit not-visible">
  <form>
    <span class="subtitle1">Edition de la tâche</span>
    <i class="material-icons">clear</i>
    <br>
    <br>
    <div class="input not-empty" data-placeholder="Titre de la tâche">
      <input type="text" name="title" value="Bonjour 1">
    </div>
    <br>
    <details>
      <br>
      <div class="input not-empty rows-2" data-placeholder="Description">
        <textarea rows="3"></textarea>
      </div>
    </details>

    <br>
    <div class="input not-empty no-control" data-placeholder="Date d'échéance">
      <input type="date" name="date" value="2018-11-15">
      <span>à </span>
      <input type="time" name="hour" value="12:12">
    </div>
    <br>
    <div class="input not-empty" data-placeholder="Déplacer vers">
      <select name="move" data-defaultvalue="1"></select>
    </div>
    <br>
    <button type="button">Enregistrer</button>
    <br>
    <button type="reset">Réinitialiser</button>
    <br>
    <button type="button" class="btn-warning">Supprimer</button>
  </form>
</div>
<main>
  <div class="container">
    <a href="/les-kanbans.php" class="link"><h5>La liste des kanbans publics</h5></a>
    <br/>
    <a class="link"><h5>Créer un nouveau kanban</h5></a>
    <br/>
    <?php if ($c === 0): ?>
      <p>Vous ne disposez d'aucun kanban.</p>
    <?php elseif ($c === 1): ?>
      <p>Vous disposez d'un unique kanban.</p>
    <?php else: ?>
      <p>Vous disposez de <?= $c ?> kanban.</p>
    <?php endif; ?>
  </div>
</main>
<div class="snackbar">Modification(s) enregistrée(s).</div>

<script src="/js/todo_page.js"></script>
<script src="/js/Kanban.js"></script>

<script src="/js/mymd.js"></script>
</body>
</html>