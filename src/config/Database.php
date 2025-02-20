<?php

namespace App\config;

class Database {
    private $host = 'localhost';
    private $db_name = 'seminariophp';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function connect() {
        $this->conn = new \mysqli($this->host, $this->username, $this->password, $this->db_name);
        if ($this->conn->connect_errno) {
            echo "Error al conectarse con la base de datos: " . $this->conn->connect_error;
        }
        return $this->conn;
    }
}