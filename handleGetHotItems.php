<?php
/*
  response information
  {
    isSuccessful: STRING, 'failed' or 'successful'
    totals      : NUMBER, total data counts
    data        : encoded JSON, response data
    msg         : STRING, 'message'
    detail      : STRING, 'detail message for msg'
  }
*/

require_once('conn.php');

$query_limit = $_GET['limit'];
$query_offset = $_GET['offset'];

// use default value
if ($query_limit == null) {
  $query_limit = 5;
}

if ($query_offset == null) {
  $query_offset = 0;
}

// get total data counts 
$stmt = $conn->prepare('SELECT COUNT(*) as count FROM products WHERE hot_item = 1');
$res = $stmt->execute();

if (!$res) {
  $response = array(
    'isSuccessful' => 'failed',
    'data'         => 'none',
    'msg'          => "can't get hotItems from database",
    'detail'       => $stmt->error,
  );
  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
  $totals = $row['count'];
}

// get hot items
$stmt = $conn->prepare('SELECT product_id as id, name, unitPrice as price, imgs as imgs FROM products WHERE hot_item = 1 ORDER BY modified_at DESC LIMIT ? OFFSET ?');
$stmt->bind_param('ii', $query_limit, $query_offset);
$res = $stmt->execute();

if (!$res) {
  $response = array(
    'isSuccessful' => 'failed',
    'data'         => 'none',
    'msg'          => "can't get hotItems from database",
    'detail'       => $stmt->error,
  );
  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

$res = $stmt->get_result();
$data = array();
while ($row = $res->fetch_assoc()) {
  array_push($data, array(
    'id'    => $row['id'],
    'name'  => $row['name'],
    'price' => $row['price'],
    'imgs'  => $row['imgs']
  ));
}

$response = array(
  'isSuccessful'  => 'successful',
  'totals'        => $totals,
  'data'          => $data,
  'msg'           => 'none',
  'detail'        => 'none'
);

$response = json_encode($response);
header('Content-Type: application/json;charset=utf-8');
echo $response;
die();
