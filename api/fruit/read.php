<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/table_utils.php';

$database = new Database();
$db = $database->getConnection();
$fruit = new Fruit($db);
$table_suffix = $_GET['table'];

function go($db, $fruit, $table_suffix)
{
  if (!($result = $fruit->read($table_suffix))) {
    return [
      "status" => false,
      "message" => "An error when calling Sfruit->read.",
      "error" => $db->error,
    ];
  }

  if (!$result["status"]) {
    return $result;
  }

  if (!($fruit_arr = build_inv_nst_arrays($table_suffix, $result["data"]))) {
    return [
      "status" => false,
      "message" => "An error in build_inv_nst_arrays. 1rea",
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
