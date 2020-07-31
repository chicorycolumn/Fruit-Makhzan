<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/build_array.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$fruit->id = $_POST['id'];

$result = $fruit->read_single();

echo json_encode(build_array($result));
?>
