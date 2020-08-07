<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/table_utils.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$identifying_column = $_GET['identifying_column'];
$identifying_data = $_GET['identifying_data'];
$acronym = $_GET['acronym'];
$table_name = $_GET['table_name'];

function go(
  $db,
  $fruit,
  $table_name,
  $identifying_column,
  $identifying_data,
  $acronym
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
      $acronym
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
  ////////////////////////////
  // return $result;
  ////////////////////////////
  if (!($fruit_arr = build_table_array($table_name, $result["data"]))) {
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
  $acronym
);
$database->closeConnection();
echo json_encode($response);
?>
