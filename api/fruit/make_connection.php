<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';

if ($_POST["lemon"] < 1) {
  $bool = false;
} else {
  $bool = true;
}

$database = new Database();
echo json_encode($database->makeConnection($bool));
return;
// echo json_encode($res);
// return;
?>
