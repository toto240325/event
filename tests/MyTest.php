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

function myCurl($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

function parseEventOutput($output) {

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

    // public function testCurlMock() {
    //     $output = myCurl("http://192.168.0.45/monitor/getEvent.php?eventFct=mockup");
    //     $parsedOutput = parseEventOutput($output);
    
    //     $this->assertSame("mockup host",$parsedOutput["host"]);
    //     $this->assertSame("mockup",$parsedOutput["type"]);
    //     $this->assertSame("this is the mockup event",$parsedOutput["text"]);
    //     $this->assertSame("",$parsedOutput["errMsg"]);
    //     // '{"records":[{"id":"135","time":"2021-09-15 00:55:19","host":"myHost","text":"my text","type":"mytype"}],"errMsg":""}', 
    //         // trim($this->_execute($args))
    // }

    // public function testCurlAddEvent() {
    //     $output = myCurl("http://localhost/monitor/getEvent.php?eventFct=add&&host=myHost&text=my+text&type=my+type");
    //     // output should be : 
    //     // {"records":[],"errMsg":"Record inserted correctly"}
    //     $parsedOutput = parseEventOutput($output);
    
    //     // $this->assertSame("mockup host",$parsedOutput["host"]);
    //     // $this->assertSame("mockup",$parsedOutput["type"]);
    //     // $this->assertSame("this is the mockup event",$parsedOutput["text"]);
    //     $this->assertSame("Record inserted correctly",$parsedOutput["errMsg"]);
    // }

    // public function testCurlLastEvent() {
    //     $output = myCurl("http://192.168.0.45/monitor/getEvent.php?eventFct=getLastEventByType&type=my+type");
    //     //output (following previous test having added some record) should be something like : 
    //     // {"records":[{"id":"13","time":"2021-11-01 01:52:11","host":"myHost","text":"my text","type":"my type"}],"errMsg":""}
    //     $parsedOutput = parseEventOutput($output);
    
    //     $this->assertSame("myhost",$parsedOutput["host"]);
    //     $this->assertSame("mockup",$parsedOutput["type"]);
    //     $this->assertSame("this is the mockup event",$parsedOutput["text"]);
    //     $this->assertSame("",$parsedOutput["errMsg"]);
    //     // '{"records":[{"id":"135","time":"2021-09-15 00:55:19","host":"myHost","text":"my text","type":"mytype"}],"errMsg":""}', 
    //         // trim($this->_execute($args))
    // }

}