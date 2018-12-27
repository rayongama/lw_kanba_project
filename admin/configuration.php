<?php
require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "autoload.php");

$modif = "false";

if (isset($_POST['title']) && isset($_POST['dburl']) && isset($_POST['dbname'])
  && isset($_POST['dbuser']) && isset($_POST['dbpassword'])) {
  $title = htmlspecialchars($_POST['title']);
  $dburl = htmlspecialchars($_POST['dburl']);
  $dbname = htmlspecialchars($_POST['dbname']);
  $dbuser = htmlspecialchars($_POST['dbuser']);
  $dbpasword = htmlspecialchars($_POST['dbpassword']);

  if (!empty($title)) {
    \Kanba\Configurator::setEntry("TITLE", $title);
    $modif = "true";
  }
  if (!empty($dburl)) {
    $modif = "true";
    \Kanba\Configurator::setEntry("DB_URL", $dburl);
  }
  if (!empty($dbname)) {
    $modif = "true";
    \Kanba\Configurator::setEntry("DB_NAME", $dbname);
  }
  if (!empty($dbuser)) {
    $modif = "true";
    \Kanba\Configurator::setEntry("DB_USERNAME", $dbuser);
  }
  if (!empty($dbpasword)) {
    $modif = "true";
    \Kanba\Configurator::setEntry("DB_PASSWORD", $dbpasword);
  }


}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kanba</title>
  <link href="/css/mymd.css" rel="stylesheet">
  <link href="/css/admin_configuration.css" rel="stylesheet">
</head>
<body>
<div class="loader none"><span></span></div>
<main>
  <h2>Panneau de configuration</h2>
  <a class="link" href="/admin/random_user.php">Choisir un utilisateur aléatoire</a>
  <br>
  <form method="post">
    <input type="hidden" name="modif" value="<?= $modif ?>">
    <div class="input not-empty" data-placeholder="Nom du site">
      <input type="text" name="title" value="<?= \Kanba\Configurator::getEntry("TITLE") ?>">
    </div>
    <br>
    <div class="input not-empty" data-placeholder="Base de donnée">
      <input type="text" name="dburl" value="<?= \Kanba\Configurator::getEntry("DB_URL") ?>">
    </div>
    <br>
    <div class="input not-empty" data-placeholder="Nom de la base">
      <input type="text" name="dbname" value="<?= \Kanba\Configurator::getEntry("DB_NAME") ?>">
    </div>
    <br>
    <div class="inline">
      <div class="input not-empty" data-placeholder="Nom d'utilisateur de la base de donnée">
        <input type="text" name="dbuser" value="<?= \Kanba\Configurator::getEntry("DB_USERNAME") ?>">
      </div>
      <div class="input not-empty" data-placeholder="Mot de passe de la base de donnée">
        <input type="password" name="dbpassword" value="<?= \Kanba\Configurator::getEntry("DB_PASSWORD") ?>">
      </div>
    </div>
    <br>
    <br>
    <button>Envoyer</button>
    <br>
    <br>
    <button type="button" class="btn-warning">Création de la base complête</button>
  </form>
</main>
<div class="snackbar">Modification(s) enregistrée(s).</div>
<script src="/js/mymd.js"></script>
<script src="/js/admin_configuration.js"></script>
</body>
</html>