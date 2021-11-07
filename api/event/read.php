<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/Event.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog event object
  $event = new Event($db);

  // Blog event query
  $result = $event->read();
  // Get row count
  $num = $result->rowCount();

  // Check if any events
  if($num > 0) {
    // Post array
    $events_arr = array();
    // $events_arr['data'] = array();

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $event_item = array(
        'id' => $id,
        'text' => $text,
        'host' => $body,
        'type' => $type,
        'time' => $time,
      );

      // Push to "data"
      array_push($events_arr, $event_item);
      // array_push($events_arr['data'], $event_item);
    }

    // Turn to JSON & output
    echo json_encode($events_arr);

  } else {
    // No Posts
    echo json_encode(
      array('message' => 'No Posts Found')
    );
  }
