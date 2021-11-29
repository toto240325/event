<?php 
  class Event {
    // DB stuff
    private $conn;
    private $db_type;
    private $table = 'events';

    // CREATE TABLE `events` (
    //   `id` int(11) NOT NULL AUTO_INCREMENT,
    //   `text` varchar(255) NOT NULL,
    //   `host` varchar(255) NOT NULL,
    //   `type` varchar(255) NOT NULL,
    //   `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    //   PRIMARY KEY (`id`)
    // );

    // event Properties
    public $id;
    public $host;
    public $type;
    public $text;
    public $time;
    public $error = "";

    // Constructor with DB
    public function __construct($db,$db_type) {
      $this->conn = $db;
      $this->db_type = $db_type;
    }

    // Get events
    public function read() {
      // Create query
      $query = 'SELECT e.id, e.text, e.host, e.type, e.time
        FROM ' . $this->table . ' e
        ORDER BY
          e.time DESC';
      
      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Execute query
      $stmt->execute();

      return $stmt;
    }

    public function read_where($type,$limit) {
      // read the last $limit events (sorted chronologically) of type $type (if provided)

      // Create query
      $query = 'SELECT e.id, e.text, e.host, e.type, e.time ' .
        'FROM ' . $this->table . ' e ' .
        ($type == '' ? '' : 'WHERE e.type = "' . $type . '"') .
        'ORDER BY ' .
        '  e.time DESC ' .
        ($limit == 0 ? '' : 'LIMIT "' . $limit . '"')
        ;
      
      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Execute query
      $stmt->execute();

      return $stmt;
    }

//     // Get Single event
//     public function read_dummy() {
//       // Create query
//       $query = 'SELECT d.id
//           FROM dummy d
//           WHERE
//             d.id = ?
//           LIMIT 0,1';

//       // Prepare statement
//       $stmt = $this->conn->prepare($query);

//       // Bind ID
//       $stmt->bindParam(1, $this->id);

//       // Execute query
//       $stmt->execute();

//       $rc = $stmt->rowCount();
//       $a = $stmt;

//       $row = $stmt->fetch(PDO::FETCH_ASSOC);

//       // Set properties
//       $this->id = $row['id'];
// }


// Get Single event
    public function read_single() {
      // Create query
      $query = 'SELECT e.id, e.text, e.host, e.type, e.time
          FROM ' . $this->table . ' e
          WHERE
            e.id = ?
          LIMIT 0,1';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
      $stmt->bindParam(1, $this->id);

      // Execute query
      $stmt->execute();

      # check if there is at least one record
      if ($this->db_type == "mysql") {
        // Get row count
        $num = $stmt->rowCount();
      } elseif ($this->db_type == "sqlite") {
        // Get row count this works for mysql but doesn't work well for sqlite
        $item = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        if($item && count($item)){ 
          $num = count($item);
          //reset the rows pointer to the beginning
          $stmt->execute();
        } else {
          $num = 0;
        }
      } else {
        die("unknown db_type !");
      }
    
      if ($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set properties
        $this->id = $row['id'];
        $this->text = $row['text'];
        $this->host = $row['host'];
        $this->type = $row['type'];
        $this->time = $row['time'];
      }
      else {
        $this->error = "no record found";
      }
      
}

// Read last event of a given type
    public function read_last() {
      // Create query
      $query = 'SELECT e.id, e.text, e.host, e.type, e.time
          FROM ' . $this->table . ' e
          WHERE
            e.type = ?
          ORDER BY e.time desc
          LIMIT 0,1';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
      $stmt->bindParam(1, $this->type);

      // Execute query
      $stmt->execute();

      # check if there is at least one record
      if ($this->db_type == "mysql") {
        // Get row count
        $num = $stmt->rowCount();
      } elseif ($this->db_type == "sqlite") {
        // Get row count this works for mysql but doesn't work well for sqlite
        $item = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        if($item && count($item)){ 
          $num = count($item);
          //reset the rows pointer to the beginning
          $stmt->execute();
        } else {
          $num = 0;
        }
      } else {
        die("unknown db_type !");
      }
    
      if ($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set properties
        $this->id = $row['id'];
        $this->text = $row['text'];
        $this->host = $row['host'];
        $this->type = $row['type'];
        $this->time = $row['time'];
      }
      else {
        $this->error = "no record found";
      }
    }


    // // Get rowid of the last row created within this DB connection
    // private function last_rowid() {
    //   $query = 'select last_insert_rowid()';

    //   // Prepare statement
    //   $stmt = $this->conn->prepare($query);

    //   // Execute query
    //   if($stmt->execute()) {
    //     if ($this->db_type == "mysql") {
    //       // Get row count
    //       $num = $stmt->rowCount();
    //     } elseif ($this->db_type == "sqlite") {
    //       // Get row count this works for mysql but doesn't work well for sqlite
    //       $item = $stmt->fetchAll(PDO::FETCH_ASSOC); 
    //       if($item && count($item)){ 
    //         $num = count($item);
    //         //reset the rows pointer to the beginning
    //         $stmt->execute();
    //       } else {
    //         $num = 0;
    //       }
    //     } else {
    //       die("unknown db_type !");
    //     }
    //   }

        
  //       return true;
  // }



    // Create event
    public function create() {
    
      // Create query
      //$query = 'INSERT INTO ' . $this->table . ' SET text = :text, host = :host, type = :type';
      $query = 'INSERT INTO ' . $this->table . ' (text, host, type) values (:text, :host, :type)';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->text = htmlspecialchars(strip_tags($this->text));
      $this->host = htmlspecialchars(strip_tags($this->host));
      $this->type = htmlspecialchars(strip_tags($this->type));

      // Bind data
      $stmt->bindParam(':text', $this->text);
      $stmt->bindParam(':host', $this->host);
      $stmt->bindParam(':type', $this->type);

      // Execute query
      if($stmt->execute()) {   
        try {

          $lastid = intval($this->conn->lastInsertId());
          return $lastid;

        } catch(Exception $e) {
          // this should never occur as the last rowid is supposed to be a int anyway
          throw new Exception("Value must be 1 or below");
        }
      }

      // Print error if something goes wrong
      printf("Error: %s.\n", $stmt->error);

      return -1;
    }

    // Update event
    public function update() {
          // Create query
          $query = 'UPDATE ' . $this->table . '
            SET text = :text, host = :host, type = :type
            WHERE id = :id';

          // Prepare statement
          $stmt = $this->conn->prepare($query);

          // Clean data
          $this->text = htmlspecialchars(strip_tags($this->text));
          $this->host = htmlspecialchars(strip_tags($this->host));
          $this->type = htmlspecialchars(strip_tags($this->type));
          $this->id = htmlspecialchars(strip_tags($this->id));

          // Bind data
          $stmt->bindParam(':text', $this->text);
          $stmt->bindParam(':host', $this->host);
          $stmt->bindParam(':type', $this->type);
          $stmt->bindParam(':id', $this->id);

          // Execute query
          if($stmt->execute()) {
            return true;
          }

          // Print error if something goes wrong
          printf("Error: %s.\n", $stmt->error);

          return false;
    }

    // Delete event
    public function delete() {
          // Create query
          $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

          // Prepare statement
          $stmt = $this->conn->prepare($query);

          // Clean data
          $this->id = htmlspecialchars(strip_tags($this->id));

          // Bind data
          $stmt->bindParam(':id', $this->id);

          // Execute query
          if($stmt->execute()) {
            return true;
          }

          // Print error if something goes wrong
          printf("Error: %s.\n", $stmt->error);

          return false;
    } 
  }
?>