<?php
/*  response information
    {
      isSuccessful: STRING, 'failed' or 'successful'
      data        : encoded JSON, response data
      msg         : STRING, 'message'
      detail      : STRING, 'detail message for msg'
    }
*/

require_once('conn.php');

$query_webp = is_null($_GET['webp']) ? false : true;

$stmt_query = $query_webp ?
  'SELECT banner_id as id, src_webp_url as src, alt_info as alt, link_to as link FROM banners ORDER BY banner_id ASC' :
  'SELECT banner_id as id, src_url as src, alt_info as alt, link_to as link FROM banners ORDER BY banner_id ASC';
$stmt = $conn->prepare($stmt_query);
$res = $stmt->execute();

if (!$res) {
  $response = array(
    'isSuccessful' => 'failed',
    'data'         => 'none',
    'msg'          => "can't get banners info from database",
    'detail'        => $stmt->error,
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
    'id'  => $row['id'],
    'src' => $row['src'],
    'alt' => $row['alt'],
    'link' => $row['link'],
  ));
}

$response = array(
  'isSuccessful'  => 'successful',
  'data'          => $data,
  'msg'           => 'none',
  'detail'        => 'none'
);

$response = json_encode($response);
header('Content-Type: application/json;charset=utf-8');
echo $response;
die();
