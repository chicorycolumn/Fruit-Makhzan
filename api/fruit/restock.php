<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include('../../utils/build_array.php');

$database = new Database();
$dbwhole = $database->getConnection();
$db = $dbwhole->connection;

$fruit = new Fruit($dbwhole);
$fruit->id = $_POST['id'];

if ($result = $fruit->read_single()){

    $single_fruit = build_array($result);
    $new_quantity = $single_fruit[0]["quantity"] + 10;
    $fruit->restock_self($new_quantity);

    if ($result = $fruit->read_single()){
            $single_fruit = build_array($result);
            $response = $single_fruit[0];
        }

}else{
    $response=array(
        "status" => false,
        "message" => "Unable to restock fruit."
    );
}
print_r(json_encode($response));
?>