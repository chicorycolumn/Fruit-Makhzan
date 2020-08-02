<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/build_array.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$fruit->id = $_GET['id'];
$table_suffix = $_GET['table'];
$result = $fruit->read_single($table_suffix);

if (array_key_exists("status", $result)) {
  $response = $result;
} else {
  if ($single_fruit = build_array($table_suffix, $result)) {
    if (
      $result = $fruit->restock_self(
        $table_suffix,
        $single_fruit[0]["quantity"] + 10
      )
    ) {
      if ($result["status"]) {
        $result = $fruit->read_single($table_suffix);

        $response = build_array($table_suffix, $result)[0];

        if (!$response) {
          $response = [
            "status" => false,
            "message" => "Error in build_array.",
          ];
        }
      } else {
        $response = $result;
      }
    } else {
      $response = [
        "status" => false,
        "message" => "restock_self failed in an unknown way.",
      ];
    }
  } else {
    $response = [
      "status" => false,
      "message" => "Error in build_array.",
    ];
  }
}
$database->closeConnection();
print_r(json_encode($response));
?>
