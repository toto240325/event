<?php 

function create_fct($input, $direct_call) {

  // detect is we are in development mode (module are in ~) or production mode (modules are in /var/www/)
  $dir = dirname(__FILE__);
  $dir_arr = explode("/",$dir);
  $dev_mode = ($dir_arr[1] == "home");
  //echo "dev_mode : $dev_mode";

  if ($dev_mode) {
    $root_folder = "/home/toto/event";
  } else {
    $root_folder = "/var/www/event";
  }

  include_once "$root_folder/config/Database.php";
  include_once "$root_folder/models/Event.php";
  include_once "$root_folder/utilities.php";
  include "$root_folder/params.php";

  if (!$direct_call) {
    // Headers  
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: event');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, hostization, X-Requested-With');
  }

  // Instantiate DB & connect
  $database = new Database($params);
  // $version = $database->querySingle('SELECT SQLITE_VERSION()');
  $db = $database->connect();

  // Instantiate blog event object
  $event = new Event($db,$database->db_type);
  
  //echo "input : " . $input . "\n";
  $data = json_decode($input);
  
  $event->text = $data->text;
  $event->categ = $data->categ;
  $event->host = $data->host;
  
  // Create event and get the id of the row created
  $dt = new DateTime("now", new DateTimeZone('Europe/Paris'));
  $dt_string = $dt->format('Y/m/d H:i:s');
  
  $lastid = $event->create();
  if($lastid > 0 ) {

    //row creation was successful; get id of the row just created
    //given that for the events table id is the "integer primary key", rowid = id
    // more : https://www.sqlite.org/rowidtable.html

    $message = "event $lastid created on $dt_string ($event->text)";
    $error = "";
  } else {
    $message = 'event not created on '. $dt_string;
    $error = 'event not created on '. $dt_string;
  }

  $result = array(
    'error' => $error,
    'message' => $message,
    'id' => $lastid
  );

  return $result;
}

?>
