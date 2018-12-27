<?php require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "autoload.php"); ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Titre de la page</title>
  <link rel="stylesheet" href="style.css">
  <script src="script.js"></script>
</head>
<body>
<pre><?php

  (new \Kanba\Entity\Todo(10))->getOwnerId();
  (new \Kanba\Entity\Todo(11))->getOwnerId();
  (new \Kanba\Entity\Todo(12))->getOwnerId();
  (new \Kanba\Entity\Todo(13))->getOwnerId();
  (new \Kanba\Entity\Todo(100))->getOwnerId();
  (new \Kanba\Entity\Todo(102))->getOwnerId();
  (new \Kanba\Entity\Todo(254))->getOwnerId();
  (new \Kanba\Entity\Todo(256))->getOwnerId();
  (new \Kanba\Entity\Todo(5))->getOwnerId();
  (new \Kanba\Entity\Todo(1241544))->getOwnerId();



  ?>
</pre>
</body>
</html>