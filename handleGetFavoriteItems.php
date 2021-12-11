<?php
/*
  response information
  {
    isSuccessful: STRING, 'failed' or 'successful'
    data        : encoded JSON, response data,
    msg         : STRING, 'message'
    detail      : STRING, 'detail message for 'msg'
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

// get favorite products of current user
$stmt = $conn->prepare("SELECT products FROM favorite_products WHERE liked_by = ?");
$stmt->bind_param('s', $_SESSION['account']);
$res = $stmt->execute();

if (!$res) {
  $response = array(
    'isSuccessful'  => 'failed',
    'data'          => 'none',
    'msg'           => 'encounter SQL error',
    'detail'        => $stmt->error,
  );

  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

// get every product id of favorite products
$fetch_products_list_by_pid = array();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

// fetch no data
if ($row == null) {
  $response = array(
    'isSuccessful'  =>  'successful',
    'data'          =>   array(),
    'msg'           =>  'empty data',
    'detail'        =>  'none',
  );

  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

$decode_json = json_decode($row['products']);
for ($i = 0; $i < count($decode_json); $i++) {
  array_push($fetch_products_list_by_pid, $decode_json[$i]->pid);
}

// get products info by fetch_products_list_by_pid
// use SQL transaction to ensure data transmission
// disable auto commit 
$conn->autocommit(FALSE);
$conn->begin_transaction();
$data = array();
try {
  for ($i = 0; $i < count($fetch_products_list_by_pid); $i++) {
    $stmt = $conn->prepare("SELECT product_id as id, name, imgs, unitPrice as price FROM products WHERE product_id = ?");
    $stmt->bind_param('i', $fetch_products_list_by_pid[$i]);
    $res = $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    array_push($data, $row);
  }
  $conn->autocommit(TRUE);
} catch (mysqli_sql_exception $exception) {
  $conn->rollback();
}

$response = array(
  'isSuccessful'  => 'successful',
  'data'          => $data,
  'msg'           => 'none',
  'detail'        => 'none',
);

$response = json_encode($response);
header('Content-Type: application/json;charset=utf-8');
echo $response;
die();
