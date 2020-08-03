<?php

function cleanUpDB($connection)
{
  $query = "SELECT * FROM Games WHERE Last_Accessed < ?";

  if ($stmt = $connection->prepare($query)) {
    $t = time() - 3600; /////////////// 3600 * 24 * 30
    $stmt->bind_param("i", $t);
    if ($stmt->execute()) {
      $result = $stmt->get_result();
      $stmt->close();
      if ($result->num_rows) {
        $gid_arr = [];

        while ($row = $result->fetch_assoc()) {
          array_push($gid_arr, $row["Game_ID"]);
        }

        foreach ($gid_arr as $gid) {
          $result = delete_row($connection, "Game_ID", $gid, "Games");

          if (!$result) {
            return [
              "status" => false,
              "message" => "Error in delete_row.",
              "error" => $connection->error,
            ];
          }

          if (!$result["status"]) {
            return $result;
          }

          $result = delete_table($connection, $gid . "__INV");

          if (!$result) {
            return [
              "status" => false,
              "message" => "Error in delete_table INV.",
              "error" => $connection->error,
            ];
          }

          if (!$result["status"]) {
            return $result;
          }

          $result = delete_table($connection, $gid . "__NST");

          if (!$result) {
            return [
              "status" => false,
              "message" => "Error in delete_table NST.",
              "error" => $connection->error,
            ];
          }

          if (!$result["status"]) {
            return $result;
          }

          return [
            "status" => true,
            "message" => "Outdated games were deleted.",
          ];
        }
      }
      return [
        "status" => true,
        "data" => [],
        "message" => "There were no outdated games to delete.",
      ];
    }
    return [
      "status" => false,
      "message" => "Error in execution.",
      "error" => $connection->error,
    ];
  }
  return [
    "status" => false,
    "message" => "Could not prepare query.",
    "error" => $connection->error,
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

  if ($stmt = $connection->prepare($query)) {
    $stmt->bind_param($acronym, $change_data, $id_data);
    if ($stmt->execute()) {
      $result = $stmt->get_result();
      $stmt->close();
      return ["status" => true, "message" => "Successfully updated!"];
    } else {
      return [
        "status" => false,
        "message" => "Error in execution.",
        "error" => $connection->error,
      ];
    }
  }
  return [
    "status" => false,
    "message" => "Could not prepare query.",
    "error" => $connection->error,
  ];
}

function check_row_exists(
  $connection,
  $column,
  $identifier,
  $table_name,
  $column_to_return
) {
  $query = "SELECT * FROM " . $table_name . " WHERE " . $column . " = ?";

  if ($stmt = $connection->prepare($query)) {
    $stmt->bind_param("s", $identifier);
    if ($stmt->execute()) {
      $result = $stmt->get_result();
      $stmt->close();
      if ($result->num_rows) {
        if ($column_to_return) {
          $row = $result->fetch_assoc();
          return [
            "status" => true,
            "message" => "Successfully queried.",
            "rows" => $result->num_rows,
            "data" => $row[$column_to_return],
          ];
        } else {
          return [
            "status" => true,
            "message" => "Successfully queried.",
            "rows" => $result->num_rows,
          ];
        }
      }
      return [
        "status" => true,
        "message" => "Successfully queried.",
        "rows" => $result->num_rows,
      ];
    } else {
      return [
        "status" => false,
        "message" => "An error in execution.",
        "error" => $connection->error,
      ];
    }
  } else {
    return [
      "status" => false,
      "message" => "Couldn't prepare this query.",
      "error" => $connection->error,
    ];
  }
}

function delete_row($connection, $column, $identifier, $table_name)
{
  $query = "DELETE FROM " . $table_name . " WHERE " . $column . " = ?";

  if ($stmt = $connection->prepare($query)) {
    $stmt->bind_param("s", $identifier);
    if ($stmt->execute()) {
      $stmt->close(); //////////////
      return [
        "status" => true,
        "message" =>
          "Successfully deleted row " .
          $identifier .
          " from table " .
          $table_name,
      ];
    } else {
      return [
        "status" => false,
        "message" => "An error in execution.",
        "error" => $connection->error,
      ];
    }
  } else {
    return [
      "status" => false,
      "message" => "Couldn't prepare this query.",
      "error" => $connection->error,
    ];
  }
}

function delete_table($connection, $table_name)
{
  $query = "DROP TABLE IF EXISTS " . $table_name;

  if ($stmt = $connection->prepare($query)) {
    if ($stmt->execute()) {
      return [
        "status" => true,
        "message" => "Successfully deleted table " . $table_name,
      ];
    } else {
      return [
        "status" => false,
        "message" => "An error in execution.",
        "error" => $connection->error,
      ];
    }
  } else {
    return [
      "status" => false,
      "message" => "Couldn't prepare the query.",
      "error" => $connection->error,
    ];
  }
}

?>
