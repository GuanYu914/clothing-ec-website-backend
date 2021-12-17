<?php
/*
  response information
  {
    isSuccessful: STRING, 'failed' or 'successful'
    msg         : STRING, 'message'
    detail      : STRING, 'detail message for msg'
  }
*/

if (!isset($_SESSION)) {
  session_name('clothing-ec');
  session_start();
}
session_destroy();

$response = array(
  'isSuccessful'  => 'successful',
  'msg'           => 'none',
  'detail'        => 'none'
);

$response = json_encode($response);
header('Content-Type: application/json;charset=utf-8');
echo $response;
die();
