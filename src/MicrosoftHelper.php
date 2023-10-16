<?php
namespace jtAPI;
include_once 'APIHelper.php';
$accountInfo_EP = 'https://graph.microsoft.com/v1.0/me/';
$redirectPage = '';

class MicrosoftHelper extends APIHelper{ // used to send and get messages
    function send($message) {
        // $result = $this->postRequest("https://graph.microsoft.com/v1.0/me/sendMail/", $message);
        $result = $this->postRequest("https://graph.microsoft.com/v1.0/users/mediareleases@lkfmarketing.com/sendMail/", $message);
        return $result;
    }

    function getAttachments($messageID) {
        // $result = $this->getRequest("https://graph.microsoft.com/v1.0/me/messages/" . $messageID . "/attachments/");
        $result = $this->getRequest("https://graph.microsoft.com/v1.0/users/mediareleases@lkfmarketing.com/messages/" . $messageID . "/attachments/");
        $attachments = $result['value'];
        return $attachments;
    }

    function getMessage($messageID) {
        // $messageData = $this->getRequest("https://graph.microsoft.com/v1.0/me/messages/" . $messageID);
        $messageData = $this->getRequest("https://graph.microsoft.com/v1.0/users/mediareleases@lkfmarketing.com/messages/" . $messageID);
        return $messageData;
    }

    
}

function microsoftTokenInit() { // Make sure to call establishSession() before calling this. Or establish the session yourself.
    global $accountInfo_EP;
    $token = $_SESSION['t'];
    if (!isset($token)) {
        returnWithToken();                                                                          // ----------REDIRECT TO LOGIN WE DONT HAVE A TOKEN----------
    }
    else {
        $testcall = new APIHelper($token);
        $result = $testcall->getRequest($accountInfo_EP);

        if (!isset($result['displayName'])) {
            returnWithToken();                                                                     // ------------REDIRECT TO LOGIN TOKEN IS EXPIRED------------
        }
    }
    return $token;
}

function returnWithToken($url=null, $prompt="select_account") {
    $redirectPage = getRedirectPage();
    $_SESSION['prompt'] = $prompt;
    
    if (!isset($url)){
        $url= getUrlNoParams();
    }

    if (!isset($_SESSION['jt-return'])) {
        $_SESSION['jt-return'] = [];
    }
    array_push($_SESSION['jt-return'], $url);
    
    go($redirectPage);
}

function getRedirectPage() {
    global $redirectPage;
    return $redirectPage;
}

function setRedirectPage($page) {
    global $redirectPage;
    $redirectPage = $page;
}