<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';

// if ($_GET["lemon"] < 1) {
//   $bool = false;
// } else {
//   $bool = true;
// }

$database = new Database();
echo json_encode($database->startNewGame());
return;
?>
