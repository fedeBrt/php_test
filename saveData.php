<?php

function insertData($link, $cpu_load, $mem_load, $inprogress_job, $queued_jobs){
    $event_setup_query = "SET @@global.event_scheduler = 1";
    mysqli_query($link, $event_setup_query);

    $insert_data_query = "CREATE EVENT savingData
    ON SCHEDULE EVERY 1 MINUTE DO
    INSERT INTO server_info.server_status (id, dateAndTime, vmIp, cpu, mem, inProgressJobs, queuedJobs)
        VALUES (NULL, NOW(), '209.18.114.71', $cpu_load, $mem_load, $inprogress_job, $queued_jobs)";

    if (mysqli_query($link, $insert_data_query)) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $insert_data_query . "<br>" . mysqli_error($link);
    }   
}

?>