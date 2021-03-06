<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/utils.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$identifying_column = $_GET['identifying_column'];
$identifying_data = $_GET['identifying_data'];
$type_definition_string = $_GET['type_definition_string'];
$table_name = $_GET['table_name'];

$get_full = false;
if (isset($_GET['get_full']) && json_decode($_GET['get_full'])) {
  $get_full = true;
}
$load_session_from_db = false;
if (
  isset($_GET['load_session_from_db']) &&
  json_decode($_GET['load_session_from_db'])
) {
  $load_session_from_db = true;
}

function go(
  $db,
  $fruit,
  $table_name,
  $identifying_column,
  $identifying_data,
  $type_definition_string,
  $get_full
) {
  if (
    !($result = $fruit->read_single(
      $table_name,
      $identifying_column,
      $identifying_data,
      $type_definition_string,
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

  if (!($result_arr = build_table_array($result["data"]))) {
    return [
      "status" => false,
      "message" => "Error in build_table_array. 1res",
      "error" => $db->error,
    ];
  }

  return [
    "status" => true,
    "data" => $result_arr,
  ];
}
$response = go(
  $db,
  $fruit,
  $table_name,
  $identifying_column,
  $identifying_data,
  $type_definition_string,
  $get_full
);
$database->closeConnection();

if ($load_session_from_db) {
  $_SESSION['gid'] = $identifying_data;
  $_SESSION['inv_table_name'] = $identifying_data . "__inv";
  $_SESSION['money_stat'] = $response['data'][0]['money_stat'];
  $_SESSION['days_stat'] = $response['data'][0]['days_stat'];
  $_SESSION['trend_calculates'] = $response['data'][0]['trend_calculates'];
  $_SESSION['level_record'] = $response['data'][0]['level_record'];
  $_SESSION['overall_sales_history'] =
    $response['data'][0]['overall_sales_history'];
}

echo json_encode($response);
?>
