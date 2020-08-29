<?php
include_once '../config/database.php';
include_once '../../utils/table_utils.php';

$database = new Database();
$db = $database->getConnection();
$time = $_POST['time'];

function go($db, $time)
{
  if (
    !($result = update_row(
      $db,
      "last_accessed",
      time(),
      "game_id",
      $_SESSION['gid'],
      "games",
      "is"
    ))
  ) {
    return [
      "status" => false,
      "message" => "Error when calling update_row for timestamp.",
      "error" => $db->error,
    ];
  }
  return $result;
}

$response = go($db, $time);
$database->closeConnection();
print_r(json_encode($response));
?>
