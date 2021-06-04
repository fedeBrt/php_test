<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

require_once $_SERVER['DOCUMENT_ROOT']."/php_test/queries.php";



$link = mysqli_connect("localhost", "root", "", "server_info");

// connection to the db
connectToDB($link);

// create table if it doesn't exist
createTable($link);

//APIs list comes from config.json
//$serverAPIs = array("https://glai-tls1.transperfect.com/aiportal/v1.1/stats", "https://209.18.114.72/aiportal/v1.1/stats", "https://209.18.114.73/aiportal/v1.1/stats", "https://209.18.114.74/aiportal/v1.1/stats", "https://217.72.248.93/aiportal/v1.1/stats");
$data = file_get_contents ("config.json");
// array $serverAPIs
$serverAPIs = json_decode($data, true);
/*echo "<br/>";
print_r ($serverAPIs['apiAddressTwo']);*/
//getting data from APIs
/*$stats = getServerStatistics("https://glai-tls1.transperfect.com/aiportal/v1.1/stats");
$response = json_decode($stats, true); 
echo '<p>Server status: '. $response['default']['name'] .'</p>';*/
//$isNullEmptyOrSet = is_null($response) || isset($response) || empty($response);
//echo 'stats is '.$isNullEmptyOrSet.'<br>';

//$IPs = array("217.72.248.93", "209.18.114.72", "209.18.114.73", "209.18.114.74", "217.72.248.93");

foreach ($serverAPIs as $key => $value){

    $IPsList = array();
    // echo 'The key : '.$key.' and the value '.$value;
    //for each API I get its JSON
    $stats = getServerStatistics($value);
    
    $response = json_decode($stats, true); 
    array_merge($response, $response);
    //because of true, it's in an array
    // In every JSON I look for rhe values I need
    // $isNullEmptyOrSet = is_null($value) || isset($value) || empty($value);
    // $isNullEmptyOrSet = is_null($response) || isset($response) || empty($response);
    /*$isResponseEmpty = empty($response);
    $isNullEmptyOrSet = is_null($stats) || isset($stats) || empty($stats);
    echo 'response is '.$isNullEmptyOrSet.'<br>';*/
    // echo 'is response empty? '.$isResponseEmpty.'<br>';
    //echo 'respuesta : '.$response["default"];

    //echo 'allalai : '.$response['welcome']['total-jobs'];

    // foreach ($response as $keyX => $valueX){
    //     echo '<p>Server status: '. $keyX.' and the value '.$valueX.'</p>';
    // }

    //print_r($response);

}

// $json_mock_2 = '[{
//                    "name" : "Federica", 
//                    "apellido" : "Valoyes",
//                    "gender" : "she doesnt knows",
//                    "age" : 19
//                 }]';

// $phpArray = json_decode($json_mock_2, true);
// echo $phpArray[0]["name"].' '.$phpArray[0]["apellido"];
// $phpObject = json_decode($json_mock_2);
// echo 'imprimiendo objeto '. $phpObject[0]->name .' '.$phpObject[0]->apellido;



foreach ($response as $key => $value){
// VM_IP
$pattern = "/[\d.]{15}/";
$valid = filter_var("https://209.18.114.71/aiportal/v1.1/stats", FILTER_VALIDATE_IP);
preg_match($pattern,  "https://209.18.114.71/aiportal/v1.1/stats", $matches); 
//print_r( $matches);

// cpu load
$cpu_load = $response['system']['cpu']['used'];
//echo "<p>cpu load" . $cpu_load . "</p>";

// mem load
$mem_load = $response['system']['mem']['used'];
//echo "<p>mem load" . $mem_load . "</p>";

// queued jobs
$queued_jobs = $response['welcome']['current-jobs-delayed'] + $response['welcome']['current-jobs-ready'] 
            + $response['ocr']['current-jobs-delayed'] + $response['ocr']['current-jobs-ready'] 
            + $response['convertsource']['current-jobs-delayed'] + $response['convertsource']['current-jobs-ready']
            + $response['langdet']['current-jobs-delayed'] + $response['langdet']['current-jobs-ready'] 
            + $response['translate']['current-jobs-delayed'] + $response['translate']['current-jobs-ready'] 
            + $response['generatefinal']['current-jobs-delayed'] + $response['generatefinal']['current-jobs-ready'];

//echo "<p>queued jobs" . $queued_jobs . "</p>";

//inprogress jobs
$inprogress_job = $response['welcome']['current-jobs-reserved'] + $response['ocr']['current-jobs-reserved'] 
                + $response['convertsource']['current-jobs-reserved'] + $response['langdet']['current-jobs-reserved'] 
                + $response['translate']['current-jobs-reserved'] + $response['generatefinal']['current-jobs-reserved'];


//echo "<p>in progress jobs" . $inprogress_job . "</p>";

// get data and save it every minute
//insertData($link, $cpu_load, $mem_load, $inprogress_job, $queued_jobs);
}

// get the information and save it in a json

function select($link){
    $rows = getData($link);

    $json_retrieved_data = json_encode($rows);
    return $json_retrieved_data;
}

header('Content-Type: application/json');
echo select($link);
//echo "<p>" . $json_retrieved_data . "</p>";
//file_put_contents('results.json', $json_retrieved_data);

?>