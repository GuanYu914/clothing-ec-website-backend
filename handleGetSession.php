<?php
/*
  response information
  {
    isSuccessful: STRING, 'failed' or 'successful',
    data        : encoded JSON, response data
    msg         : STRING, 'message',
    detail      : STRING, 'detail message for msg'
  }
 */

if (!isset($_SESSION)) {
  session_start();
}

if (!isset($_SESSION['account']) || !isset($_SESSION['nickname'])) {
  $response = array(
    'isSuccessful' => 'failed',
    'data'         => 'none',
    'msg'          => 'session variable not set',
    'detail'       => ''
  );

  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

if (empty($_SESSION['account'] ||
  empty($_SESSION['nickname']))) {
  $response = array(
    'isSuccessful' => 'failed',
    'data' => 'none',
    'msg' => 'session variable not set',
    'detail' => ''
  );
  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

// get current session data
$data = array(
  'id'        => $_SESSION['id'],
  'nickname'  => $_SESSION['nickname'],
  'account'   => $_SESSION['account'],
  'password'  => $_SESSION['password']
);

$response = array(
  'isSuccessful' => 'successful',
  'data' => $data,
  'msg' => '',
  'detail' => ''
);

$response = json_encode($response);
header('Content-Type: application/json;charset=utf-8');
echo $response;
die();
