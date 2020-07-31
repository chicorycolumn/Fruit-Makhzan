<?php

include_once '../config/database.php';
include_once '../objects/fruit_class.php';

$database = new Database();
$dbwhole = $database->getConnection();
$db = $dbwhole->connection;

$fruit = new Fruit($dbwhole);
$result = $fruit->read();
$num = $result->num_rows;

if ($num > 0) {
  $fruit_arr = [];
  $fruit_arr["fruit"] = [];

  while ($row = $result->fetch_assoc()) {
    $fruit_item = [
      "id" => $row["id"],
      "name" => $row["name"],
      "quantity" => $row["quantity"],
      "selling_price" => $row["selling_price"],
      "total_sales" => $row["total_sales"],
      "created" => $row["created"],
    ];
    array_push($fruit_arr["fruit"], $fruit_item);
  }
  echo json_encode($fruit_arr["fruit"]);
} else {
  echo json_encode([]);
}
?>
