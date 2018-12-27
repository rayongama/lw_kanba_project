<?php

namespace Kanba\Entity;

/**
 * Interface IEntity
 * @package Kanba\Entity
 */
interface IEntity
{

  /**
   * Récupère l'identifiant unique de l'entité.
   * @return int
   */
  public function getId(): int;

  /**
   * Effectue une mise à jour de l'entité dans la base de donnée.
   * @param \PDO $pdo
   * @param \stdClass $data
   * @return void
   */
  public function update(\PDO $pdo, \stdClass $data): void;

  /**
   * Effectue une migration de la table correspondante à l'entité.
   * @param \PDO $pdo
   * @return void
   */
  static function migrate(\PDO $pdo): void;

  /**
   * Récupère le nom de table correspondante à l'entité.
   * @return String
   */
  static function getTableName(): String;

  /**
   * Effectue une hydration de la table correspondante à l'entité.
   * @param \PDO $pdo
   * @param int $n
   * @return void
   */
  static function hydrate(\PDO $pdo, int $n = 10): void;

}