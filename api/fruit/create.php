<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$fruit->name = $_POST['name'];
$fruit->quantity = $_POST['quantity'];
$fruit->selling_price = $_POST['selling_price'];

$result = $fruit->create_self();

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
print_r(json_encode($response));
?>
