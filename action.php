<?php
// require_once('init.php');

$payload = @file_get_contents("php://input");
$data = json_decode($payload); 

if (gettype($data) !== 'object') 
die(json_encode(["status"=>false,"response"=>"Data is not an object"]));

switch($data->action){
  case 'Get Live-Score':
    $liveScore = get_liveScore($data);
      printResponse($liveScore);
    break;

  default:
    print_r('No Action');
    break;
}

//Checkpoint
function printResponse($response){
  if($response['status']){
      print_r(json_encode($response));
  }else{
      die(json_encode($response));
  }
}

//Pointstreak API
function get_liveScore($data){
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.pointstreak.com/baseball/season/schedule/'.$data->sessionID.'/json',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
      'apikey: '.$data->apiKey.''
    ),
  ));
  
  $resp = curl_exec($curl);
  $response = json_decode($resp);

  //Check Response Code eg: 200 | 204 | 400
  $responseCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
  
  curl_close($curl);
  
  if ($responseCode == 200 || $responseCode == 204){
    return ["status"=>true,"response"=>$response,"request"=>__FUNCTION__];
  } else {
    return ["status"=>false,"response"=>$response,"request"=>__FUNCTION__];
  }
}