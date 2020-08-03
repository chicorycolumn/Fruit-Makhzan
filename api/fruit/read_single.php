<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/table_utils.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$fruit->name = $_GET['name'];
$table_suffix = $_GET['table'];

function go($db, $fruit, $table_suffix)
{
  if (!($result = $fruit->read_single($table_suffix))) {
    return [
      "status" => false,
      "message" => "Error when calling Sfruit->read_single.",
      "error" => $db->error,
    ];
  }

  if (!$result["status"]) {
    return $result;
  }

  if (!($fruit_arr = build_inv_nst_arrays($table_suffix, $result["data"]))) {
    return [
      "status" => false,
      "message" => "Error in build_inv_nst_arrays. 1res",
      "error" => $db->error,
    ];
  }

  return [
    "status" => true,
    "data" => $fruit_arr,
  ];
}
$response = go($db, $fruit, $table_suffix);
$database->closeConnection();
echo json_encode($response);
?>
