<?php 
  class Event {
    // DB stuff
    private $conn;
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

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
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

          $row = $stmt->fetch(PDO::FETCH_ASSOC);

          // Set properties
          $this->text = $row['text'];
          $this->host = $row['host'];
          $this->type = $row['type'];
          $this->time = $row['time'];
    }

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
            return true;
      }

      // Print error if something goes wrong
      printf("Error: %s.\n", $stmt->error);

      return false;
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