<?php
/*
  response information {
    isSuccessful: STRING, 'failed' or 'successful'
    msg         : STRING, 'message'
    detail      : STRING, 'detail message for msg'
  }
*/

if (!isset($_SESSION)) {
  session_start();
}

require_once('conn.php');

// if account session var is not set
if (empty($_SESSION['account'])) {
  $response = array(
    'isSuccessful'  => "failed",
    'data'          => 'none',
    'msg'           => 'session variable not set',
    'detail'        => 'none',
  );

  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

// takes raw data from the request
$json_from_request = file_get_contents('php://input');
// convert it into an array
$post_data = json_decode($json_from_request, true)['productsInfo'];
$post_data = json_encode($post_data);

// check if this user have favorite items
$stmt = $conn->prepare("SELECT * FROM favorite_products WHERE liked_by = ?");
$stmt->bind_param('s', $_SESSION['account']);
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

$stmt->store_result();

// if no user match, create new
if (!$stmt->num_rows()) {
  $stmt = $conn->prepare("INSERT INTO favorite_products (liked_by, products) VALUES (? ,?)");
  $stmt->bind_param('ss', $_SESSION['account'], $post_data);
  $res = $stmt->execute();

  if (!$res) {
    $response = array(
      'isSuccessful'  => 'failed',
      'msg'           => 'encounter SQL error',
      'detail'        => $stmt->error,
    );
    $response = json_encode($response);
    header('Content-Type: application/json;charset=utf-8');
    echo $response;
    die();
  }
} else {
  $stmt = $conn->prepare("UPDATE favorite_products SET products = ? WHERE liked_by = ?");
  $stmt->bind_param('ss', $post_data,  $_SESSION['account']);
  $res = $stmt->execute();

  if (!$res) {
    $response = array(
      'isSuccessful'  => 'failed',
      'msg'           => 'encounter SQL error',
      'detail'        => $stmt->error,
    );
    $response = json_encode($response);
    header('Content-Type: application/json;charset=utf-8');
    echo $response;
    die();
  }
}

$response = array(
  'isSuccessful'  => 'successful',
  'msg'           => 'none',
  'detail'        => 'none'
);

$response = json_encode($response);
header('Content-Type: application/json;charset=utf-8');
echo $response;
die();
