<?php 
// to test : 
// cd ~/event/api/event ; php delete.php

// detect if we are call from apached or from command line
$direct_call = ($argc != null);


if ($direct_call) {
  $root_folder = "/home/toto/event";
} else {
  $root_folder = "/var/www/event";
}

require_once "$root_folder/api/event/delete_fct.php";
require_once "$root_folder/api/event/create_fct.php";

// detect if we are call from apached or from command line
$direct_call = ($argc != null);



function create_test_event() {
  $input = '{
    "text" : "dummy event created to be deleted just after for testing purposes",
    "host" : "test host",
    "type" : "test dummy"
  }';
  $direct_call = true;
  $result = create_fct($input, $direct_call); 
  $id = $result["id"]; 
  return $id;

}

if ($direct_call) {
  // echo "this is a call from command line\n";
  // echo "There are $argc arguments\n";
  // for ($i=0; $i < $argc; $i++) {
  //   echo $argv[$i] . "\n";
  // }

  $id = create_test_event();

  $input = '{
    "id" : ' . $id . '
  }';  
} else {
  $input = file_get_contents("php://input");
  // echo "this is a call from apache\n";
  // echo "input: $input";
}

$result = delete_fct($input, $direct_call);

?>
