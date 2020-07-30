<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';

$database = new Database();
$dbwhole = $database->getConnection();
$db = $dbwhole->connection;

$fruit = new Fruit($dbwhole);
$fruit->id = $_POST['id'];

$result = $fruit->read_single();

if($result->num_rows){
    $fruit_arr=array();
    $fruit_arr["fruit"]=array();

    while($row = $result->fetch_assoc()) {
        $fruit_item=array(
            "id" => $row["id"],
            "name" => $row["name"],
            "quantity" => $row["quantity"],
            "selling_price" => $row["selling_price"],
            "total_sales" => $row["total_sales"],
            "created" => $row["created"]
        );
        array_push($fruit_arr["fruit"], $fruit_item);
    } 
    echo json_encode($fruit_arr["fruit"]);
} else {
    echo json_encode(array());
}
return;


 

//******* */

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