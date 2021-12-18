<?php 

function read_ps4_fct($input, $direct_call) {

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


    // Set ID to update
    $from = $data->from;

    // event query
    $result = $event->read_ps4($from);

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
        $rec_arr = array();
        // $events_arr['data'] = array();

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $rec_item = array(
                'date' => convert_UTC_to_CET($row["d"]),
                'count' => intval($c)
            );

            // Push to "data"
            //array_unshift($rec_arr, $rec_item);
            array_push($rec_arr, $rec_item);
        }

        // Turn to JSON & output
        $error = '';
        $records = $rec_arr;

    } else {
        // No event
        $error = 'error no event found';
        $records = array();
    }

    $result = array(
        'error' => $error,
        'records' => $records
    );

    return $result;
    
}
?>