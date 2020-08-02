<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/build_array.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$fruit->id = $_GET['id'];
$table_suffix = $_GET['table'];

$result = $fruit->read_single($table_suffix);

$database->closeConnection();

if ($fruit_arr = build_array($table_suffix, $result)) {
  $response = $fruit_arr;
} else {
  $response = [
    "status" => false,
    "message" => "Error in build_array.",
  ];
}
echo json_encode($response);
?>
