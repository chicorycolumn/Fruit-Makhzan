<?php

function build_array($result)
{
  if ($result->num_rows) {
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
    return $fruit_arr["fruit"];
  } else {
    return false;
  }
}

?>
