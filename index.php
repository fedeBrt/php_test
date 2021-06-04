<?php
ini_set('max_execution_time', '60000'); 
require_once $_SERVER['DOCUMENT_ROOT']."/php_test/queries.php";
require_once $_SERVER['DOCUMENT_ROOT']."/php_test/api.php";

function saveData($link, $response, $url){
    //ip
    $ip = "1234";
    if ($url == "https://209.18.114.71/aiportal/v1.1/stats"){
        $ip = "209.18.114.71";
    } else if ($url == "https://209.18.114.72/aiportal/v1.1/statss"){
        $ip = "209.18.114.72";
    } else  if ($url == "https://209.18.114.73/aiportal/v1.1/stats"){
        $ip = "209.18.114.73";
    } else  if ($url == "https://209.18.114.74/aiportal/v1.1/stats"){
        $ip = "209.18.114.74";
    } else  if ($url == "https://217.72.248.93/aiportal/v1.1/stats"){
        $ip = "217.72.248.93";
    } else  if ($url == "https://glai-tls1.transperfect.com/aiportal/v1.1/stats"){
        $ip = "217.72.248.93";
    }

    // cpu load
    $cpu_load = $response['system']['cpu']['used'];

    // mem load
    $mem_load = $response['system']['mem']['used'];

    // queued jobs
    $queued_jobs = $response['welcome']['current-jobs-delayed'] + $response['welcome']['current-jobs-ready'] 
                + $response['ocr']['current-jobs-delayed'] + $response['ocr']['current-jobs-ready'] 
                + $response['convertsource']['current-jobs-delayed'] + $response['convertsource']['current-jobs-ready']
                + $response['langdet']['current-jobs-delayed'] + $response['langdet']['current-jobs-ready'] 
                + $response['translate']['current-jobs-delayed'] + $response['translate']['current-jobs-ready'] 
                + $response['generatefinal']['current-jobs-delayed'] + $response['generatefinal']['current-jobs-ready'];

    //inprogress jobs
    $inprogress_job = $response['welcome']['current-jobs-reserved'] + $response['ocr']['current-jobs-reserved'] 
                    + $response['convertsource']['current-jobs-reserved'] + $response['langdet']['current-jobs-reserved'] 
                    + $response['translate']['current-jobs-reserved'] + $response['generatefinal']['current-jobs-reserved'];

    insertData($link, $ip, $cpu_load, $mem_load, $inprogress_job, $queued_jobs);
}


// connection to the db
$link = mysqli_connect("localhost", "root", "", "server_info");
connectToDB($link);

// create table
createTable($link);

 //APIs list comes from config.json
 $data = file_get_contents ("config.json");
 $serverAPIs = json_decode($data, true);

  //for each API I get its JSON
 foreach ($serverAPIs as $key => $url){
     $stats = getServerStatistics($url);    
     $response = json_decode($stats, true);
     saveData($link, $response, $url);
 }

 header('Content-Type: application/json');
 echo displayNewInfo($link);

?>