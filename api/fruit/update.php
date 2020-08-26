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
$acronym = $_GET['acronym'];
$update_data = $_GET['update_data'];
$should_update_session = $_GET['should_update_session'];
$get_full = false;

if (array_key_exists("level_record", $update_data)) {
  $update_data['level_record'] = json_encode($update_data['level_record']);
}

// print_r(gettype($update_data['level_record']));
// return;

function go(
  $db,
  $fruit,
  $table_name,
  $identifying_column,
  $identifying_data,
  $acronym,
  $update_data,
  $get_full,
  $should_update_session
) {
  if (
    !($result = $fruit->update_self(
      $table_name,
      $identifying_column,
      $identifying_data,
      $acronym,
      $update_data,
      $get_full
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

  $result["update_data"] = $update_data;

  if ($should_update_session) {
    $labels = ['days_stat', 'money_stat', 'trend_calculates'];

    foreach ($labels as $label) {
      if (array_key_exists($label, $update_data)) {
        $_SESSION[$label] = $update_data[$label];
      }
    }

    $label = "level_record";

    if (array_key_exists($label, $update_data)) {
      $_SESSION[$label] = $update_data[$label];
      ////recently uncommented this
      $result['update_data']['level_record'] = json_decode(
        $result['update_data']['level_record']
      );
      ////
    }
  }

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
  $get_full,
  $should_update_session
);

$database->closeConnection();
print_r(json_encode($response));
?>
