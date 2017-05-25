<?php
  require_once("Api.php");

    if ($_SERVER['REQUEST_METHOD'] == "GET") {
  	$data = str_replace(" ", "+", $_GET['quote']);
  	$param = array(
  		'quote' => $data,
  		'oauth_access_token' => $_GET['oauth_access_token'],
        'oauth_access_token_secret' => $_GET['oauth_access_token_secret'],
        'consumer_key' => $_GET['consumer_key'],
        'consumer_secret' => $_GET['consumer_secret'],
  	);
    
    $url = 'https://ajalan065.github.io/SampleApi/Api.php/test?' . http_build_query($param);
   $ch = curl_init();

    // set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    // grab URL and pass it to the browser
    curl_exec($ch);

    // close cURL resource, and free up system resources
    curl_close($ch);
    }

?>