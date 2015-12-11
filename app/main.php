<?php

include_once '../libs/ussd/MoUssdReceiver.php';
include_once '../libs/ussd/MtUssdSender.php';

include_once 'config.php';

include_once '../libs/dbinteract.php';
include_once '../libs/log.php';

ini_set('error_log', 'ussd-app-error.log');


$getUSSD = new MoUssdReceiver();
$USSDSessionID = $getUSSD->getSessionId();
session_id($USSDSessionID);
session_start();

$content = $getUSSD->getMessage(); // get the message content
$address = $getUSSD->getAddress(); // get the sender's address
$requestId = $getUSSD->getRequestID(); // get the request ID
$applicationId = $getUSSD->getApplicationId(); // get application ID
$encoding = $getUSSD->getEncoding(); // get the encoding value
$version = $getUSSD->getVersion(); // get the version
$sessionId = $getUSSD->getSessionId(); // get the session ID;
$ussdOperation = $getUSSD->getUssdOperation(); // get the ussd operation

$Messages = array(
    "main" => "Welcome to Lyrisize!\nEnter the name of the Song:"
);


logFile("Previous Menu is := " . $_SESSION['menu-Opt']. $Messages["main"]);

if(($getUSSD->getUssdOperation()) == "mo-init"){
    showView($USSDSessionID, $Messages["main"], $INFO);
    if (!(isset($_SESSION['menu-Opt']))) {
        $_SESSION['menu-Opt'] = "main"; //Initialize main menu
    }
}

if(($getUSSD->getUssdOperation()) == "mo-cont"){
    $menuName = null;

    if(is_numeric($getUSSD->getMessage())){
        if ($getUSSD->getMessage() == "000") {
            $responseExitMsg = "Exit Program!";
            session_destroy();
        }else{
            $num = (int)$getUSSD->getMessage();
            $data = json_decode(getSessionInfo($sessionId, $address), true);
            showView($USSDSessionID, getLyrics($data[$num]['track_id'], $API), $INFO);
            //deleteSessionInfo($sessionId);
        }
    }else{
        if($getUSSD->getMessage() == 'P'){
            showView($USSDSessionID, getTracks(getContentInfo($sessionId, $address), $sessionId, $address, getPageInfo($sessionId, $address)-1, $API), $INFO);
        } else if($getUSSD->getMessage() == 'N'){
            showView($USSDSessionID, getTracks(getContentInfo($sessionId, $address), $sessionId, $address, getPageInfo($sessionId, $address)+1, $API), $INFO);
        }else {
            showView($USSDSessionID, getTracks($content, $sessionId, $address, 1, $API), $INFO);
        }
    }
}

function getTracks($content, $sessionId, $address, $pagenum = 1, $API){
    $query = "http://api.musixmatch.com/ws/1.1/track.search?apikey=$API&q=".$content."&page_size=10&f_has_lyrics=1&page=".$pagenum;
    $jdata = file_get_contents($query);
    $data = json_decode($jdata, true);
    $result = "";
    $i = 1;
    $tem = "";
    foreach($data["message"]["body"]["track_list"] as $t){
        $result .= $i.". ".$t["track"]["track_name"]." - ".$t["track"]["artist_name"]."\n";
        $tem[$i++] = array(
            "track_id" => $t["track"]["track_id"]
        );
    }
    if($pagenum > 1){
        $result .= "\nP. Previous Page.\n";
    }
    if($data["message"]["header"]["available"] > $pagenum*10){
        $result .= "N. Next Page.\n";
    }
    saveSessionInfo($content, $sessionId, $address, json_encode($tem), $pagenum);
    return $result;
}

function getLyrics($trackId, $API){
    $query = "http://api.musixmatch.com/ws/1.1/track.lyrics.get?apikey=$API&track_id=".$trackId;
    $jdata = file_get_contents($query);
    $data = json_decode($jdata, true);
    $result = $data["message"]["body"]["lyrics"]["lyrics_body"];
    return $result;
}

function showView($sessionID, $viewMessage, $info){
    $password = $info["password"];
    $destinationAddress = $info["destinationAddress"];
    $chargingAmount = $info["chargingAmount"];
    $applicationId = $info["applicationId"];
    $encoding = $info["encoding"];
    $version = $info["version"];

    if ($viewMessage == "000") {
        $ussdOperation = "mt-fin";
    } else {
        $ussdOperation = "mt-cont";
    }


    try {
        $sender = new MtUssdSender($info["mt-url"]); // Application ussd-mt sending https url
        $response = $sender->ussd($applicationId, $password, $version, $viewMessage,
            $sessionID, $ussdOperation, $destinationAddress, $encoding, $chargingAmount);
        return $response;
    } catch (UssdException $ex) {
        //throws when failed sending or receiving the ussd
        error_log("USSD ERROR: {$ex->getStatusCode()} | {$ex->getStatusMessage()}");
        return null;
    }
}
