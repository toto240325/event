<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: PUT');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Event.php';

  // Instantiate DB & connect
  $database = new Database($params);
  $db = $database->connect();

  // Instantiate blog event object
  $event = new Event($db,$database->db_type);

  // Get raw evented data
  $data = json_decode(file_get_contents("php://input"));

  // Set ID to update
  $event->id = $data->id;

  $event->text = $data->text;
  $event->host = $data->host;
  $event->type = $data->type;
  
  // Update event
  if($event->update()) {
    echo json_encode(
      array('message' => 'Event Updated')
    );
  } else {
    echo json_encode(
      array('message' => 'Event Not Updated')
    );
  }

