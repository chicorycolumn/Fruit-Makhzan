<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$fruit->id = $_GET['id'];
$table_suffix = $_GET['table'];
$result = $fruit->delete_self($table_suffix);

if ($result) {
  $response = $result;
} else {
  $response = [
    "status" => false,
    "message" => "An error in api/delete.",
  ];
}
$database->closeConnection();
print_r(json_encode($response));
?>
