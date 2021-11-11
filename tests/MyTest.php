<?php
// two ways to execute the test suite : 
// php vendor/bin/phpunit tests/MyTest.php --stderr --testdox # --stderr to avoid errors to interfere ??? --testdox : better output ?
// ./vendor/bin/phpunit --testdox

//include 'Event.php';

// private function _execute(array $params = array()) {
//     $_GET = $params;
//     ob_start();
//     return ob_get_clean();
// }


// function parseEventOutput($output) {

//     // $output should be something like this :  
//     //{"records":"{\"id\":\"63\",\"time\":\"2017-11-22 22:07:56\",\"host\":\"mockup host\",\"text\":\"this is the mockup event\",\"type\":\"mockup\"}","errMsg":""}

//     $json = json_decode($output, true);

//     // $json should be something like this :
//     // array(2) {
//     //   ["records"]=>
//     //   string(111) "{"id":"63","time":"2017-11-22 22:07:56","host":"mockup host","text":"this is the mockup event","type":"mockup"}"
//     //   ["errMsg"]=>
//     //   string(0) ""
//     // }

//     // var_dump($json);
//     $records = $json["records"];
//     $errMsg = $json["errMsg"];
//     if ($records != []) {
//         var_dump($records);
//         $json2 = json_decode($records,true);
//         $id = $json2["id"];
//         $time = $json2["time"];
//         $host = $json2["host"];
//         $text = $json2["text"];
//         $type = $json2["type"];
        
//         // echo "host : ".$host."\n";
//         // echo "id : ".$id."\n";
//         // echo "time : ".$time."\n";
//         // echo "text : ".$text."\n";
//         // echo "type : ".$type."\n";    
//     } else {
//         $id = null;
//         $time = null;
//         $host = null;
//         $text = null;
//         $type = null;
//     }
//     return [
//         "id" => $id,
//         "time" => $time,
//         "host" => $host,
//         "text" => $text,
//         "type" => $type,
//         "errMsg" => $errMsg
//     ];   
// }

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

    public function testThatStringMatch() {
        $a = "toto";
        $b = "to"."to";
        $this->assertSame($a,$b);
    }

    public function testThatObjectsAreEqual() {
        $a = 1;
        $b = $a;
        $c = 2;
        $this->assertEquals($a,$b);
        $this->assertNotEquals($a,$c);
    }

    public function testEventMock() {
        $output = myCurl("http://192.168.0.52/event_dev/api/event/mock.php");
        $result = json_decode($output, true);
        $message = $result["message"]; 
        $this->assertStringContainsString("this is the mockup function",$message);
    }
    
        // curl -X POST -d "{\"text\" : \"test from phpunit\",\"host\" : \"test host\",\"type\" : \"sqlite test\"}"  "http://192.168.0.52/event_dev/api/event/create.php"    
        public function testEventCreate() {
        $url = "http://192.168.0.52/event_dev/api/event/create.php";

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
            "text" : "test from phpunit",
            "host" : "test host",
            "type" : "sqlite test"
        }';  
    
        $json = post($url,$fields_string);
        $result = json_decode($json,true);
        // echo "result : \n";
        // var_dump($result);

        $message = $result["message"]; 
        $this->assertStringContainsString("event created on ",$message);
    }

    # curl "http://192.168.0.52/event_dev/api/event/read_single.php?id=5"
    public function testEventRead() {
        $output = myCurl("http://192.168.0.52/event_dev/api/event/read.php");
        $result = json_decode($output, true);
        $num = count($result);
        //echo "number of records found: " . $num;
        $this->assertGreaterThan(1,$num);
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