<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/Event.php';
  include '../../utilities.php';
  include '../../params.php';

  // Instantiate DB & connect
  $database = new Database($params);

  $db = $database->connect();

  // Instantiate blog event$event object
  $event = new Event($db,$database->db_type);

  // Get ID
  //$event->id = isset($_GET['id']) ? $_GET['id'] : $event->id = 1; # die("sorry, no _GET\n");
  $event->id = isset($_GET['id']) ? $_GET['id'] : die("sorry, no _GET\n");

  // Get event$event
//  $event->read_dummy();
  $event->read_single();

  // Create array
  $event_arr = array(
    'id' => $event->id,
    'text' => $event->text,
    'host' => $event->host,
    'type' => $event->type,
    'time' => convert_UTC_to_CET($event->time),
    'error' => $event->error,
  );

  // Make JSON
  print_r(json_encode($event_arr));