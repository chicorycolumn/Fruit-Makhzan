<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/table_utils.php';

$database = new Database();
$db = $database->getConnection();
$fruit = new Fruit($db);
$table_name = $_GET['table_name'];

function go($db, $fruit, $table_name)
{
  if (!($result = $fruit->read($table_name))) {
    return [
      "status" => false,
      "message" => "An error when calling Sfruit->read.",
      "error" => $db->error,
    ];
  }

  if (!$result["status"]) {
    return $result;
  }

  if (!($res_array = build_table_array($table_name, $result["data"]))) {
    return [
      "status" => false,
      "message" => "An error in build_table_array. 1rea",
      "error" => $db->error,
    ];
  }

  return [
    "status" => true,
    "data" => $res_array,
  ];
}

$response = go($db, $fruit, $table_name);
$database->closeConnection();
echo json_encode($response);

?>
