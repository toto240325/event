<?php 

function delete_fct($input, $direct_call) {


    // Headers
    if ($direct_call) {
        $root_folder = "/home/toto/event";
    } else {
    $root_folder = "/var/www/event";
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: event');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, hostization, X-Requested-With');
    }

    include_once "$root_folder/config/Database.php";
    include_once "$root_folder/models/Event.php";
    include "$root_folder/params.php";


    // Instantiate DB & connect
    $database = new Database($params);
    $db = $database->connect();

    // Instantiate blog event object
    $event = new Event($db,$database->db_type);
    
    // Get raw evented data
    $data = json_decode($input);

    // Set ID to update
    $event->id = $data->id;

    // Delete event
    if($event->delete()) {
        echo json_encode(
        array('message' => 'event deleted')
        );
    } else {
        echo json_encode(
        array('message' => 'event not deleted')
        );
    }
}

?>
