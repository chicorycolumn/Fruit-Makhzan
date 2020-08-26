<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$fruit->name = $_GET['name'];
$table_name = $_GET['table_name'];

function go($db, $fruit, $table_name)
{
  if (!($result = $fruit->delete_self($table_name))) {
    return [
      "status" => false,
      "message" => "Error when calling Sfruit->delete_self.",
      "error" => $db->error,
    ];
  }
  return $result;
}

$response = go($db, $fruit, $table_name);
$database->closeConnection();
print_r(json_encode($response));
?>
