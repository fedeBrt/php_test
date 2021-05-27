<?php

function connectToDB($link){
    if($link === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    echo "Connect Successfully. Host info: " . mysqli_get_host_info($link);
}

function createTable($link){
    $create_table_query = "CREATE TABLE IF NOT EXISTS server_status 
                        (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        dateAndTime TIMESTAMP,
                        vmIp VARCHAR(13) NOT NULL,
                        cpu FLOAT(4,2) NOT NULL,
                        mem FLOAT(4,2),
                        inProgressJobs INT(6),
                        queuedJobs INT(6))";

    if (mysqli_query($link, $create_table_query)) {
        echo "New table created successfully";
    } else {
        echo "Error: " . $create_table_query . "<br>" . mysqli_error($link);
    }
}

function insertData($link, $cpu_load, $mem_load, $inprogress_job, $queued_jobs){
   $event_setup_query = "SET @@global.event_scheduler = 1";
    mysqli_query($link, $event_setup_query);

    $insert_data_query = "CREATE EVENT IF NOT EXISTS savingData ON SCHEDULE EVERY 1 MINUTE DO
    INSERT INTO server_info.server_status (dateAndTime, vmIp, cpu, mem, inProgressJobs, queuedJobs)
        VALUES (NOW(), '209.18.114.71', 1, 1, 1, 1)";

    if (mysqli_query($link, $insert_data_query)) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $insert_data_query . "<br>" . mysqli_error($link);
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