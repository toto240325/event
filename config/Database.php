<?php 

$db_type = "";

#mysql database
class Database_mysql {
  // DB Params
  public $host = 'localhost';
  public $db_name = 'mydatabase';
  private $username = 'toto';
  private $password = 'Toto!';
  private $conn;

  // DB Connect
  public function connect() {
    global $db_type;
    $db_type = "mysql";
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

# sqlite database
class Database_sqlite {
  // DB Params
  private $conn;
  
  // DB Connect
  public function connect() {
    $this->conn = null;
    try { 
      $this->conn = new PDO('sqlite:/home/toto/event_dev/mydatabase_dev.db');
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
      echo 'Connection Error: ' . $e->getMessage();
    }

    return $this->conn;
  }
}

function show_tables() {
  include_once '/home/toto/event_dev/models/Event.php';

  // Instantiate DB & connect
  $database = new Database_sqlite();
  $db = $database->connect();

  $result = $db->query("SELECT name FROM sqlite_master WHERE type='table';");

  echo "Tables in this db:\n";
  // Loop thru all data from messages table 
  // and insert it to file db
  foreach ($result as $r) {
    echo $r['name']."\n";  
  }
}

//show_tables();

  ?>
