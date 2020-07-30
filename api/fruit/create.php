<?php
 
include_once '../config/database.php';
include_once '../objects/fruit_class.php';

$database = new Database();
$db = $database->getConnection();
 
$fruit = new Fruit($db);
 
$fruit->name = $_POST['name'];
$fruit->quantity = $_POST['quantity'];
$fruit->selling_price = $_POST['selling_price'];

if($fruit->create_self()){
    $fruit_arr=array(
        "status" => true,
        "message" => "Successfully created!",
        "id" => $fruit->id,
        "name" => $fruit->name,
        "quantity" => $fruit->quantity,
        "selling_price" => $fruit->selling_price,
        "total_sales" => $fruit->total_sales,
     
    );
}
else{
    $fruit_arr=array(
        "status" => false,
        "message" => "Unable to add fruit!"
    );
}
print_r(json_encode($fruit_arr));
?>