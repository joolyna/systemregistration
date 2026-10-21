<?php

class Database
{
    private $host = "localhost";
    private $db_name = "student_event_registration";
    private $username = "root";
    private $password = "";

    public $conn;

    public function connect()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host .
                ";dbname=" . $this->db_name .
                ";charset=utf8mb4",
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }

        return $this->conn;
    }

    // Alias so both connect() and getConnection() work
    public function getConnection()
    {
        return $this->connect();
    }
}
