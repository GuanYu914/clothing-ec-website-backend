<?php
/*
  response information
  {
    isSuccessful: STRING, 'failed' or 'successful'
    data        : encoded JSON, response data,
    msg         : STRING, 'message'
    detail      : STRING, 'detail message for msg'
  }
*/

if (!isset($_SESSION)) {
  session_name('clothing-ec');
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

// get cart products of current user
$stmt = $conn->prepare("SELECT products FROM carts WHERE account = ?");
$stmt->bind_param('s', $_SESSION['account']);
$res = $stmt->execute();

if (!$res) {
  $response = array(
    'isSuccessful'  => 'failed',
    'data'          => 'none',
    'msg'           => 'encounter SQL error',
    'detail'        => 'none'
  );

  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

// get every product id of cart products
$fetch_products_list_by_pid = array();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

// fetch no data
if ($row == null) {
  $response = array(
    'isSuccessful'  => 'successful',
    'data'          =>  array(),
    'msg'           => 'empty data',
    'detail'        => 'none'
  );

  $response = json_encode($response);
  header('Content-Type: application/json;charset=utf-8');
  echo $response;
  die();
}

$decode_json = json_decode($row['products']);
for ($i = 0; $i < count($decode_json); $i++) {
  array_push(
    $fetch_products_list_by_pid,
    array(
      'pid'       => $decode_json[$i]->pid,
      'color'     => $decode_json[$i]->color,
      'size'      => $decode_json[$i]->size,
      'quantity'  => $decode_json[$i]->quantity,
    )
  );
}

// get products info by fetch_products_list_by_pid
// use SQL transaction to ensure data transmission
// disable auto commit
$conn->autocommit(FALSE);
$conn->begin_transaction();
$data = array();
try {
  for ($i = 0; $i < count($fetch_products_list_by_pid); $i++) {
    $stmt = $conn->prepare("SELECT product_id as pid, name, imgs, colors, sizes, unitPrice as price FROM products WHERE product_id = ?");
    $stmt->bind_param('i', $fetch_products_list_by_pid[$i]['pid']);
    $res = $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    $pid_selected_color = $fetch_products_list_by_pid[$i]['color'];
    $pid_selected_size = $fetch_products_list_by_pid[$i]['size'];

    $selected_colors = json_decode($row['colors']);
    for ($j = 0; $j < count($selected_colors); $j++) {
      if ($selected_colors[$j]->hexcode == $pid_selected_color) {
        $selected_colors[$j]->selected = TRUE;
      } else {
        $selected_colors[$j]->selected = FALSE;
      }
    }

    $selected_sizes = json_decode($row['sizes']);
    for ($k = 0; $k < count($selected_sizes); $k++) {
      if ($selected_sizes[$k]->name == $pid_selected_size) {
        $selected_sizes[$k]->selected = TRUE;
      } else {
        $selected_sizes[$k]->selected = FALSE;
      }
    }

    array_push(
      $data,
      array(
        'pid'       => $row['pid'],
        'name'      => $row['name'],
        'imgs'      => $row['imgs'],
        'colors'    => json_encode($selected_colors),
        'sizes'     =>  json_encode($selected_sizes),
        'price'     => $row['price'],
        'quantity'  => $fetch_products_list_by_pid[$i]['quantity']
      )
    );
  }
  $conn->autocommit(TRUE);
} catch (mysqli_sql_exception $exception) {
  $conn->rollback();
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
