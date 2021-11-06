<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: DELETE');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Event.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog event object
  $event = new Event($db);

  // Get raw evented data
  $data = json_decode(file_get_contents("php://input"));

  // Set ID to update
  $event->id = $data->id;

  // Delete event
  if($event->delete()) {
    echo json_encode(
      array('message' => 'event Deleted')
    );
  } else {
    echo json_encode(
      array('message' => 'event Not Deleted')
    );
  }

