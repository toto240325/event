<?php
// two ways to execute the test suite : 
// cd ~/event ; php vendor/bin/phpunit tests/MyTest.php --stderr --testdox # --stderr to avoid errors to interfere ??? --testdox : better output ?
// ./vendor/bin/phpunit --testdox

require "api/event/create_fct.php";

echo "current directory : " . getcwd() . "\n";
include 'params.php';
//include '../utils/log_event.php';

function mylog($text) {
    echo $text . "\n";
}


function log_event($text,$type) {
    # curl "http://192.168.0.52/event_dev/api/event/read_where.php?type=temperature&limit=3"
    global $event_server;
    $output = myCurl($event_server . "/api/event/readWhere.php?type=temperature&limit=4");
    $result = json_decode($output, true);
    return $result;
}

function myCurl($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

function post2($url,$fields_string) {
    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

    //So that curl_exec returns the contents of the cURL; rather than echoing it
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

    //execute post
    return $result = curl_exec($ch);
}

function post($url,$fields_string) {
    $sURL = $url;
    $sPD = $fields_string;
    $aHTTP = array(
    'http' => // The wrapper to be used
        array(
        'method'  => 'POST', // Request Method
        // Request Headers Below
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $sPD
    )
    );
    $context = stream_context_create($aHTTP);
    $contents = file_get_contents($sURL, false, $context);
    return $contents;
}

class MyTest extends \PHPUnit\Framework\TestCase {

    public function testEventMock() {
        global $event_server;
        mylog("event server : " . $event_server);
        $output = myCurl($event_server . "/api/event/mock.php");
        $result = json_decode($output, true);
        $message = $result["message"]; 
        $this->assertStringContainsString("this is the mockup function",$message);
    }
    

    // public function testEventCreate() {
    //     //test the create.php module (not the REST API !)   
    //     //The data you want to send via POST (as a json string)

    //     $input = '{
    //         "text" : "test from direct php call (MyTest)",
    //         "host" : "test host",
    //         "type" : "test"
    //     }';
    //     $direct_call = true;
    //     $result = create_fct($input, $direct_call);     
    //     // echo "result : \n";
    //     // var_dump($result);

    //     $message = $result["message"]; 
    //     $this->assertStringContainsString("event created on ",$message);
    // }


    public function testEventAPICreate() {
        // curl -X POST -d "{\"text\" : \"test from phpunit\",\"host\" : \"test host\",\"type\" : \"sqlite test\"}"  event_server + "/api/event/create.php"    
        global $event_server;
        $url = $event_server . "/api/event/create.php";

        // //The data you want to send via POST (seperated http query fields like : text=text1&host=hostABC&type=this+is+mytype)
        // $fields = [
        //     'text' => 'text sent via POST',
        //     'host' => 'my host',
        //     'type' => 'posted'
        // ];
    
        // //url-ify the data for the POST
        // $fields_string = http_build_query($fields);
    
        //The data you want to send via POST (as a json string)
        $fields_string = '{
            "text" : "test from phpunit via apache",
            "host" : "test host",
            "type" : "test"
        }';  
    
        $json = post($url,$fields_string);
        $result = json_decode($json,true);
        // echo "result : \n";
        // var_dump($result);

        $message = $result["message"]; 
        $this->assertStringContainsString("event created on ",$message);
    }


    // public function testEventAPIRead() {
    // # curl "http://192.168.0.52/event_dev/api/event/read.php"
    //     global $event_server;
    //     $output = myCurl($event_server . "/api/event/read.php");
    //     $result = json_decode($output, true);
    //     $num = count($result);
    //     //echo "number of records found: " . $num;
    //     $this->assertGreaterThan(1,$num);
    //     if ($num > 0) {
    //         $rec = $result[0];
    //         // echo "rec : \n";
    //         // var_dump($rec);
    //         // echo "\n";
    //         $t = $rec["text"];
    //         $this->assertStringContainsString("test from phpunit",$t);
            
    //     }
    // }
    
    # curl "http://192.168.0.52/event_dev/api/event/read_single.php?id=1"
    public function testEventAPIReadSingle() {
        global $event_server;
        $output = myCurl($event_server . "/api/event/read_single.php?id=1");
        $result = json_decode($output, true);
        $error = $result["error"];
        $this->assertEquals("",$error);
        if ($result["error"] =="") {
            $t = $result["id"];
            $this->assertStringContainsString("1",$t);
        }
    }

    # curl "http://192.168.0.52/event_dev/api/event/read_last.php?type=temperature"
    public function testEventAPIReadLast() {
        global $event_server;
        $output = myCurl($event_server . "/api/event/read_last.php?type=temperature");
        $result = json_decode($output, true);
        $error = $result["error"];
        $this->assertEquals("",$error);
        if ($result["error"] =="") {
            $t = $result["type"];
            $this->assertStringContainsString("temperature",$t);
        }
    }

    public function testEventAPIReadWhere() {
        # curl "http://192.168.0.52/event_dev/api/event/read_where.php?type=temperature&limit=3"
        global $event_server;
        $output = myCurl($event_server . "/api/event/read_where.php?type=temperature&limit=4");
        $result = json_decode($output, true);
        $num = count($result);
        //echo "number of records found: " . $num;
        $this->assertEquals(4,$num);                
    }       

    public function testLogEvent() {
        # curl "http://192.168.0.52/event_dev/api/event/read_where.php?type=temperature&limit=3"
        global $event_server;
        $result = log_event("test","mynewtype");
        $output = myCurl($event_server . "/api/event/read_where.php?limit=1");
        $result = json_decode($output, true);
        $num = count($result);
        //echo "number of records found: " . $num;
        $this->assertGreaterThanOrEqual(1,$num);
        if ($num > 0) {
            $rec = $result[0];
            // echo "rec : \n";
            // var_dump($rec);
            // echo "\n";
            $t = $rec["text"];
            $this->assertStringContainsString("test from phpunit",$t);
            
        }
    }
}