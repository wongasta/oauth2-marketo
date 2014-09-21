<?php
session_start();
include('marketoAuth.php');
$maxAttempts = 10;
$maxAttemptsTimeOut = 900;

if (isset($_SESSION['MKTO_LAST_ACTIVITY']) && (time() - $_SESSION['MKTO_LAST_ACTIVITY'] > $maxAttemptsTimeOut)) {
    session_unset();
    session_destroy();
}
$_SESSION['MKTO_LAST_ACTIVITY'] = time();

if(empty($_SESSION['MKTO_API_CONN_COUNT'])){
    $_SESSION['MKTO_API_CONN_COUNT']=1;
}else{
    $_SESSION['MKTO_API_CONN_COUNT']++;
}


$json = file_get_contents('php://input');
$obj = json_decode($json);
$requestType = 'undefined';
$url = '';

switch($_GET['type']){
    case 'createLead':
        $requestType = 'createLead';
        $url = 'https://702-myh-396.mktorest.com/rest/v1/leads.json?access_token=' . $access_token;
        break;
    default:
        $requestType = 'undefined';
}

if($requestType!=='undefined' && $_SESSION['MKTO_API_CONN_COUNT']<=$maxAttempts){
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        CURLOPT_POSTFIELDS => json_encode($obj)
    ));
    // Send the request
    $res = curl_exec($ch);
    //close connection
    curl_close($ch);
}else{
    if($_SESSION['MKTO_API_CONN_COUNT']>$maxAttempts){
        $res = array('err_cde'=>500, 'err_msg'=>'Maximum connection reached. Please wait ' . ($maxAttemptsTimeOut/60) . ' minutes.', 'cnn_attempts'=>$_SESSION['MKTO_API_CONN_COUNT']);
    }else{
        $res = array('err_cde'=>400, 'err_msg'=>'Invalid type selection. Please specify type', 'cnn_attempts'=>$_SESSION['MKTO_API_CONN_COUNT']);
    }
    $res = json_encode($res);
}

header('Content-Type: application/json');
print $res;
