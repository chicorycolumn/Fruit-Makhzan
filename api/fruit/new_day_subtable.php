<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/table_utils.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$table_name = $_POST['table_name'];
$data_obj = $_POST['data_obj'];
$column_to_change = $_POST['column_to_change'];
$identifying_column = $_POST['identifying_column'];
$operation = $_POST['operation'];
$new_data_key = $_POST['new_data_key'];
$data_type = $_POST['data_type'];

function go(
  $db,
  $fruit,
  $table_name,
  $column_to_change,
  $operation,
  $identifying_column,
  $data_obj,
  $new_data_key,
  $data_type
) {
  if (
    !($result = $fruit->update_multiple(
      $table_name,
      $column_to_change,
      $identifying_column,
      $operation,
      $data_obj,
      $new_data_key,
      $data_type
    ))
  ) {
    return [
      "status" => false,
      "message" => "Error when calling Sfruit->update_multiple.",
      "error" => $db->error,
    ];
  }

  if (!$result["status"]) {
    return $result;
  }

  return $result;
}

$response = go(
  $db,
  $fruit,
  $table_name,
  $column_to_change,
  $operation,
  $identifying_column,
  $data_obj,
  $new_data_key,
  $data_type
);

$database->closeConnection();
print_r(json_encode($response));

?>
