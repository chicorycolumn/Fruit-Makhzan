<?php

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

function build_table_array($table, $result)
{
  if (!$result->num_rows) {
    return false;
  }

  // $res_array = [["test" => "eg"]];
  $res_array = [];

  while ($row = $result->fetch_assoc()) {
    if ($table != "games") {
      $item = [
        "id" => $row["id"],
        "name" => $row["name"],
        "quantity" => $row["quantity"],
        "selling_price" => $row["selling_price"],
        "resilience" => $row["resilience"],
        "max_prices" => $row["max_prices"],
        "popularity_factors" => $row["popularity_factors"],
      ];

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
    array_push($gid_arr, $row["Game_ID"]);
  }

  foreach ($gid_arr as $gid) {
    if (
      !($result = delete_row($connection, "Game_ID", $gid, "games", "s")) ||
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
