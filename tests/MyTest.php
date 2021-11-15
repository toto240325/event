<?php
// two ways to execute the test suite : 
// php vendor/bin/phpunit tests/MyTest.php --stderr --testdox # --stderr to avoid errors to interfere ??? --testdox : better output ?
// ./vendor/bin/phpunit --testdox

include '../params.php';

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
        $output = myCurl($event_server . "/api/event/mock.php");
        $result = json_decode($output, true);
        $message = $result["message"]; 
        $this->assertStringContainsString("this is the mockup function",$message);
    }
    

    public function testEventCreate() {
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
            "text" : "test from phpunit",
            "host" : "test host",
            "type" : "temperature"
        }';  
    
        $json = post($url,$fields_string);
        $result = json_decode($json,true);
        // echo "result : \n";
        // var_dump($result);

        $message = $result["message"]; 
        $this->assertStringContainsString("event created on ",$message);
    }


    public function testEventRead() {
    # curl "http://192.168.0.52/event_dev/api/event/read.php"
    global $event_server;
        $output = myCurl($event_server . "/api/event/read.php");
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
    
    # curl "http://192.168.0.52/event_dev/api/event/read_single.php?id=1"
    public function testEventReadSingle() {
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
    public function testEventReadLast() {
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

        public function testEventReadWhere() {
            # curl "http://192.168.0.52/event_dev/api/event/read_where.php?type=temperature&limit=3"
            global $event_server;
                $output = myCurl($event_server . "/api/event/read.php");
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