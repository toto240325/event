<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/Event.php';

  // Instantiate DB & connect
  $database = new Database_mysql();
  $db = $database->connect();
  $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);

    echo json_encode(
    array('message' => 
    'this is the mockup function of the event REST API; ' . 
    'db_name : ' . $database->db_name . '; ' .
    'host : ' . $database->host . '; ' .
    'driver : ' . $driver
    )
  );
  echo "\n";

  ?>
