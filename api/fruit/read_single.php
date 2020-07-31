<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/build_array.php';

$database = new Database();
$dbwhole = $database->getConnection();
$db = $dbwhole->connection;

$fruit = new Fruit($dbwhole);
$fruit->id = $_POST['id'];

$result = $fruit->read_single();

echo json_encode(build_array($result));
?>
