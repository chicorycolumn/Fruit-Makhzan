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

  if (!($single_fruit = build_inv_nst_arrays($table_suffix, $result["data"]))) {
    return [
      "status" => false,
      "message" => "Error in build_inv_nst_arrays. 1re",
      "error" => $db->error,
    ];
  }

  if (
    !($result = $fruit->restock_self(
      $table_suffix,
      $single_fruit[0]["quantity"] + 10
    ))
  ) {
    return [
      "status" => false,
      "message" => "Error when calling Sfruit->restock_self.",
      "error" => $db->error,
    ];
  }

  if (!$result["status"]) {
    return $result;
  }

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
      "message" => "Error in build_inv_nst_arrays. 2re",
      "error" => $db->error,
    ];
  }
  return [
    "data" => $fruit_arr,
    "status" => true,
  ];
}

$response = go($db, $fruit, $table_suffix);
$database->closeConnection();
print_r(json_encode($response));
?>
