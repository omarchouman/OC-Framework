<?php

namespace Core\Database;

abstract class Migration
{
    protected $connection;
    protected $table;

    public function __construct()
    {
        $this->connection = $this->getConnection();
    }

    abstract public function up();
    abstract public function down();

    public function getConnection()
    {
        $config = require __DIR__ . '/../../config/database.php';
        
        try {
            $tempDsn = "mysql:host={$config['host']}";
            $tempConnection = new \PDO($tempDsn, $config['username'], $config['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            $tempConnection->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET {$config['charset']} COLLATE {$config['collation']}");
            
            $dsn = "mysql:host={$config['host']};dbname={$config['database']}";
            return new \PDO($dsn, $config['username'], $config['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (\PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    protected function createTable($table, $callback)
    {
        $this->table = $table;
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        
        $sql = $blueprint->toSql();
        $this->connection->exec($sql);
    }

    protected function dropTable($table)
    {
        $this->connection->exec("DROP TABLE IF EXISTS {$table}");
    }
} 