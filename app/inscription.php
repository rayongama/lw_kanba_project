<?php
session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "autoload.php");

$error = false;

if (isset($_POST['username']) && !empty($_POST['username'])
  && isset($_POST['password']) && !empty($_POST['password'])) {
  $username = htmlspecialchars($_POST['username']);
  $password = htmlspecialchars($_POST['password']);
  $user = new \Kanban\User($username, $password);
  if ($user->existsAndIsGood() === "EXISTS") {
    $error = true;
  } else {
    $user->insert();
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
  <title><?= \Kanban\Configurator::getEntry("TITLE") ?> - Inscription</title>
  <link href="/css/mymd.css" rel="stylesheet">
  <link href="/css/login.css" rel="stylesheet">
</head>
<body>
<div class="container">
  <h6>Tu es nouveau ?</h6>
  <a href="se-connecter.php"><span
        class="subtitle1"><?= $error ? "Ce nom d'utilisateur est déjà utilisé" : "Ou clique ici si tu es déjà inscrit !" ?></span></a>
  <br>
  <br>
  <form method="post" action="inscription.php">
    <div class="input" data-placeholder="Nom d'utilisateur">
      <input type="text" name="username"/>
    </div>
    <br/>
    <div class="input" data-placeholder="Mot de passe">
      <input type="password" name="password"/>
    </div>
    <br/>
    <button type="button">S'inscrire</button>
  </form>
</div>

<script src="/js/inscription.js"></script>

<script src="/js/mymd.js"></script>
<script src="/js/sjcl.js"></script>
</body>
</html>