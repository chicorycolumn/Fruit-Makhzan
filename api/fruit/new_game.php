<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';

// if ($_GET["lemon"] < 1) {
//   $bool = false;
// } else {
//   $bool = true;
// }

$database = new Database();

if ($result = $database->startNewGame()) {
  $response = $result;
} else {
  $response = [
    "status" => false,
    "message" => "An error in api/new_game.",
  ];
}

echo json_encode($response);
return;
?>
