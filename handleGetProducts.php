<?php
/*
    response information
    {
      isSuccessful: STRING,  'failed' or 'successful'
      totals      : NUMBER, totals data count
      data        : encoded JSON, response data
      msg         : STRING, 'message',
      detail      : STRING, 'detail message for msg'
    } 
  */

require_once("conn.php");

$query_main_category = $_GET['main'];
$query_sub_category = $_GET['sub'];
$query_detailed_category = $_GET['detailed'];
$flag_enable_search_sub_category = false;
$flag_enable_search_detailed_category = false;
$query_limit = $_GET['limit'];
$query_offset = $_GET['offset'];

if ($query_main_category == null || $query_main_category == 'undefined') {
  $response = array(
    'isSuccessful'  => 'failed',
    'data'          => 'none',
    'msg'           => "must provide query string 'main' field",
    'detail'        => 'none',
  );

  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

if ($query_sub_category != null && $query_sub_category != "undefined") {
  $flag_enable_search_sub_category = true;
}

if ($query_detailed_category != null && $query_detailed_category != "undefined") {
  $flag_enable_search_detailed_category = true;
}

// use default value
if ($query_limit == null) {
  $query_limit = 5;
}

if ($query_offset == null) {
  $query_offset = 0;
}

$stmt = $conn->prepare('SELECT product_id as id, category FROM products ORDER BY product_id ASC');
$res = $stmt->execute();

if (!$res) {
  $response = array(
    'isSuccessful'  => 'failed',
    'data'          => 'none',
    'msg'           => "can't get products info from database",
    'detail'        => $stmt->error,
  );
  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}
// generate products_list need to fetch
$res = $stmt->get_result();
$fetch_products_list_by_id = array();
while ($row = $res->fetch_assoc()) {
  $decode_json = json_decode($row['category']);
  if (get_object_vars($decode_json)['main'] === $query_main_category) {
    if (!$flag_enable_search_sub_category) {
      array_push($fetch_products_list_by_id, $row['id']);
      continue;
    }
    if (get_object_vars($decode_json)['sub'] === $query_sub_category) {
      if (!$flag_enable_search_detailed_category) {
        array_push($fetch_products_list_by_id, $row['id']);
        continue;
      }
      if (get_object_vars($decode_json)['detailed'] === $query_detailed_category) {
        array_push($fetch_products_list_by_id, $row['id']);
      }
    }
  }
}

// get products info by products_list
// according to limit and offset parameters
// use SQL transaction to ensure data transmission
// disable auto commit
$conn->autocommit(FALSE);
$conn->begin_transaction();
$data = array();
try {
  for ($i = 0; $i < $query_limit; $i++) {
    $stmt = $conn->prepare('SELECT product_id as id, name, imgs, unitPrice as price FROM products WHERE product_id = ?');
    // 超出要抓取的產品清單內容長度，則跳出
    if ($i > count($fetch_products_list_by_id) - 1) break;
    $stmt->bind_param('i', $fetch_products_list_by_id[$i + $query_offset]);
    $res = $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    if ($row !== null) {
      array_push($data, $row);
    }
  }
} catch (mysqli_sql_exception $exception) {
  $conn->rollback();
}
$conn->autocommit(TRUE);

// 若根據 limit, offset 設定，結果抓不到資料，可能需要重新檢查 limit, offset
if (count($data) == 0) {
  $response = array(
    'isSuccessful' => 'failed',
    'totals'       => count($fetch_products_list_by_id),
    'data'         => '',
    'msg'          => "can't find anything on database",
    'detail'       => 'check your limit and offset settings',
  );

  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

$response = array(
  'isSuccessful'  => 'successful',
  'totals'        => count($fetch_products_list_by_id),
  'data'          => $data,
  'msg'           => 'none',
  'detail'        => 'none'
);

$response = json_encode($response);
header('Content-Type: application/json;charset=utf-8');
echo $response;
die();
