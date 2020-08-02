<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/build_array.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$fruit->name = $_GET['name'];
$table_suffix = $_GET['table'];

if ($result = $fruit->read_single($table_suffix)) {
  if (array_key_exists("status", $result) && !$result["status"]) {
    $response = $result;
  } else {
    if ($fruit_arr = build_array($table_suffix, $result)) {
      $response["data"] = $fruit_arr;
      $response["status"] = true;
    } else {
      $response = [
        "status" => false,
        "message" => "Error in build_array.",
      ];
    }
  }
} else {
  $response = [
    "status" => false,
    "message" => "Error in read_single.",
  ];
}
$database->closeConnection();
echo json_encode($response);
?>
