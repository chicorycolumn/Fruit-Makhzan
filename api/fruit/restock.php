<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';
include '../../utils/build_array.php';

$database = new Database();
$dbwhole = $database->getConnection();
$db = $dbwhole->connection;

$fruit = new Fruit($dbwhole);
$fruit->id = $_POST['id'];

$result = $fruit->read_single();

if ($single_fruit = build_array($result)) {
  if ($fruit->restock_self($single_fruit[0]["quantity"] + 10)) {
    if ($result = $fruit->read_single()) {
      $single_fruit = build_array($result);
      $response = $single_fruit[0];
    } else {
      $response = [
        "status" => false,
        "message" =>
          "Unable to read single. An error such as the wrong id perhaps.",
      ];
    }
  } else {
    $response = [
      "status" => false,
      "message" =>
        "Unable to restock self. An error in fruit_class, such as the query string being malformed.",
    ];
  }
} else {
  $response = [
    "status" => false,
    "message" =>
      "Unable to read single. An error such as the wrong id perhaps.",
  ];
}
print_r(json_encode($response));
?>
