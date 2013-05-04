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

$filename = 'ds_' . $headers['X-DataSift-ID'] . '_' . uniqid() . '.json';

// Write to disk
$fh = fopen('/data/'. $filename, 'a+');
fwrite($fh, file_get_contents('php://input')."\n");

// Write named pipe
$pipe="/tmp/queueserver-input";
$fhp = fopen($pipe, 'w') or die("can't open file $pipe");
fwrite($fhp, $filename);

// Respond to DataSift Push
echo json_encode(array('success' => true));