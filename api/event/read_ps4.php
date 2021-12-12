<?php 
// to debug : 
// F5 -> start "listen for Xdebug"
// if "address already in use :::9003"
// lsof -n -i -P | grep LISTEN
// cd ~/event/api/event ; php read_ps4.php 2021-12-01

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

require_once "$root_folder/api/event/read_ps4_fct.php";

// detect if we are call from apache or from command line
$direct_call = ($argc != null);
if ($direct_call) {
  // echo "this is a call from command line\n";
  // echo "There are $argc arguments\n";
  // for ($i=0; $i < $argc; $i++) {
  //   echo $argv[$i] . "\n";
  // }
  $input = '{
    "from"  : "2021-12-01"
  }';
} else {
    $from = isset($_GET['from']) ? $_GET['from'] : "";
    
    $input = '{
      "from"  : ' . $from . '
    }';
  
}

$result = read_ps4_fct($input, $direct_call);
echo json_encode($result);

?>
