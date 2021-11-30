<?php 
// to test : 
// F5 -> start "listen for Xdebug"
// if "address already in use :::9003"
// lsof -n -i -P | grep LISTEN
// cd ~/event/api/event ; php create.php

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

require_once "$root_folder/api/event/create_fct.php";

// detect if we are call from apache or from command line
$direct_call = ($argc != null);

if ($direct_call) {
  // echo "this is a call from command line\n";
  // echo "There are $argc arguments\n";
  // for ($i=0; $i < $argc; $i++) {
  //   echo $argv[$i] . "\n";
  // }
  $input = '{
    "text" : "test from direct php call (MyTest)",
    "host" : "test host",
    "type" : "test"
  }';
  
} else {
  $input = file_get_contents("php://input");
  // echo "this is a call from apache\n";
  // echo "input: $input";
}

$result = create_fct($input, $direct_call);     
echo json_encode($result);

?>
