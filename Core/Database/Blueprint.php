<?php

namespace Core\Database;

class Blueprint
{
    protected $table;
    protected $columns = [];
    protected $primaryKey = null;
    protected $foreignKeys = [];

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function id()
    {
        $this->bigIncrements('id');
        $this->primaryKey = 'id';
        return $this;
    }

    public function bigIncrements($column)
    {
        $this->columns[] = "`{$column}` BIGINT UNSIGNED AUTO_INCREMENT";
        return $this;
    }

    public function string($column, $length = 255)
    {
        $this->columns[] = "`{$column}` VARCHAR({$length})";
        return $this;
    }

    public function text($column)
    {
        $this->columns[] = "`{$column}` TEXT";
        return $this;
    }

    public function integer($column)
    {
        $this->columns[] = "`{$column}` INT";
        return $this;
    }

    public function bigInteger($column)
    {
        $this->columns[] = "`{$column}` BIGINT";
        return $this;
    }

    public function boolean($column)
    {
        $this->columns[] = "`{$column}` BOOLEAN";
        return $this;
    }

    public function timestamp($column)
    {
        $this->columns[] = "`{$column}` TIMESTAMP";
        return $this;
    }

    public function timestamps()
    {
        $this->timestamp('created_at')->nullable();
        $this->timestamp('updated_at')->nullable();
        return $this;
    }

    public function nullable()
    {
        $lastColumn = array_pop($this->columns);
        $this->columns[] = $lastColumn . " NULL";
        return $this;
    }

    public function default($value)
    {
        $lastColumn = array_pop($this->columns);
        $this->columns[] = $lastColumn . " DEFAULT " . (is_string($value) ? "'{$value}'" : $value);
        return $this;
    }

    public function foreign($column)
    {
        $this->foreignKeys[] = [
            'column' => $column,
            'references' => null,
            'on' => null,
            'onDelete' => null,
            'onUpdate' => null
        ];
        return $this;
    }

    public function references($column)
    {
        $lastKey = array_pop($this->foreignKeys);
        $lastKey['references'] = $column;
        $this->foreignKeys[] = $lastKey;
        return $this;
    }

    public function on($table)
    {
        $lastKey = array_pop($this->foreignKeys);
        $lastKey['on'] = $table;
        $this->foreignKeys[] = $lastKey;
        return $this;
    }

    public function onDelete($action)
    {
        $lastKey = array_pop($this->foreignKeys);
        $lastKey['onDelete'] = $action;
        $this->foreignKeys[] = $lastKey;
        return $this;
    }

    public function onUpdate($action)
    {
        $lastKey = array_pop($this->foreignKeys);
        $lastKey['onUpdate'] = $action;
        $this->foreignKeys[] = $lastKey;
        return $this;
    }

    public function toSql()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (";
        $sql .= implode(', ', $this->columns);

        if ($this->primaryKey) {
            $sql .= ", PRIMARY KEY (`{$this->primaryKey}`)";
        }

        foreach ($this->foreignKeys as $key) {
            $sql .= ", FOREIGN KEY (`{$key['column']}`) REFERENCES `{$key['on']}` (`{$key['references']}`)";
            
            if ($key['onDelete']) {
                $sql .= " ON DELETE {$key['onDelete']}";
            }
            
            if ($key['onUpdate']) {
                $sql .= " ON UPDATE {$key['onUpdate']}";
            }
        }

        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        return $sql;
    }
} 