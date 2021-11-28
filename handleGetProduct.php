<?php
/*
  response information
  {
    isSuccessful: STRING, 'failed' or 'successful'
    data        : encoded JSON, response data,
    msg         : STRING, 'message',
    detail      : STRING, 'detail message for msg'
  }
*/

require_once("conn.php");

$query_product_id = $_GET['id'];

if ($query_product_id == null) {
  $response = array(
    'isSuccessful' => 'failed',
    'data'         => 'none',
    'msg'          => "must provide query string 'id'",
    'detail'       => '',
  );

  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

$stmt = $conn->prepare("SELECT product_id as pid, category, detail, name, imgs, colors, sizes, unitPrice as price FROM products WHERE product_id = ?");
$stmt->bind_param('i', $query_product_id);
$res = $stmt->execute();

if (!$res) {
  $response = array(
    'isSuccessful' => 'failed',
    'data'         => 'none',
    'msg'          => "can't get product info from database",
    'detail'       => 'encounter sql error',
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
    'pid'     => $row['pid'],
    'category' => $row['category'],
    'name'    => $row['name'],
    'detail'  => $row['detail'],
    'imgs'    => $row['imgs'],
    'colors'  => $row['colors'],
    'sizes'   => $row['sizes'],
    'price'   => $row['price'],
  ));
}

$response = array(
  'isSuccessful' => 'successful',
  'data'         => $data,
  'msg'          => "",
  'detail'       => '',
);

$response = json_encode($response);
header('Content-Type: application/json;charset=utf-8');
echo $response;
die();