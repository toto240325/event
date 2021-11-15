<?php 
  // to debug : 
  // F5 -> start "listen for Xdebug"
  // if "address already in use :::9003"
  // lsof -n -i -P | grep LISTEN

  // cd /home/toto/event_dev/api/event ; php read_where.php

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

  // Instantiate event object
  $event = new Event($db,$database->db_type);

  $debug = false;
  if ($debug) {
    $type = isset($_GET['type']) ? $_GET['type'] : $event->type = "temperature"; # die("sorry, no _GET\n");
    $limit = isset($_GET['limit']) ? $_GET['limit'] : $event->limit = 3; # die("sorry, no _GET\n");  
  } else {
    // Get $type
    $type = isset($_GET['type']) ? $_GET['type'] : "";

    // Get $limit
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 0;
  }


  // event query
  $result = $event->read_where($type,$limit);

  if ($database->db_type == "mysql") {
    // Get row count
    $num = $result->rowCount();
  } elseif ($database->db_type == "sqlite") {
    // Get row count this works for mysql but doesn't work well for sqlite
    $item = $result->fetchAll(PDO::FETCH_ASSOC); 
    if($item && count($item)){ 
      $num = count($item);
      //reset the rows pointer to the beginning
      $result->execute();
    } else {
      $num = 0;
    }
  } else {
    die("unknown db_type !");
  }

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
        'host' => $host,
        'type' => $type,
        'time' => convert_UTC_to_CET($time)
      );

      // Push to "data"
      array_push($events_arr, $event_item);
      // array_push($events_arr['data'], $event_item);
    }

    // Turn to JSON & output
    echo json_encode($events_arr);

  } else {
    // No event
    echo json_encode(
      array('message' => 'No event Found')
    );
  }
