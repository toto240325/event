<?php 

echo "test";



  class Database {
    // DB Params
    private $host = 'localhost';
    private $db_name = 'mydatabase';
    private $username = 'toto';
    private $password = 'Toto!';
    private $conn;

    // DB Connect
    public function connect() {
      $this->conn = null;

      try { 
        $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch(PDOException $e) {
        echo 'Connection Error: ' . $e->getMessage();
      }

      return $this->conn;
    }
  }

  $database = new Database();
  $db = $database->connect();

  echo "end";
