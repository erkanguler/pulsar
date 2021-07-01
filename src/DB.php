<?php

namespace Erkan\App;

use PDO;
use PDOException;

use function Erkan\App\logError;

class DB
{

    private PDO $pdo;

    function __construct()
    {
    }

    public function getPDO(): PDO
    {

        if (isset($this->pdo)) {
            return $this->pdo;
        }

        try {
            $this->pdo = new PDO('mysql:host=localhost;dbname=boozt', $user = 'root', $pass = 'host07');
            return $this->pdo;
        } catch (PDOException $e) {
            http_response_code(500);
            $errMsg = 'Error: Database connection could not be established.';
            logError(__LINE__, __FILE__, $errMsg);
            die($errMsg);
        }
    }
}
