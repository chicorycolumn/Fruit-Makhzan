<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$fruit->id = $_POST['id'];

if ($fruit->delete_self()) {
  $response = [
    "status" => true,
    "message" => "Successfully deleted!",
  ];
} else {
  $response = [
    "status" => false,
    "message" => "Cannot be deleted.",
  ];
}
print_r(json_encode($response));
?>
