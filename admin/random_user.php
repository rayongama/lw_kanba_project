<?php

use Faker\Factory;
use Kanba\MyPDO;
use Kanba\Entity\User;

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "autoload.php");

$faker = Factory::create("fr_FR");

$_SESSION['username'] = $faker->randomElement(User::getFakeUser(MyPDO::getPDO()))->username;
$_SESSION['password'] = User::getFakePasswordHash();
header("Location: /");