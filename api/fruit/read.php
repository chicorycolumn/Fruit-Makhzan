<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/build_array.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$table_suffix = $_GET['table'];
if ($result = $fruit->read($table_suffix)) {
  if ($result["status"]) {
    // $num = $result->num_rows;
    if ($fruit_arr = build_array($table_suffix, $result["data"])) {
      $response["data"] = $fruit_arr;
      $response["status"] = true;
    } else {
      $response = [
        "status" => false,
        "message" => "An error in build_array. 1rea",
      ];
    }
  } else {
    $response = $result;
  }
} else {
  $response = [
    "status" => false,
    "message" => "An error when calling Sfruit->read.",
  ];
}

$database->closeConnection();
echo json_encode($response);

?>
