<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$fruit->id = $_GET['id'];
$table_suffix = $_GET['table'];
if ($result = $fruit->delete_self($table_suffix)) {
  $response = $result;
} else {
  $response = [
    "status" => false,
    "message" => "Error when calling Sfruit->delete_self.",
  ];
}

$database->closeConnection();
print_r(json_encode($response));
?>
