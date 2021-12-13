<?php
$report_file = "report.json";
$server_url = "http://localhost:8080/report";
$json_string = file_get_contents($report_file);

function submit_report($url, $data){
    $ch = curl_init($url);
    $payload = json_encode($data);
    // Attach encoded JSON string to the POST fields
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    // Set the content type to application/json
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    // Return response instead of outputting
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Execute the POST request
    $result = curl_exec($ch);
    // Close cURL resource
    curl_close($ch);
    return($result);
}
$report = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json_string), true );
if (json_last_error()){
    var_dump($report);
    die(json_last_error_msg());
}

$response = submit_report($server_url, $report);
print_r($response . "\n");