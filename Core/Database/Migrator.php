<?php

namespace Core\Database;

class Migrator
{
    protected $connection;
    protected $migrationsPath;
    protected $migrationsTable = 'migrations';

    public function __construct()
    {
        $this->connection = (new BaseMigration())->getConnection();
        $this->migrationsPath = __DIR__ . '/../../migrations';
        $this->createMigrationsTable();
    }

    protected function createMigrationsTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL
        )";
        
        $this->connection->exec($sql);
    }

    public function run()
    {
        $files = $this->getMigrationFiles();
        $batch = $this->getNextBatchNumber();

        foreach ($files as $file) {
            $migration = $this->getMigrationName($file);
            
            if (!$this->hasRun($migration)) {
                $this->runMigration($file, $migration, $batch);
            }
        }
    }

    public function rollback()
    {
        $lastBatch = $this->getLastBatchNumber();
        
        if ($lastBatch === 0) {
            return;
        }

        $migrations = $this->getMigrationsForBatch($lastBatch);
        
        foreach (array_reverse($migrations) as $migration) {
            $this->rollbackMigration($migration);
        }
    }

    protected function getMigrationFiles()
    {
        $files = glob($this->migrationsPath . '/*.php');
        sort($files);
        return $files;
    }

    protected function getMigrationName($file)
    {
        return basename($file, '.php');
    }

    protected function hasRun($migration)
    {
        $stmt = $this->connection->prepare("SELECT COUNT(*) FROM {$this->migrationsTable} WHERE migration = ?");
        $stmt->execute([$migration]);
        return (bool) $stmt->fetchColumn();
    }

    protected function runMigration($file, $migration, $batch)
    {
        require_once $file;
        $class = $this->getMigrationClass($migration);
        $instance = new $class();
        
        $instance->up();
        
        $stmt = $this->connection->prepare("INSERT INTO {$this->migrationsTable} (migration, batch) VALUES (?, ?)");
        $stmt->execute([$migration, $batch]);
    }

    protected function rollbackMigration($migration)
    {
        $file = $this->migrationsPath . '/' . $migration . '.php';
        require_once $file;
        
        $class = $this->getMigrationClass($migration);
        $instance = new $class();
        
        $instance->down();
        
        $stmt = $this->connection->prepare("DELETE FROM {$this->migrationsTable} WHERE migration = ?");
        $stmt->execute([$migration]);
    }

    protected function getMigrationClass($migration)
    {
        // Remove the timestamp prefix (e.g., "2025_05_24_223045_")
        $name = preg_replace('/^\d+_\d+_\d+_\d+_/', '', $migration);
        
        // Convert snake_case to PascalCase
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        
        return "\\Migrations\\{$className}";
    }

    protected function getNextBatchNumber()
    {
        $stmt = $this->connection->query("SELECT MAX(batch) FROM {$this->migrationsTable}");
        return $stmt->fetchColumn() + 1;
    }

    protected function getLastBatchNumber()
    {
        $stmt = $this->connection->query("SELECT MAX(batch) FROM {$this->migrationsTable}");
        return (int) $stmt->fetchColumn();
    }

    protected function getMigrationsForBatch($batch)
    {
        $stmt = $this->connection->prepare("SELECT migration FROM {$this->migrationsTable} WHERE batch = ? ORDER BY id");
        $stmt->execute([$batch]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
} 