<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/table_utils.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$table_name = $_POST['table_name'];
$identifying_column = $_POST['identifying_column'];
$identifying_data = $_POST['identifying_data'];
$profit = $_POST['profit'];
$json_data_object = $_POST['json_data_object'];
$json_data_object_name = $_POST['json_data_object_name'];
$level_record = $_POST['level_record'];

$get_full = false;

$update_data = [
  "money_stat" => $_SESSION['money_stat'] + $profit,
  "days_stat" => $_SESSION['days_stat'] + 1,
  "trend_calculates" => evolve_trend_calculates(
    $_SESSION['trend_calculates'],
    $_SESSION['days_stat'],
    $json_data_object
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
  $json_data_object,
  $json_data_object_name,
  $level_record
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
      $json_data_object,
      $json_data_object_name
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
  $_SESSION[$json_data_object_name] = json_encode($json_data_object);

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
  $json_data_object,
  $json_data_object_name,
  $level_record
);

$database->closeConnection();
print_r(json_encode($response));
?>
