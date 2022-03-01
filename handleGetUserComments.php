<?php
/*
  response information
  {
    isSuccessful: STRING, 'failed' or 'successful'
    data        : encoded JSON, response data
    msg         : STRING, 'message'
    detail      : STRING, 'detail message for msg'
  }
 */

require_once('conn.php');

$query_offset = is_null($_GET['offset']) ? 0 : $_GET['offset'];
$query_limit = is_null($_GET['limit']) ? 5 : $_GET['limit'];
$query_webp = is_null($_GET['webp']) ? false : true;

// get total data counts
$stmt = $conn->prepare('SELECT COUNT(*) as count FROM comments');
$res = $stmt->execute();

if (!$res) {
  $response = array(
    'isSuccessful' => 'failed',
    'data'         => 'none',
    'msg'          => "can't get comments from database",
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

// get user comments
$stmt_query = $query_webp ?
  'SELECT comments.comment_id as id, comments.comment as comment, users.webp_avatar as avatar FROM comments INNER JOIN users ON comments.account = users.account ORDER BY comments.created_at DESC LIMIT ? OFFSET ?' :
  'SELECT comments.comment_id as id, comments.comment as comment, users.avatar as avatar FROM comments INNER JOIN users ON comments.account = users.account ORDER BY comments.created_at DESC LIMIT ? OFFSET ?';
$stmt = $conn->prepare($stmt_query);
$stmt->bind_param('ii', $query_limit, $query_offset);
$res = $stmt->execute();

if (!$res) {
  $response = array(
    'isSuccessful' => 'failed',
    'data'         => 'none',
    'msg'          => "can't get comments from database",
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
    'id'      => $row['id'],
    'avatar'  => $row['avatar'],
    'comment' => $row['comment']
  ));
}

// 若根據 limit, offset 設定，結果抓不到資料，可能需要重新檢查 limit, offset
if (count($data) == 0) {
  $response = array(
    'isSuccessful' => 'failed',
    'totals'       => $totals,
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
  'totals'        => $totals,
  'data'          => $data,
  'msg'           => 'none',
  'detail'        => 'none'
);

$response = json_encode($response);
header('Content-Type: application/json;charset=utf-8');
echo $response;
die();
