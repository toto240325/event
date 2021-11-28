<?php 
// to test : 
// cd ~/event/api/event ; php create.php

require_once "create_fct.php";

// detect if we are call from apached or from command line
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

create_fct($input, $direct_call);

?>
