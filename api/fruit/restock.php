<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/table_utils.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
$increment = filter_var($_POST['increment'], FILTER_SANITIZE_SPECIAL_CHARS);
$table_name = filter_var($_POST['table_name'], FILTER_SANITIZE_SPECIAL_CHARS);
$identifying_column = "name";
$identifying_data = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
$acronym = "s";
$get_full = false;

function go(
  $db,
  $fruit,
  $increment,
  $table_name,
  $identifying_column,
  $identifying_data,
  $acronym,
  $name,
  $get_full
) {
  if (!($result = $fruit->restock_self($name, $table_name, $increment))) {
    return [
      "status" => false,
      "message" => "Error when calling Sfruit->restock_self.",
      "error" => $db->error,
    ];
  }

  if (!$result["status"]) {
    return $result;
  }

  if (
    !($result = $fruit->read_single(
      $table_name,
      $identifying_column,
      $identifying_data,
      $acronym,
      $get_full
    ))
  ) {
    return [
      "status" => false,
      "message" => "Error when calling Sfruit->read_single.",
      "error" => $db->error,
    ];
  }

  if (!$result["status"]) {
    return $result;
  }

  if (!$result['data']->num_rows) {
    return [
      "status" => false,
      "message" =>
        "There are no rows from reading the db. The identifying data (" .
        $identifying_data .
        ") at identifying column (" .
        $identifying_column .
        ") does not correspond to anything in the table (" .
        $table_name .
        ").",
      "error" => $db->error,
    ];
  }

  if (!($fruit_arr = build_table_array($result["data"]))) {
    return [
      "status" => false,
      "message" => "Error in build_table_array. 2re",
      "error" => $db->error,
    ];
  }
  return [
    "data" => $fruit_arr,
    "status" => true,
  ];
}

$response = go(
  $db,
  $fruit,
  $increment,
  $table_name,
  $identifying_column,
  $identifying_data,
  $acronym,
  $name,
  $get_full
);
$database->closeConnection();
print_r(json_encode($response));
?>
