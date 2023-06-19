<?php

namespace app\migrate;

use \PDO;

class ProductMigration
{
    private PDO $connection;
    private $rootDir;
    public function __construct(PDO $pdo, $dir)
    {
        $this->connection = $pdo;
        $this->rootDir = $dir;
    }

    public function migrate(): void
    {
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                sku TEXT NOT NULL,
                name TEXT NOT NULL,
                price DECIMAL(10, 2) NOT NULL,
                type TEXT NOT NULL
            );
        ");
    }
    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigratons = $this->getAppliedMigrations();

        $newMigrations = [];
        $instance = null;
        $files = scandir($this->rootDir . '/src/migrate/migrations');
        $toApplyMigrations = array_diff($files, $appliedMigratons);
        foreach ($toApplyMigrations as $migration) {
            if ($migration === '.' || $migration === '..') {
                continue;
            }
            require_once($this->rootDir  . '\src\migrate\migrations\\' . $migration);
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $className = "app\migrate\migrations\\" . $className;
            $instance = new $className();
            $this->log("Applying migration $migration");
            $instance->up($this->connection);
            $this->log("Applied migration $migration");
            $newMigrations[] = $migration;
        }
        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log("All migrations are applied");
        }
    }

    //create migrations table in database
    public function createMigrationsTable()
    {
        $this->connection->exec("CREATE TABLE IF NOT EXISTS migrations(
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
         );");
    }

    //get exsisting migrations from database
    public function getAppliedMigrations()
    {
        $statement = $this->connection->prepare("SELECT migration FROM migrations");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    //saves migrated file names in database
    public function saveMigrations(array $migrations)
    {
        $str = implode(",", array_map(fn ($m) => "('$m')", $migrations));
        $statement = $this->connection->prepare("INSERT INTO migrations (migration) VALUES
      $str
      ");
        $statement->execute();
    }
    protected function log($message)
    {
        echo '[' . date('Y-md- H:i:s') . '] - ' . $message . PHP_EOL;
    }
}
