<?php
include_once '../config/database.php';
include_once '../objects/fruit_class.php';
 
$database = new Database();
$db = $database->getConnection();
 
$fruit = new Fruit($db);
 
$fruit->id = $_POST['id'];
$fruit->name = $_POST['name'];
$fruit->quantity = $_POST['quantity'];
$fruit->selling_price = $_POST['selling_price'];
 
if($fruit->update_self()){
    $fruit_arr=array(
        "status" => true,
        "message" => "Updated successfully!"
    );
}
else{
    $fruit_arr=array(
        "status" => false,
        "message" => "Unable to edit fruit."
    );
}
print_r(json_encode($fruit_arr));
?>