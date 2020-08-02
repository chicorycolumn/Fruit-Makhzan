<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/build_array.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$fruit->name = $_GET['name'];
$fruit->quantity = $_GET['quantity'];
$fruit->selling_price = array_key_exists('selling_price', $_GET)
  ? $_GET['selling_price']
  : 0;
$table_suffix = $_GET['table'];

$result = $fruit->create_self($table_suffix);

if ($result["status"]) {
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
} else {
  $response = $result;
}
$database->closeConnection();
print_r(json_encode($response));
?>
