<?php 

function delete_fct($input, $direct_call) {

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
    $db = $database->connect();

    // Instantiate event object
    $event = new Event($db,$database->db_type);
    
    // Get parameters
    $data = json_decode($input);

    // Set ID to update
    $event->id = $data->id;


    // Delete event
    if($event->delete()) {
        $msg = "event $event->id deleted";
        $error = '';
    } else {
        $msg = "event $event->id not deleted";
        $error = 'error event not deleted';
    }

    $result = array(
        'message' => $msg,
        'error' => $error
    );

    return $result;

}

?>
