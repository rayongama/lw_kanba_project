<?php

use Kanba\Entity\Kanba;

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "autoload.php");

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= \Kanba\Configurator::getEntry("TITLE") ?> - La liste des Kanbas publics</title>
  <link href="/css/mymd.css" rel="stylesheet">
  <link href="/css/list.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
        rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div class="container">
  <h4>La liste des Kanbas publics</h4>
  <ul>
    <?php foreach(Kanba::getPublicArray() as $k): $kanba = new Kanba($k->id)?>
      <a href="/<?= $kanba->getSlug() ?>#visualiser">
        <li>
          <span><?= $k->title ?> </span>
          <span>par</span>
          <span><?= $kanba->getOwnerName() ?></span>
        </li>
      </a>
    <?php endforeach; ?>
  </ul>
</div>


<script src="/js/mymd.js"></script>
</body>
</html>