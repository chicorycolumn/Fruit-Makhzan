<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/build_array.php';

$database = new Database();
$db = $database->getConnection();

$fruit = new Fruit($db);
$fruit->name = $_GET['name'];
$table_suffix = $_GET['table'];
if ($result = $fruit->read_single($table_suffix)) {
  if ($result["status"]) {
    if ($single_fruit = build_array($table_suffix, $result["data"])) {
      if (
        $result = $fruit->restock_self(
          $table_suffix,
          $single_fruit[0]["quantity"] + 10
        )
      ) {
        if ($result["status"]) {
          if ($result = $fruit->read_single($table_suffix)) {
            if ($result["status"]) {
              if ($fruit_arr = build_array($table_suffix, $result["data"])) {
                $response = [
                  "data" => $fruit_arr,
                  "status" => true,
                ];
              } else {
                $response = [
                  "status" => false,
                  "message" => "Error in build_array. 2re",
                ];
              }
            } else {
              $response = $result;
            }
          } else {
            $response = [
              "status" => false,
              "message" => "Error when calling Sfruit->read_single.",
            ];
          }
        } else {
          $response = $result;
        }
      } else {
        $response = [
          "status" => false,
          "message" => "Error when calling Sfruit->restock_self.",
        ];
      }
    } else {
      $response = [
        "status" => false,
        "message" => "Error in build_array. 1re",
      ];
    }
  } else {
    $response = $result;
  }
} else {
  $response = [
    "status" => false,
    "message" => "Error when calling Sfruit->read_single.",
  ];
}

$database->closeConnection();
print_r(json_encode($response));
?>
