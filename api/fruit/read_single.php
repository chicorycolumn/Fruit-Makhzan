<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
 
$database = new Database();
$db = $database->getConnection();
 
$fruit = new Fruit($db);

$fruit->id = isset($_GET['id']) ? $_GET['id'] : die();

$stmt = $fruit->read_single();

if($stmt->rowCount() > 0){
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $fruit_arr=array(
        "id" => $row['id'],
        "name" => $row['name'],
        "quantity" => $row['quantity'],
        "selling_price" => $row['selling_price'],
        "total_sales" => $row['total_sales'],
        "created" => $row['created']
    );
    print_r(json_encode($fruit_arr));
} else {return false;}


?>