<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/build_array.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$fruit->id = $_POST['id'];
$result = $fruit->read_single();

if (array_key_exists("status", $result)) {
  $response = $result;
} else {
  $single_fruit = build_array($result);
  if ($result = $fruit->restock_self($single_fruit[0]["quantity"] + 10)) {
    if ($result["status"]) {
      $result = $fruit->read_single();
      $response = build_array($result)[0];
    } else {
      $response = $result;
    }
  } else {
    $response = [
      "status" => false,
      "message" => "restock_self failed in an unknown way.",
    ];
  }
}
$database->closeConnection();
print_r(json_encode($response));
?>
