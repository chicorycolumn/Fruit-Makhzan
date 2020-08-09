<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/table_utils.php';

//REMINDER! When you want to get history for graphs, make a request to inv table, and
//          give parameter $get_full = true. This way both fruit->read_single and also
//          build_array will put the history columns on your data. I'm excluding the
//          history columns from normal requests to read_single inventory as they can be big.

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$identifying_column = $_GET['identifying_column'];
$identifying_data = $_GET['identifying_data'];
$acronym = $_GET['acronym'];
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
  $acronym,
  $get_full
) {
  // return [
  //   "tn" => $table_name,
  //   "idc" => $identifying_column,
  //   "ida" => $identifying_data,
  //   "ac" => $acronym,
  // ];

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

  // return $result;

  if (!$result["status"]) {
    return $result;
  }

  // return $result;

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

  if (
    !($fruit_arr = build_table_array($table_name, $result["data"], $get_full))
  ) {
    return [
      "status" => false,
      "message" => "Error in build_table_array. 1res",
      "error" => $db->error,
    ];
  }

  return [
    "status" => true,
    "data" => $fruit_arr,
  ];
}
$response = go(
  $db,
  $fruit,
  $table_name,
  $identifying_column,
  $identifying_data,
  $acronym,
  $get_full
);
$database->closeConnection();

if ($load_session_from_db) {
  $_SESSION['gid'] = $identifying_data;
  $_SESSION['inv_table_name'] = $identifying_data . "__inv";
  $_SESSION['money_stat'] = $response['data'][0]['money_stat'];
  $_SESSION['days_stat'] = $response['data'][0]['days_stat'];
  $_SESSION['trend_calculates'] = $response['data'][0]['trend_calculates'];
}

echo json_encode($response);
?>
