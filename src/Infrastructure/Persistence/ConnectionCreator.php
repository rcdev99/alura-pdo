<?php

namespace Alura\Pdo\Infrastructure\Persistence;

use PDO;

class ConnectionCreator
{
    public static function createConnection():PDO
    {
        $path = __DIR__ . '/../../../banco.sqlite';

        $connection = new PDO('sqlite:' . $path);
        //Necessário para exibições de erro em transações do BD
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $connection;
    }
}