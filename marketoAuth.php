<?php

//set POST variables
$marketoID = '';
$marketoPublicKey = '';
$marketoPrivateKey = '';

$url = 'https://' . $marketoID . '.mktorest.com/identity/oauth/token';

$fields = array(
    'grant_type' => urlencode('client_credentials'),
    'client_id' => urlencode($marketoPublicKey),
    'client_secret' => urlencode($marketoPrivateKey)
);

//url-ify the data for the POST
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');

//open connection
$ch = curl_init();
curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url,
    CURLOPT_POST => count($fields),
    CURLOPT_POSTFIELDS => $fields_string
));

//execute post
$result = curl_exec($ch);

$result = json_decode($result, true);
$access_token = $result['access_token'];

//close connection
curl_close($ch);
