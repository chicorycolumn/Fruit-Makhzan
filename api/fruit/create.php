<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$fruit->name = $_GET['name'];
$fruit->quantity = $_GET['quantity'];
$fruit->selling_price = $_GET['selling_price'];
$table_suffix = $_GET['table'];

$result = $fruit->create_self($table_suffix);

if ($result["status"]) {
  $response = [
    "status" => true,
    "message" => "Successfully created!",
    "id" => $fruit->id,
    "name" => $fruit->name,
    "quantity" => $fruit->quantity,
    "selling_price" => $fruit->selling_price,
    "total_sales" => $fruit->total_sales,
  ];
} else {
  $response = $result;
}
$database->closeConnection();
print_r(json_encode($response));
?>
