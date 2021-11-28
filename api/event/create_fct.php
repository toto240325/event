<?php 

function create_fct($input, $direct_call) {

  // Headers
  if (!$direct_call) {

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: event');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, hostization, X-Requested-With');
  }

  include_once '../../config/Database.php';
  include_once '../../models/Event.php';
  include '../../utilities.php';
  include '../../params.php';

  // Instantiate DB & connect
  $database = new Database($params);
  // $version = $database->querySingle('SELECT SQLITE_VERSION()');
  $db = $database->connect();

  // Instantiate blog event object
  $event = new Event($db,$database->db_type);
  

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
  
  // Create event and get the id of the row created
  $dt = new DateTime("now", new DateTimeZone('Europe/Paris'));
  $dt_string = $dt->format('Y/m/d H:i:s');
  
  $lastid = $event->create();
  if($lastid > 0 ) {

    //row creation was successful; get id of the row just created
    //given that for the events table id is the "integer primary key", rowid = id
    // more : https://www.sqlite.org/rowidtable.html

    $msg = 'event created on '. $dt_string . '(' . $event->text . ')';
    echo json_encode(
      array(
        'message' => $msg,
        'id' => $lastid,
        'error' => ''
      )
    );
  } else {
    $msg = 'event not created on '. $dt_string;
    echo json_encode(
      array(
        'message' => $msg,
        'error' => 'error !!!!'
      )
    );
  }    
}

?>
