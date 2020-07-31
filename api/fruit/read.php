<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/build_array.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$result = $fruit->read();

if (array_key_exists("status", $result)) {
  echo json_encode($result);
} else {
  $num = $result->num_rows;

  if ($fruit_arr = build_array($result)) {
    echo json_encode($fruit_arr);
  } else {
    echo [];
  }
}

?>
