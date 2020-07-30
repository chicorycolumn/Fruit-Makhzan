<?php
 
include_once '../config/database.php';
include_once '../objects/fruit_class.php';

$database = new Database();
$db = $database->getConnection();
 
$fruit = new Fruit($db);
 
$fruit->id = $_POST['id'];
 
if($fruit->delete_self()){
    $fruit_arr=array(
        "status" => true,
        "message" => "Successfully deleted!"
    );
}
else
{
    $fruit_arr=array(
        "status" => false,
        "message" => "Hda m'esf! Cannot be deleted."
    );
}
print_r(json_encode($fruit_arr));
?>