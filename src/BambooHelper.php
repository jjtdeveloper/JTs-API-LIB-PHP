<?php
namespace jtAPI;
include_once 'APIHelper.php';
class BambooHelper extends APIHelper {
    function getRequest($endpoint, $queries=null, $additionalHeaders=array()) {
        $ch = curl_init($endpoint);

        // Set the cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = array_merge([
            "Authorization: Basic " . base64_encode("$this->TOKEN:x"),
            "Accept: application/json",                                             // Set the Accept header to JSON
        ], $additionalHeaders);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        curl_close($ch);
        // Check if the result is valid JSON
        $resultArray = json_decode($result);
        return $resultArray;
    }

}