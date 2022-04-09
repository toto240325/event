<?php 
// to test : 
//    F5 -> start "listen for Xdebug"
//    set breakpoint
//    cd ~/event/api/event ; php create.php
// if "address already in use :::9003"
// lsof -n -i -P | grep LISTEN

// detect is we are in development mode (module are in ~) or production mode (modules are in /var/www/)
$dir = dirname(__FILE__);
$dir_arr = explode("/",$dir);
$dev_mode = ($dir_arr[1] == "home");
//echo "dev_mode : $dev_mode\n";


if ($dev_mode) {
  $root_folder = "/home/toto/event";
} else {
  $root_folder = "/var/www/event";
}

require_once "$root_folder/api/event/create_fct.php";

// detect if we are call from apache or from command line
// $direct_call = ($argc != null);
$direct_call = isset($argc);


if ($direct_call) {
  // echo "this is a call from command line\n";
  // echo "There are $argc arguments\n";
  // for ($i=0; $i < $argc; $i++) {
  //   echo $argv[$i] . "\n";
  // }
  $input = '{
    "text" : "test from direct php call (MyTest)",
    "host" : "test host",
    "categ" : "test"
  }';
  
} else {
  $input = file_get_contents("php://input");
  // echo "this is a call from apache<br>\n";
  // echo "input: $input<br>\n";
}

// die("test99 <br> 
//     dev_mode : $dev_mode <br>
//     direct_call : $direct_call <br>
//     strlen(input) = " . strlen($input) . "<br>
//     input = $input <br>
//     result : $result <br> end");

$result = create_fct($input, $direct_call);     

echo json_encode($result);

?>
