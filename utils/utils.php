<?php

function check_gid()
{
  $gid = delete_manipulated_cookie();
  return !(preg_match("/[^\w]/i", $gid) || strlen($gid) != 15);
}

function delete_manipulated_cookie()
{
  if (isset($_COOKIE["makhzan"])) {
    $putative_gid = $_COOKIE["makhzan"];

    if (preg_match("/[^\w]/i", $putative_gid) || strlen($putative_gid) != 15) {
      if (!headers_sent()) {
        setcookie("makhzan", "", time() - 3600, "/");
      }
      return "0";
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

      $database->closeConnection();

      if (false || !$result || !$result["status"] || !$result["rows"]) {
        if (!headers_sent()) {
          setcookie("makhzan", "", time() - 3600, "/");
        }
        return "0";
      } else {
        return $putative_gid;
      }
    }
  }
  return "0";
}

function get_gid()
{
  function make($length, $chars)
  {
    $gid = "";
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; ++$i) {
      $gid .= $chars[random_int(0, $max)];
    }
    return $gid;
  }

  $gid = make(1, "hjklmnpqrstvwyz");
  $gid .= make(1, "aeiou");
  $gid .= make(1, "bcdfgklmnprstvwxyz");
  $gid .= make(1, "bcdfghjklmnpqrstvwyz");
  $gid .= make(1, "aeiou");
  $gid .= make(1, "bcdfgklmnprstvwxyz");
  $gid .= make(9, '0123456789abcdefghijklmnopqrstuvwxyz');

  return $gid;
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

function build_table_array($result)
{
  $res_array = [];

  while ($row = $result->fetch_assoc()) {
    foreach ($row as $key => $val) {
      if (
        in_array($key, [
          "max_prices",
          "popularity_factors",
          'popularity_history',
          'price_history',
          'quantity_sold_history',
          'from_quantity_sold_history',
        ])
      ) {
        $item[$key] = json_decode($val);
      } else {
        $item[$key] = $val;
      }
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
    foreach ($query_array as $query) {
      mysqli_query($connection, $query);
    }
  } else {
    echo "Error: Unable to mysqli_query(Sthis->connection, Squery). Possibly there was already a table with this game id, in which case just try clicking the New Game button again." .
      PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    echo $query;
    print_r($connection);
    print_r($query_array);
    exit();
  }

  return true;
}

function wipe_previous_game($connection)
{
  $message = "Right, ";
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
  $timeout = 3600 * 24 * 30;
  $log = [];
  $query = "SELECT * FROM games WHERE last_accessed < ?";

  if (!($stmt = $connection->prepare($query))) {
    return [
      "status" => false,
      "message" => "Not able: prepare query.",
      "error" => $connection->error,
    ];
  }

  $t = time() - $timeout;
  $stmt->bind_param("i", $t);

  if (!$stmt->execute()) {
    return [
      "status" => false,
      "message" => "Error in execution appeared.",
      "error" => $connection->error,
    ];
  }

  $result = $stmt->get_result();
  $stmt->close();

  if (!($res_arr = build_table_array($result))) {
    return [
      "status" => true,
      "data" => [],
      "message" => "There were no outdated games to delete.",
    ];
  }

  foreach ($res_arr as $row) {
    if (
      !($result = delete_row(
        $connection,
        "game_id",
        $row['game_id'],
        "games",
        "s"
      )) ||
      !$result["status"]
    ) {
      $log["Undeleted_rows"][] = $row['game_id'];
    }

    if (
      !($result = delete_table($connection, $row['game_id'] . "__inv")) ||
      !$result["status"]
    ) {
      $log["Undeleted_tables"][] = $row['game_id'];
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
  $identifying_column,
  $identifying_data,
  $table_name,
  $type_definition_string
) {
  $query =
    "UPDATE " .
    $table_name .
    " SET " .
    $change_column .
    " = ? WHERE " .
    $identifying_column .
    " = ?";

  if (!($stmt = $connection->prepare($query))) {
    return [
      "status" => false,
      "message" => "Unable to: prepare query.",
      "error" => $connection->error,
    ];
  }

  $stmt->bind_param($type_definition_string, $change_data, $identifying_data);

  if (!$stmt->execute()) {
    return [
      "status" => false,
      "message" => "Error in execution occurred.",
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
  $type_definition_string
) {
  $query = "SELECT * FROM " . $table_name . " WHERE " . $column . " = ?";

  if (!($stmt = $connection->prepare($query))) {
    return [
      "status" => false,
      "message" => "Wasn't able to prepare this query.",
      "error" => $connection->error,
    ];
  }

  $stmt->bind_param($type_definition_string, $identifier);

  if (!$stmt->execute()) {
    return [
      "status" => false,
      "message" => "An error in execution appeared.",
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

function delete_row(
  $connection,
  $column,
  $identifier,
  $table_name,
  $type_definition_string
) {
  $query = "DELETE FROM " . $table_name . " WHERE " . $column . " = ?";

  if (!($stmt = $connection->prepare($query))) {
    return [
      "status" => false,
      "message" => "Was not able to prepare this query.",
      "error" => $connection->error,
    ];
  }

  $stmt->bind_param($type_definition_string, $identifier);

  if (!$stmt->execute()) {
    return [
      "status" => false,
      "message" => "An error occurred in execution.",
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
      "message" => "Not able to prepare the query.",
      "error" => $connection->error,
    ];
  }

  if (!$stmt->execute()) {
    return [
      "status" => false,
      "message" => "There was an error in execution.",
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
