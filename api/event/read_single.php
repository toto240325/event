<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/Event.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog event$event object
  $event = new Event($db);

  // Get ID
  $event->id = isset($_GET['id']) ? $_GET['id'] : die();

  // Get event$event
  $event->read_single();

  // Create array
  $event_arr = array(
    'id' => $event->id,
    'text' => $event->text,
    'host' => $event->host,
    'type' => $event->type,
  );

  // Make JSON
  print_r(json_encode($event_arr));