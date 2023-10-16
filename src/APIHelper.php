<?php
namespace jtAPI;
include_once '_include.php';
//-------------------------------------- Important Links --------------------------------------
$settingsPage = 'https://vgs.lkfhosting.com/jt-testing/PRHelperBETA/settings.php';
$selectMessagePage = 'https://vgs.lkfhosting.com/jt-testing/PR-Automator/selectMessage.php';
$selectedRecipientsPage = 'https://vgs.lkfhosting.com/jt-testing/PR-Automator/selectRecipients.php';


//------------------------------------- General API Class --------------------------------------
class APIHelper {
    public string $TOKEN;
    function __construct($token) {
        $this->TOKEN = $token;
    }
    // Uses curl to Handle basic API connections
    function getRequest($endpoint, $queries=null, $additionalHeaders=array()) {
        $ch = curl_init();                                              // initiate cURL

        $headers = array_merge(array('Authorization: Bearer ' . $this->TOKEN), $additionalHeaders); // set the token and add additional headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (isset($queries)) {                                           // if there are queries add them to the url
            $url = $endpoint . '?' . http_build_query($queries);
        } else {
            $url = $endpoint;
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $returnedJson = curl_exec($ch);                                 // execute the cURL and get the returned Json
        $returnArray = json_decode($returnedJson, 1);                   // convert Json to php array
        curl_close($ch);                                                // close curl connection
        return $returnArray;
    }

    function postRequest($endpoint, $postData, $queries=null, $contentType='application/json') {
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->TOKEN,
    
            'Content-type: ' . $contentType
        ));
        if (isset($queries)){
            $url = $endpoint . '?' . http_build_query($queries);
        } else {
            $url = $endpoint;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            json_encode($postData)
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
        $result = json_decode(curl_exec($ch), 1);
        curl_close($ch);
        return $result;
    }

    function getRequestBatches($endpoint, $recordName, $queries=array()) {
        $isMore = true;
        $returnArray = array();                                 // initializing an empty array

        while($isMore) {
            $result = $this->getRequest($endpoint, $queries);              // get the the batch using the getrequest method

            if (isset($result['offset'])) {                     // check if the batch has an offset variable, if so there are more records to get
                $queries['offset'] = $result['offset'];
            }
            else {$isMore = false;}                             // if there is no offset variable then there are no more records to retreive

            $returnArray = array_merge($returnArray, $result[$recordName]);     // merge the return array with the batch just retrieved 
        }
        return $returnArray;
    }
    

    
}

function establishSession() {
    if (session_status() == PHP_SESSION_NONE) { // MUST BE AT THE BEGINNING OF ALL THE FILES DONT JUST CALL SESSION START
        session_start();
    }
}

// FOR THE PROMPT ARGUMENT:
//none - no show login or consent
// login - request to reauthenticate
// consent - request the user consent
// select_account - auth server to choose list of account or single account
function getUrl() {
    $currentUrl = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return $currentUrl;
}

function getUrlNoParams() {
    $currentUrl = explode("?", $_SERVER['REQUEST_URI']);
    return "https://". $_SERVER['HTTP_HOST'] . $currentUrl[0];
}

function returnToPrev() {
    $return_url = array_pop($_SESSION['jt-return']);
    go($return_url);
}

function go($url) {
    header('Location: ' . $url);
}

function paramStr($params) { // Turns an array into a string that can be put into html for a js function
    $paramsString = "`" . implode("`,`" , $params) . "`";
    return $paramsString;
}