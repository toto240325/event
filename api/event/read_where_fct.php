<?php 

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
    $nb = $data->nb;
    $date_from = $data->date_from;

    // event query
    $result = $event->read_where($categ, $nb, $date_from);

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