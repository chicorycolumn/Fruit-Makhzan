<?php

function evolve_trend_calculates($session_TCs, $days, $week_record)
{
  $trends = (array) json_decode($session_TCs);

  $trends['weather'] = weatherFromDay($days);
  $trends['politics'] = politicsFromDay($days, $trends['politics']);
  $trends['love'] = random_int(1, 100);
  $conf_res = conformityFromHistory(
    $trends['conformity'],
    $trends['conformity_history']
  );
  $trends['conformity'] = $conf_res['conformity'];
  $trends['conformity_history'] = $conf_res['conformity_history'];

  if ($days % 7 == 6) {
    $trends['decadence'] = decadenceFromData($week_record);
  } else {
    $trends['decadence'] = $trends['decadence'];
  }

  return json_encode($trends);
}

function decadenceFromData($week_record)
{
  $total_profit = 0;
  $total_costs = 0;
  foreach ($week_record as $day => $values) {
    $total_profit += $week_record[$day]['profit'];
    $total_costs += $week_record[$day]['costs'];
  }

  $net = $total_profit - $total_costs;
  $abs = abs($net);
  $inc = 0;

  if (0 < $abs && $abs < 50) {
    $inc = 5;
  } elseif (50 <= $abs && $abs < 100) {
    $inc = 10;
  } elseif (100 <= $abs && $abs < 500) {
    $inc = 15;
  } elseif (500 <= $abs && $abs < 2500) {
    $inc = 20;
  } elseif (2500 <= $abs && $abs < 10000) {
    $inc = 25;
  } elseif (10000 <= $abs && $abs < 50000) {
    $inc = 30;
  } elseif (50000 <= $abs && $abs < 500000) {
    $inc = 35;
  } elseif (500000 <= $abs && $abs < 10000000) {
    $inc = 40;
  } elseif (10000000 <= $abs && $abs < 200000000) {
    $inc = 45;
  } elseif (200000000 <= $abs) {
    $inc = 50;
  }

  if ($net >= 0) {
    $res = 50 + $inc;
  } else {
    $res = 50 - $inc;
  }

  // return json_encode([
  //   "total_profit" => $total_profit,
  //   "total_costs" => $total_costs,
  //   "net" => $net,
  //   "abs" => $abs,
  //   "inc" => $inc,
  //   "res" => $res,
  // ]);
  return $res;
}

function conformityFromHistory($current, $hist)
{
  if (substr($hist, -1) == "s") {
    $prob = random_int(1, 10);
    if ($prob == 1) {
      return [
        "conformity" => random_int(1, $current),
        "conformity_history" => substr($hist, -1) . "d",
      ];
    } elseif ($prob == 10) {
      return [
        "conformity" => random_int($current, 100),
        "conformity_history" => substr($hist, -1) . "u",
      ];
    } else {
      return [
        "conformity" => $current,
        "conformity_history" => substr($hist, -1) . "s",
      ];
    }
  } elseif ($hist == "uu") {
    $prob = random_int(1, 100);
    if ($prob >= 60) {
      return [
        "conformity" => random_int($current, 100),
        "conformity_history" => substr($hist, -1) . "u",
      ];
    } else {
      return [
        "conformity" => $current,
        "conformity_history" => substr($hist, -1) . "s",
      ];
    }
  } elseif ($hist == "dd") {
    $prob = random_int(1, 100);
    if ($prob >= 60) {
      return [
        "conformity" => random_int(1, $current),
        "conformity_history" => substr($hist, -1) . "d",
      ];
    } else {
      return [
        "conformity" => $current,
        "conformity_history" => substr($hist, -1) . "s",
      ];
    }
  } elseif (substr($hist, -1) == "u") {
    $prob = random_int(1, 100);
    if ($prob <= 5) {
      return [
        "conformity" => random_int(1, $current),
        "conformity_history" => substr($hist, -1) . "d",
      ];
    } elseif ($prob >= 65) {
      return [
        "conformity" => random_int($current, 100),
        "conformity_history" => substr($hist, -1) . "u",
      ];
    } else {
      return [
        "conformity" => $current,
        "conformity_history" => substr($hist, -1) . "s",
      ];
    }
  } elseif (substr($hist, -1) == "d") {
    $prob = random_int(1, 100);
    if ($prob >= 65) {
      return [
        "conformity" => random_int(1, $current),
        "conformity_history" => substr($hist, -1) . "d",
      ];
    } elseif ($prob <= 5) {
      return [
        "conformity" => random_int($current, 100),
        "conformity_history" => substr($hist, -1) . "u",
      ];
    } else {
      return [
        "conformity" => $current,
        "conformity_history" => substr($hist, -1) . "s",
      ];
    }
  }
}

function weatherFromDay($days)
{
  $days = $days % 365;

  if ($days <= 91) {
    return random_int(40, 100); //Spring
  } elseif (91 * 1 < $days && $days <= 91 * 2) {
    return random_int(80, 100); //Summer
  } elseif (91 * 2 < $days && $days <= 91 * 3) {
    return random_int(40, 80); //Autumn
  } else {
    return random_int(1, 40); //Winter
  }
}

function politicsFromDay($days, $current)
{
  if ($days % 7 == 6) {
    return random_int(1, 100);
  } else {
    return $current;
  }
}

function delete_manipulated_cookie()
{
  if (isset($_COOKIE["makhzan"])) {
    $putative_gid = $_COOKIE["makhzan"];

    if (preg_match("/[^\w]/i", $putative_gid) || strlen($putative_gid) != 15) {
      setcookie("makhzan", "", time() - 3600);
    } else {
      include_once '../api/config/database.php';
      $database = new Database();
      $db = $database->getConnection();

      $result = check_row_exists(
        $db,
        "game_id",
        $putative_gid,
        "games",
        "last_accessed",
        "s"
      );

      if (false || !$result || !$result["status"] || !$result["rows"]) {
        setcookie("makhzan", "", time() - 3600);
      }
      $database->closeConnection();
    }
  }
}

function add_to_json(
  $conn,
  $table_name,
  $json_column,
  $key,
  $value,
  $identifying_column,
  $identifying_value
) {
  $query =
    "UPDATE " .
    $table_name .
    " " .
    $json_column .
    " SET " .
    $json_column .
    "=JSON_INSERT(" .
    $json_column .
    ", '$." .
    $key .
    "', " .
    $value .
    ") WHERE " .
    $identifying_column .
    "='" .
    $identifying_value .
    "'";

  $conn->query($query);
}

function build_table_array($table, $result, $get_full)
{
  $res_array = [];

  while ($row = $result->fetch_assoc()) {
    if (substr($table, -3) == "inv") {
      $item = [
        "id" => $row["id"],
        "name" => $row["name"],
        "quantity" => $row["quantity"],
        "selling_price" => $row["selling_price"],
        "resilience" => $row["resilience"],
        "max_prices" => json_decode($row["max_prices"]),
        "popularity_factors" => json_decode($row["popularity_factors"]),
      ];

      if ($get_full) {
        $item['popularity_history'] = json_decode($row['popularity_history']);
        $item['price_history'] = json_decode($row['price_history']);
        $item['quantity_sold_history'] = json_decode(
          $row['quantity_sold_history']
        );
        $item['from_quantity_sold_history'] = json_decode(
          $row['from_quantity_sold_history']
        );
      }

      // $durability_word = $row["durability"] > 6 ? "High" : "Medium";
      // $durability_word = $row["durability"] < 4 ? "Low" : $durability_word;
    } elseif ($table == "games") {
      // $item = ["greeting" => "smello"];
      $item = [
        "game_id" => $row["game_id"],
        "last_accessed" => $row["last_accessed"],
        "money_stat" => $row["money_stat"],
        "days_stat" => $row["days_stat"],
        "trend_calculates" => $row["trend_calculates"],
      ];
    } else {
      return false;
    }

    array_push($res_array, $item);
  }

  return $res_array;
}

function make_table(
  $table_name,
  $create_table_querystring,
  $connection,
  $query_array
) {
  $query = "CREATE TABLE " . $table_name . $create_table_querystring;

  if (mysqli_query($connection, $query)) {
    // $query = "ALTER TABLE " . $table_name . " AUTO_INCREMENT=127";
    // mysqli_query($connection, $query);
    foreach ($query_array as $query) {
      mysqli_query($connection, $query);
    }
  } else {
    echo "Error: Unable to mysqli_query(Sthis->connection, Squery). Possibly there was already a table with this game id, in which case just try clicking the New Game button again." .
      PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit();
  }
  return true;
}

function wipe_previous_game($connection)
{
  $result = delete_table($connection, $_SESSION["inv_table_name"]);
  if (!$result["status"]) {
    mysqli_close($connection);
    echo $result["message"];
    echo $result["error"];
    die();
  }

  $result = delete_row($connection, "Game_ID", $_SESSION["gid"], "games", "s");

  if (!$result["status"]) {
    mysqli_close($connection);
    echo $result["message"];
    echo $result["error"];
    die();
  }
}

function clean_up_db($connection)
{
  $query = "SELECT * FROM games WHERE last_accessed < ?";

  if (!($stmt = $connection->prepare($query))) {
    return [
      "status" => false,
      "message" => "Could not prepare query.",
      "error" => $connection->error,
    ];
  }

  $t = time() - 30; /////////////// 3600 * 24 * 30
  $stmt->bind_param("i", $t);

  if (!$stmt->execute()) {
    return [
      "status" => false,
      "message" => "Error in execution.",
      "error" => $connection->error,
    ];
  }

  $result = $stmt->get_result();
  $stmt->close();

  if (!$result->num_rows) {
    return [
      "status" => true,
      "data" => [],
      "message" => "There were no outdated games to delete.",
    ];
  }

  $gid_arr = [];
  $log = [];
  while ($row = $result->fetch_assoc()) {
    array_push($gid_arr, $row["game_id"]);
  }

  foreach ($gid_arr as $gid) {
    if (
      !($result = delete_row($connection, "game_id", $gid, "games", "s")) ||
      !$result["status"]
    ) {
      $log["Undeleted_rows"][] = $gid;
    }

    if (
      !($result = delete_table($connection, $gid . "__inv")) ||
      !$result["status"]
    ) {
      $log["Undeleted_tables"][] = $gid;
    }
  }

  return [
    "status" => true,
    "message" => "Outdated games were deleted.",
  ];
}

function update_row(
  $connection,
  $change_column,
  $change_data,
  $id_column,
  $id_data,
  $table_name,
  $acronym
) {
  $query =
    "UPDATE " .
    $table_name .
    " SET " .
    $change_column .
    " = ? WHERE " .
    $id_column .
    " = ?";

  if (!($stmt = $connection->prepare($query))) {
    return [
      "status" => false,
      "message" => "Could not prepare query.",
      "error" => $connection->error,
    ];
  }

  $stmt->bind_param($acronym, $change_data, $id_data);

  if (!$stmt->execute()) {
    return [
      "status" => false,
      "message" => "Error in execution.",
      "error" => $connection->error,
    ];
  }

  $result = $stmt->get_result();
  $stmt->close();
  return ["status" => true, "message" => "Successfully updated!"];
}

function check_row_exists(
  $connection,
  $column,
  $identifier,
  $table_name,
  $column_to_return,
  $acronym
) {
  $query = "SELECT * FROM " . $table_name . " WHERE " . $column . " = ?";

  if (!($stmt = $connection->prepare($query))) {
    return [
      "status" => false,
      "message" => "Couldn't prepare this query.",
      "error" => $connection->error,
    ];
  }

  $stmt->bind_param($acronym, $identifier);

  if (!$stmt->execute()) {
    return [
      "status" => false,
      "message" => "An error in execution.",
      "error" => $connection->error,
    ];
  }

  $result = $stmt->get_result();
  $stmt->close();

  if (!$result->num_rows) {
    return [
      "status" => true,
      "message" => "Successfully queried.",
      "rows" => $result->num_rows,
    ];
  }

  $response = [
    "status" => true,
    "message" => "Successfully queried.",
    "rows" => $result->num_rows,
  ];

  if ($column_to_return) {
    $response["data"] = $result->fetch_assoc()[$column_to_return];
  }

  return $response;
}

function delete_row($connection, $column, $identifier, $table_name, $acronym)
{
  $query = "DELETE FROM " . $table_name . " WHERE " . $column . " = ?";

  if (!($stmt = $connection->prepare($query))) {
    return [
      "status" => false,
      "message" => "Couldn't prepare this query.",
      "error" => $connection->error,
    ];
  }

  $stmt->bind_param($acronym, $identifier);

  if (!$stmt->execute()) {
    return [
      "status" => false,
      "message" => "An error in execution.",
      "error" => $connection->error,
    ];
  }

  $stmt->close();

  return [
    "status" => true,
    "message" =>
      "Successfully deleted row " . $identifier . " from table " . $table_name,
  ];
}

function delete_table($connection, $table_name)
{
  $query = "DROP TABLE IF EXISTS " . $table_name;

  if (!($stmt = $connection->prepare($query))) {
    return [
      "status" => false,
      "message" => "Couldn't prepare the query.",
      "error" => $connection->error,
    ];
  }

  if (!$stmt->execute()) {
    return [
      "status" => false,
      "message" => "An error in execution.",
      "error" => $connection->error,
    ];
  }

  $stmt->close();

  return [
    "status" => true,
    "message" => "Successfully deleted table " . $table_name,
  ];
}

?>
