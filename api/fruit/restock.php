<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
 
$database = new Database();
$db = $database->getConnection();
 
$fruit = new Fruit($db); 
$fruit->id = $_POST['id'];

$stmt = $fruit->read_single();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$new_quantity = $row['quantity'] + 10;
$stmt = $fruit->restock_self($new_quantity);

if ($stmt){
    $stmt = $fruit->read_single();
    
    if($stmt->rowCount() > 0){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $response=array(
            "id" => $row['id'],
            "name" => $row['name'],
            "quantity" => $row['quantity']
        );
    }
}else{
    $response=array(
        "status" => false,
        "message" => "Unable to restock fruit."
    );
}
print_r(json_encode($response));
?>