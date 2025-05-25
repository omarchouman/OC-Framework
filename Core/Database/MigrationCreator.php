<?php

namespace Core\Database;

class MigrationCreator
{
    protected $migrationsPath;
    protected $stubsPath;

    public function __construct()
    {
        $this->migrationsPath = __DIR__ . '/../../migrations';
        $this->stubsPath = __DIR__ . '/../../stubs';
    }

    public function create($name)
    {
        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_{$name}.php";
        $path = $this->migrationsPath . '/' . $filename;

        $stub = $this->getStub();
        $stub = str_replace('{{class}}', $this->getClassName($name), $stub);
        $stub = str_replace('{{table}}', $this->getTableName($name), $stub);

        file_put_contents($path, $stub);

        return $path;
    }

    protected function getStub()
    {
        $stubPath = $this->stubsPath . '/migration.stub';
        if (!file_exists($stubPath)) {
            throw new \RuntimeException('Migration stub file not found at: ' . $stubPath);
        }
        return file_get_contents($stubPath);
    }

    protected function getClassName($name)
    {
        $parts = explode('_', $name);
        return implode('', array_map('ucfirst', $parts));
    }

    protected function getTableName($name)
    {
        $parts = explode('_', $name);
        return end($parts);
    }
} 