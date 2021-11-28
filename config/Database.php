<?php 

#mysql database
class Database {
  // DB Params
  public $db_name = 'mydatabase';
  public $db_type;
  public $mysql_host = 'localhost';
  private $conn;

  // Constructor with params
  public function __construct($params) {
    $this->mysql_username = $params["mysql_username"];
    $this->mysql_password = $params["mysql_password"];  
    $this->db_type = $params["db_type"];  
    $this->sqlite_db = $params["sqlite_db"];  
  }
  
  // DB Connect
  public function connect() {
    $this->conn = null;

    if ($this ->db_type == "mysql") {
      try { 
        $this->conn = new PDO('mysql:host=' . $this->mysql_host . ';dbname=' . $this->db_name, $this->mysql_username, $this->mysql_password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch(PDOException $e) {
        echo 'Mysql connection Error: ' . $e->getMessage();
      }
    } elseif ($this->db_type == "sqlite") {
      try { 
        $this->conn = new PDO('sqlite:'. $this->sqlite_db);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch(PDOException $e) {
        echo 'Sqlite connection Error: ' . $e->getMessage();
      }
    } else {
      die("unknown db_type : " . $this->db_type);
    }
    return $this->conn;
  }
}

// # sqlite database
// class Database_sqlite {
//   // DB Params
//   private $conn;
  
//   // DB Connect
//   public function connect() {
//     $this->conn = null;
//     try { 
//       $this->conn = new PDO('sqlite:/home/toto/event_dev/mydatabase_dev.db');
//       $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     } catch(PDOException $e) {
//       echo 'Connection Error: ' . $e->getMessage();
//     }

//     return $this->conn;
//   }
// }

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
