<?php

namespace minichan\db;

class Connection
{
  private \PDO $pdo;

  public function __construct(string $host, string $dbname, string $username, string $password)
  {
    $this->pdo = new \PDO("mysql:host=$host;dbname=$dbname", $username, $password, [
      \PDO::ATTR_PERSISTENT => true,
      \PDO::ATTR_EMULATE_PREPARES => false,
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    ]);
  }

  public function transaction(callable $handler): bool
  {
    $success = false;

    try {
      $this->begin();
      call_user_func($handler, $this->pdo);
      $success = $this->commit();
    } catch (\Exception $e) {
      $this->rollback();
    }

    return $success;
  }

  private function begin(): bool
  {
    return $this->pdo->beginTransaction();
  }

  private function commit(): bool
  {
    return $this->pdo->commit();
  }

  private function rollback(): bool
  {
    return $this->pdo->rollBack();
  }
}
