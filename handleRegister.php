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
if (empty($post_data['nickname']) || empty($post_data['account']) || empty($post_data['password'])) {
  $response = array(
    'isSuccessful'  => 'failed',
    'msg'           => 'empty post data',
    'detail'        => 'none'
  );

  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

// check if post data is valid under RE
if (mb_strlen($post_data['nickname']) > 10) {
  $response = array(
    'isSuccessful'  => 'failed',
    'msg'           => 'nickname is invalid',
    'detail'        => 'none'
  );

  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

if (
  !preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*[ ]).{8,12}/", $post_data['account']) ||
  !preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,12}$/", $post_data['password'])
) {
  $response = array(
    'isSuccessful'  => 'failed',
    'msg'           => 'account or password is invalid',
    'detail'        => 'none'
  );

  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

// use sha256 hash function generate new password
$hashed_password = hash('sha256', $post_data['password']);

// start to add user in database
$stmt = $conn->prepare("INSERT INTO users(nickname, account, password) VALUES(?, ?, ?)");
$stmt->bind_param('sss', $post_data['nickname'], $post_data['account'], $hashed_password);
$res = $stmt->execute();

if (!$res) {
  // if error is duplicate entry 
  if ($conn->errno === 1062) {
    $response = array(
      'isSuccessful'  => 'failed',
      'msg'           => 'detect same account',
      'detail'        => $stmt->error
    );
    $response = json_encode($response);
    header('Content-Type: application/json;charset=utf-8');
    echo $response;
    die();
  }
  // send error response
  $response = array(
    'isSuccessful'  => 'failed',
    'msg'           => 'encounter SQL error',
    'detail'        => $stmt->error
  );
  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

// get register user info in database
$stmt = $conn->prepare("SELECT user_id as id, nickname, account, password FROM users WHERE account = ?");
$stmt->bind_param('s', $post_data['account']);
$res = $stmt->execute();

if (!$res) {
  $response = array(
    'isSuccessful'  => 'failed',
    'msg'           => 'encounter SQL error',
    'detail'        => $stmt->error
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
