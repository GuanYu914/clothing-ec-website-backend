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

$query_offset = $_GET['offset'];
$query_limit = $_GET['limit'];

// use default value
if ($query_offset == null) {
  $query_offset = 0;
};

if ($query_limit == null) {
  $query_limit = 5;
}

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
$stmt = $conn->prepare('SELECT comments.comment_id as id, comments.comment as comment, users.avatar as avatar FROM comments INNER JOIN users ON comments.account = users.account ORDER BY comments.created_at DESC LIMIT ? OFFSET ?');
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
