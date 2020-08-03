<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';

// if ($_GET["lemon"] < 1) {
//   $bool = false;
// } else {
//   $bool = true;
// }

$database = new Database();
$db = $database->getConnection();

function go($database, $db)
{
  if (!($result = $database->startNewGame())) {
    return [
      "status" => false,
      "message" => "An error in api/new_game.",
      "error" => $db->error,
    ];
  }
  return $result;
}

$response = go($database, $db);
$response["extra"] = clean_up_db($db);

echo json_encode($response);
return;
?>
