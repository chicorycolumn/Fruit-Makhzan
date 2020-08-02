<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/build_array.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$table_suffix = $_GET['table'];
$result = $fruit->read($table_suffix);

if (array_key_exists("status", $result)) {
  echo json_encode($result);
} else {
  $num = $result->num_rows;

  if ($fruit_arr = build_array($table_suffix, $result)) {
    $response = $fruit_arr;
  } else {
    $response = "Error in build_array.";
  }
  $database->closeConnection();
  echo json_encode($response);
}

?>
