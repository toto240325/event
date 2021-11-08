<?php 

$db_type = "";

#mysql database
class Database_mysql {
  // DB Params
  private $host = 'localhost';
  private $db_name = 'mydatabase';
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
class Database  {
  // DB Params
  private $conn;
  
  // DB Connect
  public function connect() {
    global $db_type;
    $db_type = "sqlite3";
    $this->conn = null;

    try { 
      $this->conn = new PDO('sqlite:mydatabase_dev.db');
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
      echo 'Connection Error: ' . $e->getMessage();
    }

    return $this->conn;
  }
}

function testdb() {
  global $db_type;
  include_once '/home/toto/event_dev/models/Event.php';

  // Instantiate DB & connect
  $db = new Database();
  if ($db_type == "mysql") $db = $database->connect();
  $tablesquery = $db->query("SELECT name FROM sqlite_master WHERE type='table';");

  while ($table = $tablesquery->fetchArray(SQLITE3_ASSOC)) {
      echo $table['name'] . '<br />';
  }
}
testdb();

echo "\nend\n";

  ?>
