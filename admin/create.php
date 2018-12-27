<?php

use Kanba\MyPDO;

use Kanba\Entity\AbstractEntity;
use Kanba\Entity\Kanba;
use Kanba\Entity\TodoList;
use Kanba\Entity\User;
use Kanba\Entity\Todo;

require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "autoload.php");

$database = \Kanba\Configurator::getEntry("DB_NAME");

?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Configuration - Création de la base</title>
</head>
<body>
<pre>
  <?php
  try {
    $pdo = MyPDO::getPDO('');
  } catch (Exception $e) {
    header('HTTP/1.0 404 Not Found');
    die;
  }

  try {
    $pdo->prepare("DROP DATABASE $database")->execute();
    echo "\nNettoyage et suppression de la base de donnée $database\n\n";
    echo "Création de la nouvelle base de donnée $database\n\n";
    $pdo->prepare("CREATE DATABASE kanba")->execute();
  } catch (Exception $e) {
    header('HTTP/1.0 303 Forbidden');
    die;
  }

  $pdo = \Kanba\MyPDO::getPDO();
  foreach (AbstractEntity::getList() as $entity) {
    try {
      $class = new ReflectionClass("Kanba\\Entity\\$entity");
    } catch (ReflectionException $e) {
      echo "$e";
    }
    $class = $class->newInstanceWithoutConstructor();
    /**
     * @var \Kanba\Entity\IEntity $class
     */
    $class->migrate($pdo);
    $table = $class->getTableName();
    echo "Création de la table $table pour l'entité $entity\n";
  }
  echo "\n";
  echo "\nHydratation des utilisateurs\n";
  User::hydrate($pdo);
  echo "Hydratation des kanbas\n";
  Kanba::hydrate($pdo);
  echo "Hydratation des listes de tâches\n";
  TodoList::hydrate($pdo);
  echo "Hydratation des tâches\n";
  Todo::hydrate($pdo);
  ?>
</pre>
</body>
</html>
