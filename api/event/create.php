<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: event');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, hostization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Event.php';
  include '../../utilities.php';
  include '../../params.php';

  // Instantiate DB & connect
  $database = new Database($params);
  $db = $database->connect();

  // Instantiate blog event object
  $event = new Event($db,$database->db_type);
  
  // Get raw evented data
  $input = file_get_contents("php://input");
  //echo "input : " . $input . "\n";
  $data = json_decode($input);
  

  // $data = json_decode('{
  //   "text" : "backup of HP455G7",
  //   "host" : "mypc3",
  //   "type" : "sqlite test"
  // }');

  $event->text = $data->text;
  $event->type = $data->type;
  $event->host = $data->host;
  
  // Create event
  // $dt = new DateTime("now", new DateTimeZone('Europe/Paris'));
  // $dt_string = $dt->format('Y/m/d H:i:s');

  if($event->create()) {
    echo json_encode(
      array('message' => 'event created on '. $dt_string . '(' . $event->text . ')')
    );
  } else {
    echo json_encode(
      array('message' => 'event not created on '. $dt_string)
    );
  }

