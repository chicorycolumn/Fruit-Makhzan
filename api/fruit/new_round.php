<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/table_utils.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$table_name = $_POST['table_name'];
$column_to_change = $_POST['column_to_change'];
$new_value = $_POST['new_value'];
$data_type = $_POST['data_type'];

function go($db, $fruit, $table_name, $column_to_change, $new_value, $data_type)
{
  if (
    !($result = $fruit->set_to_default(
      $table_name,
      $column_to_change,
      $new_value,
      $data_type
    ))
  ) {
    return [
      "status" => false,
      "message" => "Error when calling Sfruit->update_self.",
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
  $new_value,
  $data_type
);

$database->closeConnection();
print_r(json_encode($response));
?>
