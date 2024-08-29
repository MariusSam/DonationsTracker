<?php

namespace App;

use PDO;

class Database {
    private $pdo;

    public function __construct($dbFile) {
        $this->pdo = new PDO("sqlite:$dbFile");
    }

    public function getConnection() {
        return $this->pdo;
    }
}