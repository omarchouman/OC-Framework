<?php

namespace Core;

use PDO;

class Database
{
    public PDO $connection;
    public $statement;

    public function __construct($config, $username = 'root', $password = '')
    {
        $dsn = 'mysql:'.http_build_query($config, '', ';');

        $this->connection = new PDO($dsn, $username, $password, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    /**
     * @param $query
     * @param  array  $params
     * @return $this
     */
    public function query($query, array $params = []): static
    {
        $this->statement = $this->connection->prepare($query);
        $this->statement->execute($params);

        return $this;
    }

    /**
     * @return mixed
     */
    public function get(): mixed
    {
        return $this->statement->fetchAll();
    }

    /**
     * @return mixed
     */
    public function find(): mixed
    {
        return $this->statement->fetch();
    }

    /**
     * @return mixed
     */
    public function findOrFail(): mixed
    {
        $result = $this->find();

        if (!$result) {
            abort();
        }

        return $result;
    }
}