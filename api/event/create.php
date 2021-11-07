<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: event');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, hostization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Event.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog event object
  $event = new event($db);

  // Get raw evented data
  $data = json_decode(file_get_contents("php://input"));

  $event->text = $data->text;
  $event->type = $data->type;
  $event->host = $data->host;
  
  // Create event
  $dt = new DateTime("now", new DateTimeZone('Europe/Paris'));
  $dt_string = $dt->format('Y/m/d H:i:s');

  if($event->create()) {
    echo json_encode(
      array('message' => 'event created on '. $dt_string)
    );
  } else {
    echo json_encode(
      array('message' => 'event not created on '. $dt_string)
    );
  }

