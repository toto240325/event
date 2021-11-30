<?php
// two ways to execute the test suite : 
// cd ~/event ; php vendor/bin/phpunit tests/MyTest.php --stderr --testdox # --stderr to avoid errors to interfere ??? --testdox : better output ?
// ./vendor/bin/phpunit --testdox

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
include "$root_folder/params.php";

echo "current directory : " . getcwd() . "\n";
//include '../utils/log_event.php';


function mylog($text) {
    echo $text . "\n";
}


function log_event($text,$categ) {
    # curl "http://192.168.0.52/event/api/event/read_where.php?categ=temperature&nb=3"
    global $event_server;

    // $text = str_replace(' ', '+', $text); // Replaces all spaces with +.
    // $text = preg_replace('/[^A-Za-z0-9\-+]/', '', $text);
    // $categ = str_replace(' ', '+', $categ); // Replaces all spaces with +.
    // $categ = preg_replace('/[^A-Za-z0-9\-]/', '', $categ);

    $host = gethostname();

    $input = '{
        "text" : "' . $text . '",
        "host" : "' . $host . '",
        "categ" : "' . $categ . '"
      }';      
    $direct_call = true;
    $result = create_fct($input, $direct_call);     
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

function post($url, $fields_string) {
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
    

    public function testEventCreate() {
        //test the create.php module (not the REST API !)   
        //The data you want to send via POST (as a json string)

        $input = '{
            "text" : "test from direct php call (MyTest)",
            "host" : "test host",
            "categ" : "test"
        }';
        $direct_call = true;
        $result = create_fct($input, $direct_call);     
        // echo "result : \n";
        // var_dump($result);

        $message = $result["message"]; 
        $this->assertStringContainsString("created on ",$message);
        $id = $result["id"]; 
        $this->assertGreaterThan(0,$id);
        
    }

 
    public function testEventAPICreate() {
        // curl -X POST -d "{\"text\" : \"test from phpunit\",\"host\" : \"test host\",\"categ\" : \"sqlite test\"}"  event_server + "/api/event/create.php"    
        global $event_server;
        $url = $event_server . "/api/event/create.php";

        // //The data you want to send via POST (seperated http query fields like : text=text1&host=hostABC&categ=this+is+mytype)
        // $fields = [
        //     'text' => 'text sent via POST',
        //     'host' => 'my host',
        //     'categ' => 'posted'
        // ];
    
        // //url-ify the data for the POST
        // $fields_string = http_build_query($fields);
    
        //The data you want to send via POST (as a json string)
        $fields_string = '{
            "text" : "test from phpunit via apache",
            "host" : "test host",
            "categ" : "test"
        }';  
    
        $json = post($url,$fields_string);
        $result = json_decode($json,true);
        // echo "result : \n";
        // var_dump($result);

        $message = $result["message"]; 
        $this->assertStringContainsString("created on ",$message);
    }


    // public function testEventAPIRead() {
    // # curl "http://192.168.0.52/event/api/event/read.php"
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
    
    // # curl "http://192.168.0.52/event/api/event/read_single.php?id=1"
    // public function testEventAPIReadSingle() {
    //     global $event_server;
    //     $output = myCurl($event_server . "/api/event/read_single.php?id=1");
    //     $result = json_decode($output, true);
    //     $error = $result["error"];
    //     $this->assertEquals("",$error);
    //     if ($result["error"] =="") {
    //         $t = $result["id"];
    //         $this->assertStringContainsString("1",$t);
    //     }
    // }

    // # curl "http://192.168.0.52/event/api/event/read_last.php?categ=temperature"
    // public function testEventAPIReadLast() {
    //     global $event_server;
    //     $output = myCurl($event_server . "/api/event/read_last.php?categ=temperature");
    //     $result = json_decode($output, true);
    //     $error = $result["error"];
    //     $this->assertEquals("",$error);
    //     if ($result["error"] =="") {
    //         $t = $result["categ"];
    //         $this->assertStringContainsString("temperature",$t);
    //     }
    // }

    public function testEventAPIReadWhere() {
        # curl "http://192.168.0.52/event/api/event/read_where.php?categ=temperature&nb=3"
        global $event_server;
        $output = myCurl($event_server . "/api/event/read_where.php?categ=temperature&nb=2");
        $result = json_decode($output, true);
        $num = count($result);
        //echo "number of records found: " . $num;
        $this->assertEquals(2,$num);                
    }       








    public function testLogEvent() {
        # curl "http://192.168.0.52/event/api/event/read_where.php?categ=temperature&nb=3"
        global $event_server;


        // create a dummy test record
        $result = log_event("test 123456 log from MyTest.php","mynewtype");
        
        // read the last created event
        $output = myCurl($event_server . "/api/event/read_where.php?nb=1");
        
        // check the event just read corresponds to the event created just before
        $result = json_decode($output, true);
        $error = $result["error"];
        $this->assertSame("",$error);
        $events = $result["events"];
        $num = count($events);
        //echo "number of records found: " . $num;
        $this->assertGreaterThanOrEqual(1,$num);
        if ($num > 0) {
            $event = $events[0];
            // echo "rec : \n";
            // var_dump($rec);
            // echo "\n";
            $text = $event["text"];
            $this->assertSame("test 123456 log from MyTest.php",$text);
            
        }
    }
}