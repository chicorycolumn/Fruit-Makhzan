<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/table_utils.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$table_name = $_GET['table_name'];
$identifying_column = $_GET['identifying_column'];
$identifying_data = $_GET['identifying_data'];
$profit = $_GET['profit'];
$json_data_object = $_GET['json_data_object'];

$get_full = false;

$update_data = [
  "money_stat" => $_SESSION['money_stat'] + $profit,
  "days_stat" => $_SESSION['days_stat'] + 1,
  "trend_calculates" => evolve_trend_calculates(
    $_SESSION['trend_calculates'],
    $_SESSION['days_stat'],
    $json_data_object['overall_sales_history']
  ),
];
$acronym = "iiss";

function go(
  $db,
  $fruit,
  $table_name,
  $identifying_column,
  $identifying_data,
  $acronym,
  $update_data,
  $json_data_object
) {
  if (
    !($result = $fruit->update_self(
      $table_name,
      $identifying_column,
      $identifying_data,
      $acronym,
      $update_data
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

  if (
    !($result = $fruit->update_json(
      $table_name,
      $identifying_column,
      $identifying_data,
      $json_data_object
    ))
  ) {
    return [
      "status" => false,
      "message" => "Error when calling Sfruit->update_json.",
      "error" => $db->error,
    ];
  }

  if (!$result["status"]) {
    return $result;
  }

  $_SESSION['days_stat'] = $update_data['days_stat'];
  $_SESSION['money_stat'] = $update_data['money_stat'];
  $_SESSION['trend_calculates'] = $update_data['trend_calculates'];

  $result['update_data'] = $update_data;
  $result['update_data']['trend_calculates'] = json_decode(
    $result['update_data']['trend_calculates']
  );

  return $result;
}

$response = go(
  $db,
  $fruit,
  $table_name,
  $identifying_column,
  $identifying_data,
  $acronym,
  $update_data,
  $json_data_object
);

$database->closeConnection();
print_r(json_encode($response));
?>
