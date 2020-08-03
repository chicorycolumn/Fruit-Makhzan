<?php

function build_array($table, $result)
{
  if ($result->num_rows) {
    $fruit_arr = [];

    while ($row = $result->fetch_assoc()) {
      if ($table == "inv") {
        $fruit_item = [
          "id" => $row["id"],
          "name" => $row["name"],
          "quantity" => $row["quantity"],
          "selling_price" => $row["selling_price"],
          "total_sales" => $row["total_sales"],
          "created" => $row["created"],
        ];
      } elseif ($table == "nst") {
        $durability_word = $row["durability"] > 6 ? "High" : "Medium";
        $durability_word = $row["durability"] < 4 ? "Low" : $durability_word;

        $fruit_item = [
          "id" => $row["id"],
          "name" => $row["name"],
          "stock_price" => $row["stock_price"],
          "popularity" => $row["popularity"] . "%",
          "durability" => $durability_word,
        ];
      } else {
        return false;
      }

      array_push($fruit_arr, $fruit_item);
    }

    return $fruit_arr;
  } else {
    return false;
  }
}

?>
