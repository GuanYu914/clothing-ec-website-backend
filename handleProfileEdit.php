<?php
/*  response information
    {
      isSuccessful: STRING, 'failed' or 'successful'
      msg         : STRING, 'message',
      detail      : STRING, 'detail message for msg'
    }
*/

require_once('conn.php');

// takes raw data from the request
$json_from_request = file_get_contents('php://input');
// convert it into an array
$post_data = json_decode($json_from_request, true);

// check if post data is empty
if (empty($post_data['nickname']) || empty($post_data['account']) || empty($post_data['password'])) {
  $response = array(
    'isSuccessful'  => 'failed',
    'msg'           => 'empty post data',
    'detail'        => '',
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
    'detail'        => ''
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
    'detail'        => ''
  );

  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

// use sha256 hash function generate new password
$hashed_password = hash('sha256', $post_data['password']);

// start to update user data in database
$stmt = $conn->prepare("UPDATE users SET nickname = ?, password = ? WHERE account = ?");
$stmt->bind_param('sss', $post_data['nickname'], $hashed_password, $post_data['account']);
$res = $stmt->execute();

if (!$res) {
  $response = array(
    'isSuccessful'  => 'failed',
    'msg'           => 'encounter SQL error',
    'detail'        => $conn->errno . ": " . $conn->error,
  );

  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

$response = array(
  'isSuccessful'  => 'successful',
  'msg'           => '',
  'detail'        => '',
);

$response = json_encode($response);
header('Content-Type: application/json;charset=utf-8');
echo $response;
die();
