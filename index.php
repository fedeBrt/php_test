<?php
require_once $_SERVER['DOCUMENT_ROOT']."/php_test/queries.php";

$link = mysqli_connect("localhost", "root", "", "server_info");

// connection to the db
connectToDB($link);

// create table if it doesn't exist
createTable($link);

//connection to the API - server 1
function getServerStatistics($url) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache"
    ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    return $response;
}

//APIs list
// they need to be stored in a config file in a json format
//$serverAPIs = array("https://209.18.114.71/aiportal/v1.1/stats", "https://209.18.114.72/aiportal/v1.1/stats", "https://209.18.114.73/aiportal/v1.1/stats", "https://209.18.114.74/aiportal/v1.1/stats", "https://217.72.248.93/aiportal/v1.1/stats");
$data = file_get_contents ("config.json");
$serverAPIs = json_decode($data, true);
//getting data from APIs
//for($i=0; $i < sizeof($serverAPIs); $i++){
    $stats = getServerStatistics($serverAPIs[0]['apiAddress']);
//}

$response = json_decode($stats, true); //because of true, it's in an array
echo '<p>Server status: '. $response['default']['name'] .'</p>';

// VM_IP
$pattern = "/[\d.]{15}/";
$valid = filter_var("https://209.18.114.71/aiportal/v1.1/stats", FILTER_VALIDATE_IP);
preg_match($pattern,  "https://209.18.114.71/aiportal/v1.1/stats", $matches); 
print_r( $matches);

// cpu load
$cpu_load = $response['system']['cpu']['used'];
echo "<p>" . $cpu_load . "</p>";

// mem load
$mem_load = $response['system']['mem']['used'];
echo "<p>" . $mem_load . "</p>";

// queued jobs
$queued_jobs = $response['welcome']['current-jobs-delayed'] + $response['welcome']['current-jobs-ready'] 
            + $response['ocr']['current-jobs-delayed'] + $response['ocr']['current-jobs-ready'] 
            + $response['convertsource']['current-jobs-delayed'] + $response['convertsource']['current-jobs-ready']
            + $response['langdet']['current-jobs-delayed'] + $response['langdet']['current-jobs-ready'] 
            + $response['translate']['current-jobs-delayed'] + $response['translate']['current-jobs-ready'] 
            + $response['generatefinal']['current-jobs-delayed'] + $response['generatefinal']['current-jobs-ready'];

echo "<p>" . $queued_jobs . "</p>";

//inprogress jobs
$inprogress_job = $response['welcome']['current-jobs-reserved'] + $response['ocr']['current-jobs-reserved'] 
                + $response['convertsource']['current-jobs-reserved'] + $response['langdet']['current-jobs-reserved'] 
                + $response['translate']['current-jobs-reserved'] + $response['generatefinal']['current-jobs-reserved'];


echo "<p>" . $inprogress_job . "</p>";

// get data and save it every minute
insertData($link, $cpu_load, $mem_load, $inprogress_job, $queued_jobs);

// get the information and save it in a json
$rows = getData($link);

$json_retrieved_data = json_encode($rows);
//echo "<p>" . $json_retrieved_data . "</p>";
file_put_contents('results.json', $json_retrieved_data);

?>