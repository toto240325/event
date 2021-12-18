<?php 
// to debug : 
// F5 -> start "listen for Xdebug"
// if "address already in use :::9003"
// lsof -n -i -P | grep LISTEN
// to test locally : 
// cd ~/event/api/event ; php read_where.php
//
// to test the API : 
// http://192.168.0.52/event/api/event/read_where.php?categ=temperature&nb=60&date_from=2021-12-01
// http://192.168.0.73/event/api/event/read_where.php?categ=temperature&nb=60&date_from=2021-12-01

// detect is we are in development mode (module are in ~) or production mode (modules are in /var/www/)
$dir = dirname(__FILE__);
$dir_arr = explode("/",$dir);
$dev_mode = ($dir_arr[1] == "home");
// echo "dev_mode : $dev_mode";
// return;

if ($dev_mode) {
  $root_folder = "/home/toto/event";
} else {
  $root_folder = "/var/www/event";
}

require_once "$root_folder/api/event/read_where_fct.php";

// detect if we are called from apache or from command line
$direct_call = ($argc != null);
if ($direct_call) {
  // echo "this is a call from command line\n";
  // echo "There are $argc arguments\n";
  // for ($i=0; $i < $argc; $i++) {
  //   echo $argv[$i] . "\n";
  // }
  $input = '{
    "categ" : "test",
    "nb"  : 2,
    "date_from" : "2021-12-02"
  }';
} else {
    $categ = isset($_GET['categ']) ? $_GET['categ'] : "";
    $nb = isset($_GET['nb']) ? $_GET['nb'] : 0;
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : "1900-01-01";
    
    $input = '{
      "categ" : "' . $categ . '",
      "nb"  : ' . $nb . ',
      "date_from" : "' . $date_from . '"
    }';
  
}

$result = read_where_fct($input, $direct_call);
echo json_encode($result);

?>
