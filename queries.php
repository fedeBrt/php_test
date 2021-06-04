<?php

function connectToDB($link){
    if($link === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
}

function createTable($link){
    $create_table_query = mysqli_query($link, "CREATE TABLE IF NOT EXISTS server_status 
                        (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        dateAndTime TIMESTAMP,
                        vmIp VARCHAR(13) NOT NULL,
                        cpu FLOAT(4,2) NOT NULL,
                        mem FLOAT(4,2),
                        inProgressJobs INT(6),
                        queuedJobs INT(6))");

    if (!$create_table_query) {
        die("Error description: " . mysqli_error($link));
    }
}

function insertData($link, $ip, $cpu_load, $mem_load, $inprogress_job, $queued_jobs){
     $insert_data_query = mysqli_query($link, "INSERT INTO server_info.server_status (dateAndTime, vmIp, cpu, mem, inProgressJobs, queuedJobs)
         VALUES (NOW(), '$ip', $cpu_load, $mem_load, $inprogress_job, $queued_jobs)");
 
     if (!$insert_data_query) {
         die("Error description: " . mysqli_error($link));
     }  
 }
 

function getData($link){
    $sth = mysqli_query($link, "SELECT id, dateAndTime, vmIp, cpu, mem, inProgressJobs, queuedJobs FROM server_status ORDER BY dateAndTime DESC LIMIT 60");
    $rows = array();
    while($r = mysqli_fetch_assoc($sth)) {
        $rows[] = $r;
    }

    return $rows;
}


?>