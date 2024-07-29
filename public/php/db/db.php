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

  public function transaction(callable $handler): void
  {
    try {
      $this->begin();
      call_user_func($handler, $this->pdo);
      $this->commit();
    } catch (\Exception $e) {
      $this->rollback();
      throw $e;
    }
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

class Sql
{
  private string $raw;
  private bool $literal;

  private function __construct(string $raw)
  {
    $this->raw = $raw;
    $this->literal = false;
  }

  public static function new(string $raw = ''): Sql
  {
    return new Sql($raw);
  }

  public function get(): string
  {
    return $this->raw;
  }

  public function literal(string $raw): Sql
  {
    $this->raw = $raw;
    $this->literal = true;

    return $this;
  }

  public function is_literal(): bool
  {
    return $this->literal;
  }

  public function select(array $cols): Sql
  {
    $cols_n = count($cols);

    $this->raw .= "SELECT\n";
    foreach ($cols as $idx => $col) {
      $this->raw .= $col;
      if ($idx !== $cols_n - 1) {
        $this->raw .= ',';
      }
      $this->raw .= "\n";
    }

    return $this;
  }

  public function from(Sql $sql, ?string $as = null): Sql
  {
    if ($sql->is_literal()) {
      $this->raw .= "FROM {$sql->get()}";
    } else {
      $this->raw .= "FROM ({$sql->get()})";
    }

    if ($as != null) {
      $this->raw .= " AS $as";
    }

    $this->raw .= "\n";

    return $this;
  }

  public function where(Sql $sql): Sql
  {
    if ($sql->is_literal()) {
      $this->raw .= "WHERE {$sql->get()}\n";
    } else {
      $this->raw .= "WHERE ({$sql->get()})\n";
    }

    return $this;
  }

  public function conditional(string $lhs, string $op, int|string $rhs): Sql
  {
    if (gettype($rhs) === 'string') {
      $this->raw .= "$lhs $op \'$rhs\'\n";
    } else {
      $this->raw .= "$lhs $op $rhs\n";
    }

    return $this;
  }

  public function and(): Sql
  {
    $this->raw .= "AND\n";

    return $this;
  }

  public function or(): Sql
  {
    $this->raw .= "OR\n";

    return $this;
  }

  public function op(string $op): Sql
  {
    $this->raw .= "$op\n";

    return $this;
  }
}
