<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$fruit->id = $_POST['id'];
$result = $fruit->delete_self();

if ($result) {
  $response = $result;
} else {
  $response = [
    "status" => false,
    "message" => "An error in api/delete.",
  ];
}
print_r(json_encode($response));
?>
