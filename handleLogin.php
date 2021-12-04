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
  session_start();
}

require_once("conn.php");

// takes raw data from the request
$json_from_request = file_get_contents('php://input');
// converts it into an array
$post_data = json_decode($json_from_request, true);

// check if post data is empty
if (empty($post_data['account']) || empty($post_data['password'])) {
  $response = array(
    'isSuccessful'  => 'failed',
    'msg'           => 'empty post data',
    'detail'        => ''
  );

  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

// check if post data is valid under RE
if (
  !preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*[ ]).{8,12}/", $post_data['account']) ||
  !preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,12}$/", $post_data['password'])
) {
  $response = array(
    'isSuccessful'  => 'failed',
    'msg'           => 'account or password is invalid',
    'detail'        => ''
  );

  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

// use sha256 hash function to generate new password
// in order to verify the password stored on database
$hashed_password = hash('sha256', $post_data['password']);

$stmt = $conn->prepare("SELECT user_id as id, nickname, account, password FROM users WHERE account = ? AND password = ?");
$stmt->bind_param('ss', $post_data['account'], $hashed_password);
$res = $stmt->execute();

if (!$res) {
  // send error response
  $response = array(
    'isSuccessful'  => 'failed',
    'msg'           => 'encounter SQL error',
    'detail'        => $conn->errno . ": " . $conn->error
  );
  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf8');
  echo $response;
  die();
}

$stmt->store_result();
// if no user match
if (!$stmt->num_rows()) {
  // send error response
  $response = array(
    'isSuccessful'  => 'failed',
    'msg'           => 'not founded in database',
    'detail'        => 'none',
  );
  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf8');
  echo $response;
  die();
}

// get current login user info in database
$stmt = $conn->prepare("SELECT user_id as id, nickname, account, password FROM users WHERE account = ?");
$stmt->bind_param('s', $post_data['account']);
$res = $stmt->execute();

if (!$res) {
  $response = array(
    'isSuccessful' => 'failed',
    'msg' => 'encounter SQL error',
    'detail' => $conn->errno . ": " . $conn->error
  );
  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

$res = $stmt->get_result();
$row = $res->fetch_assoc();

// build session and store variable
$_SESSION['id']       = $row['id'];
$_SESSION['nickname'] = $row['nickname'];
$_SESSION['account']  = $row['account'];
$_SESSION['password'] = $post_data['password'];  // un-hashed original password

$response = array(
  'isSuccessful'  => 'successful',
  'msg'           => '',
  'detail'        => ''
);

$response = json_encode($response);
header('Content-Type: application/json;charset=utf-8');
echo $response;
die();
