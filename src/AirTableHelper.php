<?php
namespace jtAPI;
include_once '_include.php';

$airTableDataFP = 'data/airTable.json';
class AirTableHelper extends APIHelper {
    function getViewEmails($baseID, $tableID, $view) {
        $records = $this->getRequestBatches("https://api.airtable.com/v0/" . $baseID . '/' . $tableID . '/', 'records', array('view' => $view));
        $emails = [];
    
        foreach ($records as $record) {
            $email = $record['fields']['Email'];
            if (isset($email)){
                array_push($emails, $email); // add the email to the emails list if it isnt blank
            }
        }
        return $emails;
    }
}
function getAirtableData() {
    $filename = 'data/airTable.json';
    $airTableData = [];

    if (file_exists($filename)) {
        // Read the JSON file and decode its contents into a PHP array
        $jsonData = file_get_contents($filename);
        $airTableData = json_decode($jsonData, true);
    } else {
        // Create a new JSON file with an empty array
        file_put_contents($filename, json_encode($airTableData));
    }

    return $airTableData;
}

function saveAirtableData($airTableData) {
    global $airTableDataFP;
    
    file_put_contents($airTableDataFP, json_encode($airTableData));
    return;
}