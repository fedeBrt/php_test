<?php
ini_set('max_execution_time', '60000'); 
require_once $_SERVER['DOCUMENT_ROOT']."/php_test/queries.php";
require_once $_SERVER['DOCUMENT_ROOT']."/php_test/api.php";

function saveData($link, $response, $url){
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

    // get data and save it every minute
     insertData($link, $url, $cpu_load, $mem_load, $inprogress_job, $queued_jobs);
}

// connection to the db
$link = mysqli_connect("localhost", "root", "", "server_info");
connectToDB($link);

// create table
createTable($link);

//while(1){
    //time limit 1 hour
    set_time_limit(60000);
    //APIs list comes from config.json
    $data = file_get_contents ("config.json");
    // array $serverAPIs
    $serverAPIs = json_decode($data, true);

    //$IPs = array("217.72.248.93", "209.18.114.72", "209.18.114.73", "209.18.114.74", "217.72.248.93");

    // recorro los servers y para cada uno tengo una respuesta - array
    foreach ($serverAPIs as $key => $url){
        //for each API I get its JSON
        $stats = getServerStatistics($url);    
        $response = json_decode($stats, true);
        //recorro cada respuesta de cada server y cogo sus datos, luego los guardo en la bd
        //tendria que ser 5 server con los mismos datos
        saveData($link, $response, $url);
    }
    
    header('Content-Type: application/json');
    echo displayNewInfo($link);   

    //sleep(60);
//}
?>