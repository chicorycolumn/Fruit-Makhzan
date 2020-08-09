<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/table_utils.php';

$database = new Database();
$db = $database->getConnection();
$fruit = new Fruit($db);
$table_name = $_GET['table_name'];
$get_full = false;

if (isset($_GET['get_full'])) {
  $get_full = $_GET['get_full'];
}

function go($db, $fruit, $table_name, $get_full)
{
  if (!($result = $fruit->read($table_name, $get_full))) {
    return [
      "status" => false,
      "message" => "An error when calling Sfruit->read.",
      "error" => $db->error,
    ];
  }

  // print_r($result);
  // die();

  if (!$result["status"]) {
    return $result;
  }

  if (!$result['data']->num_rows) {
    return [
      "status" => false,
      "message" =>
        "There are no rows from reading the db. Apparently no rows at all in table (" .
        $table_name .
        ").",
      "error" => $db->error,
    ];
  }

  if (
    !($res_array = build_table_array($table_name, $result["data"], $get_full))
  ) {
    return [
      "status" => false,
      "message" => "An error in build_table_array. 1rea",
      "error" => $db->error,
    ];
  }

  return [
    "status" => true,
    "data" => $res_array,
  ];
}

$response = go($db, $fruit, $table_name, $get_full);
$database->closeConnection();
echo json_encode($response);

?>
