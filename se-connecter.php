<?php

use Kanba\Entity\User;

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "autoload.php");

$error = false;
if (isset($_POST['username']) && !empty($_POST['username'])
  && isset($_POST['password']) && !empty($_POST['password'])) {
  $username = htmlspecialchars($_POST['username']);
  $password = htmlspecialchars($_POST['password']);
  $user = new User($username, $password);
  if ($user->isCorrect() !== true) {
    $error = true;
  } else {
    $_SESSION['username'] = $username;
    $_SESSION['password'] = $password;
    header("Location: /");
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= \Kanba\Configurator::getEntry("TITLE") ?> - Connexion</title>
  <link href="/css/mymd.css" rel="stylesheet">
  <link href="/css/login.css" rel="stylesheet">
</head>
<body>
<div class="container">
  <h6>Connecte-toi</h6>
  <a href="inscription.php"><span
        class="subtitle1"><?= $error ? "Identifiants invalides" : "Ou clique ici pour te créer un compte !" ?></span></a>
  <br>
  <br>
  <form method="post" action="se-connecter.php">
    <div class="input" data-placeholder="Nom d'utilisateur">
      <input type="text" name="username"/>
    </div>
    <br/>
    <div class="input" data-placeholder="Mot de passe">
      <input type="password" name="password"/>
    </div>
    <br/>
    <button type="button">Se connecter</button>
  </form>
</div>

<script src="/js/connexion.js"></script>

<script src="/js/sjcl.js"></script>
<script src="/js/mymd.js"></script>
</body>
</html>