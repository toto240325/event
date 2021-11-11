<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/Event.php';
  include '../../params.php';

  //date_default_timezone_set('Europe/Paris');

  // Instantiate DB & connect
  $database = new Database($params);
  $db = $database->connect();

  function show_tables2() { 
    global $db;

    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver == 'mysql') {
      echo "Running on mysql; doing something mysql specific here\n";
    } else {
      echo "Running on this driver : " . $driver . "\n";
    }
    // echo "Server Info : " . $db->getAttribute(PDO::ATTR_SERVER_INFO);
    try {

      $db->exec("CREATE TABLE IF NOT EXISTS messages2 (
        id INTEGER PRIMARY KEY, 
        title TEXT, 
        message TEXT, 
        time INTEGER)");
    } catch(PDOException $e) {
      echo 'Connection Error: ' . $e->getMessage();
    }
    $result = $db->query("SELECT name FROM sqlite_master WHERE type='table';");
    echo "Tables in this db:\n";
    // Loop thru all data from messages table 
    // and insert it to file db
    foreach ($result as $r) {
      echo $r['name']."\n";  
    }
  }
  // show_tables2();

  // Instantiate event object
  $event = new Event($db);
  // event query
  $result = $event->read();

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
