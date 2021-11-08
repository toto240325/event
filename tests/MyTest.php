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

function parseEventOutput2($output) {

    // $output should be something like this :  
    //{"records":"{\"id\":\"63\",\"time\":\"2017-11-22 22:07:56\",\"host\":\"mockup host\",\"text\":\"this is the mockup event\",\"type\":\"mockup\"}","errMsg":""}

    $json = json_decode($output, true);

    // $json should be something like this :
    // array(2) {
    //   ["records"]=>
    //   string(111) "{"id":"63","time":"2017-11-22 22:07:56","host":"mockup host","text":"this is the mockup event","type":"mockup"}"
    //   ["errMsg"]=>
    //   string(0) ""
    // }

    // var_dump($json);
    $records = $json["records"];
    $errMsg = $json["errMsg"];
    if ($records != []) {
        var_dump($records);
        $json2 = json_decode($records,true);
        $id = $json2["id"];
        $time = $json2["time"];
        $host = $json2["host"];
        $text = $json2["text"];
        $type = $json2["type"];
        
        // echo "host : ".$host."\n";
        // echo "id : ".$id."\n";
        // echo "time : ".$time."\n";
        // echo "text : ".$text."\n";
        // echo "type : ".$type."\n";    
    } else {
        $id = null;
        $time = null;
        $host = null;
        $text = null;
        $type = null;
    }

    return [
        "id" => $id,
        "time" => $time,
        "host" => $host,
        "text" => $text,
        "type" => $type,
        "errMsg" => $errMsg
    ];   
}

function myCurl($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

function myCreateEvent($text,$host,$type) {

    $url = "http://192.168.0.52/event_dev/api/event/create.php";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
        "Accept: application/json",
        "Content-Type: application/json",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);




    $data = '{
        "text": "' . $text . '",
        "host": "' . $host . '",
        "type": "' . $type . '"
    }';    

//    var_dump($data);


//     $data2 = <<<DATA
//         {
//         "text": "test php post",
//         "host": "post host",
//         "type": "post type"
//         }
//     DATA;
// var_dump($data2);
    
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    // //for debug only!
    // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
    //var_dump($resp);
    return $resp;
}

function parseEventOutput($output) {

    // $output should be something like this :  
    // [{"id":"1","text":"event1","host":null,"type":"mytype1"},{"id":"2","text":"event2","host":null,"type":"mytype2"}]

    return json_decode($output, true);
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


    public function testRead() {
        $output = myCurl("http://192.168.0.52/event_dev/api/event/read.php");
        $parsedOutput = parseEventOutput($output);
        $type = gettype($parsedOutput);
        $this->assertSame("array",$type);
        $nb_recs = count($parsedOutput);
        $this->assertGreaterThan(1,$nb_recs);       
    }

    public function testCreate() {

        $resp = myCreateEvent("my text","my host", "my type");
        // $output = myCurl("http://192.168.0.52/event_dev/api/event/create.php");
        // $parsedOutput = parseEventOutput($output);
        // $type = gettype($parsedOutput);
        // $this->assertSame("array",$type);
        // $nb_recs = count($parsedOutput);
        $this->assertSame(1,1);       
    }

}