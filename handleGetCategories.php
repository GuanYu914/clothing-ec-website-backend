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

$query_type = $_GET['type'];
if ($query_type === null) {
  $response = array(
    'isSuccessful'  => 'failed',
    'data'          => 'none',
    'msg'           => "please use query string 'type'",
    'detail'        => 'none',
  );

  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}
if ($query_type == 'main') {
  // echo "receive main";
  $stmt = $conn->prepare('SELECT category_id as id, name, src_url as src FROM categories ORDER BY category_id ASC');
  $res = $stmt->execute();

  if (!$res) {
    $response = array(
      'isSuccessful'  => 'failed',
      'data'          => 'none',
      'msg'           => "can't get categories info from database",
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
    array_push(
      $data,
      array(
        'id'   => $row['id'],
        'name' => $row['name'],
        'src'  => $row['src'],
      )
    );
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
}

if ($query_type == 'detail') {
  // echo "receive detail";
  $stmt = $conn->prepare('SELECT category_id as id, name, sub_category as category FROM categories ORDER BY category_id ASC');
  $res = $stmt->execute();

  if (!$res) {
    $response = array(
      'isSuccessful'  => 'failed',
      'data'          => 'none',
      'msg'           => "can't get categories info from database",
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
      'id'        => $row['id'],
      'name'      => $row['name'],
      'category'  => $row['category']
    ));
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
}
