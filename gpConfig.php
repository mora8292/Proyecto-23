<?php


//Include Google client library 
include_once 'src/Google_Client.php';
include_once 'src/contrib/Google_Oauth2Service.php';
require_once 'config.local.php';

/*
 * Configuration and setup Google API
 */
$clientId = $IdClient; //Google client ID
$clientSecret = $SecretClient; //Google client secret
$redirectURL = 'http://localhost:8080/index.php';  //Callback URL

//Call Google API
$gClient = new Google_Client(); 
$gClient->setApplicationName('Login to CodexWorld.com');
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setRedirectUri($redirectURL);

$google_oauthV2 = new Google_Oauth2Service($gClient);
?>  
