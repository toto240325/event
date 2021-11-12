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
  $event = new Event($db);

  // Get type
  $event->type = isset($_GET['type']) ? $_GET['type'] : die();

  // Get event$event
  $event->read_last();

  // Create array
  $event_arr = array(
    'id' => $event->id,
    'text' => $event->text,
    'host' => $event->host,
    'type' => $event->type,
    'time' => convert_UTC_to_CET($time),
  );

  // Make JSON
  print_r(json_encode($event_arr));