<?php 

function check_type($var,$type) {
    $this_type = gettype($var);
    if ($this_type != $type) {
        die("not correct type for $var; expected : $type; found : $this_type\n");
    }
}

function check_date_between($date_str, $date_from_str, $date_to_str) {
    try {
        $date_dt = strtotime($date_str);
        $date_from_dt = strtotime($date_from_str);
        $date_to_dt = strtotime($date_to_str);

        // $date_dt = new DateTime($date_str, new DateTimeZone('Europe/Paris'));
        // $date_from_dt = new DateTime($date_from_str, new DateTimeZone('Europe/Paris'));
        // $date_to_dt = new DateTime($date_to_str, new DateTimeZone('Europe/Paris'));
      } catch(Exception $e) {
        // this should never occur as the last rowid is supposed to be a int anyway
        $msg = $e->getMessage();
        die("exception when converting dates in check_date_between; error : $msg" . PHP_EOL);
        //throw new Exception("exception  when converting dates in check_date_between; error : $msg");
      }
      $result_OK = (($date_dt >= $date_from_dt) and ($date_dt <= $date_to_dt));
    //   $result2_OK = ($date_dt <= $date_to_dt);
    //   $result_OK = ($result1_OK and $result2_OK);
      if (!$result_OK) {
        die("$date_str should be between $date_from_str and $date_to_str" . PHP_EOL);
    }
}


function read_where_fct($input, $direct_call) {

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
    }
  
    // Instantiate DB & connect
    $database = new Database($params);
    $db = $database->connect();

    // Instantiate event object
    $event = new Event($db,$database->db_type);

    // Get parameters
    $data = json_decode($input);
    // echo "data : ";
    // var_dump($data);

    $categ = $data->categ;
    $nb_str = $data->nb;
    $date_from = $data->date_from;

    //check parameters have the right
    check_type($categ,"string");
    check_type($nb_str,"string");
    check_type($date_from, "string");

    check_date_between($date_from,"1900-01-01","2022-12-31");

    // event query
    $result = $event->read_where($categ, $nb_str, $date_from);

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
                'categ' => $categ,
                'time' => convert_UTC_to_CET($time)
            );

            // Push to "data"
            //array_push($events_arr, $event_item);
            array_unshift($events_arr, $event_item);
            // array_push($events_arr['data'], $event_item);
        }

        // Turn to JSON & output
        //$events = json_encode($events_arr);
        $error = '';
        $events = $events_arr;

    } else {
        // No event
        $error = 'error no event found';
        $events = array();
    }

    $result = array(
        'error' => $error,
        'events' => $events
    );

    return $result;
    
}
?>