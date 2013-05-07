<?php
if (!function_exists('apache_request_headers')) {
    function apache_request_headers() {
        foreach($_SERVER as $key=>$value) {
            if (substr($key,0,5)=="HTTP_") {
                $key=str_replace(" ","-",ucwords(str_replace("_"," ",substr($key,5))));
                $out[$key]=$value;
            }
        }
        return $out;
    }
}
$headers = apache_request_headers();

// Build file name
$filename = 'ds_' . $headers['X-DataSift-ID'] . '_' . uniqid() . '.json';

// Write inbound payload to disk
$fh = fopen('/data/' . $filename, 'w+');
fwrite($fh, file_get_contents('php://input')."\n");


// Respond to DataSift Push
echo json_encode(array('success' => true));


// Write named pipe to queue

$pipe="/tmp/queueserver-input";
$fhp = fopen($pipe, 'r+') or die("can't open file $pipe");
//stream_set_blocking($pipe,false); 
fwrite($fhp, $filename."\n");
