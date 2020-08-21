<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

class Fruit
{
  private $conn;
  private $inv_table_name;

  public $id;
  public $name;
  public $quantity;
  public $selling_price;
  public $total_sales;
  public $created;

  public function __construct($db)
  {
    $this->conn = $db;
  }

  function read($table_name, $get_full)
  {
    $query = "SELECT * FROM " . $table_name . " ORDER BY id DESC";

    if (!($stmt = $this->conn->prepare($query))) {
      return [
        "status" => false,
        "message" => "Could not prepare query.",
        "error" => $this->conn->error,
      ];
    }

    if (!$stmt->execute()) {
      return [
        "status" => false,
        "message" => "Error in execution.",
        "error" => $this->conn->error,
      ];
    }

    $result = $stmt->get_result();
    $stmt->close();

    return [
      "status" => true,
      "data" => $result,
    ];
  }

  function read_single(
    $table_name,
    $identifying_column,
    $identifying_data,
    $acronym,
    $get_full
  ) {
    if (substr($table_name, -3) == "inv" && !$get_full) {
      $columns =
        "`id`, `name`, `quantity`, `selling_price`, `max_prices`, `popularity_factors`";
    } else {
      $columns = "*";
    }

    $query =
      "SELECT " .
      $columns .
      " FROM `" .
      $table_name .
      "` WHERE " .
      $identifying_column .
      "=?";

    if (!($stmt = $this->conn->prepare($query))) {
      return [
        "status" => false,
        "message" => "Could not prepare query.",
        "error" => $this->conn->error,
      ];
    }

    $stmt->bind_param($acronym, $identifying_data);

    if (!$stmt->execute()) {
      return [
        "status" => false,
        "message" => "Error in execution.",
        "error" => $this->conn->error,
      ];
    }

    $result = $stmt->get_result();
    $stmt->close();

    return [
      "status" => true,
      "data" => $result,
    ];
  }

  function create_self($table_name)
  {
    if (!$this->is_fruit_in_table($table_name)) {
      return [
        "status" => false,
        "message" => "Error in is_fruit_in_table.",
        "error" => $this->conn->error,
      ];
    }

    if ($this->is_fruit_in_table($table_name)["status"]) {
      return [
        "status" => false,
        "message" => "Could not create. A fruit of that name already exists.",
        "error" => $this->conn->error,
      ];
    }

    $query =
      "INSERT INTO  " .
      $table_name .
      " ( `name`, `quantity`, `selling_price`) VALUES (?, ?, ?)";

    if (!($stmt = $this->conn->prepare($query))) {
      return [
        "status" => false,
        "message" => "Could not prepare query.",
        "error" => $this->conn->error,
      ];
    }

    $stmt->bind_param(
      "sii",
      $this->name,
      $this->quantity,
      $this->selling_price
    );

    if (!$stmt->execute()) {
      return [
        "status" => false,
        "message" => "Error in execution.",
        "error" => $this->conn->error,
      ];
    }

    $stmt->close();
    return [
      "status" => true,
      "message" => "Successfully created fruit!",
    ];
  }

  function is_fruit_in_table($table_name)
  {
    $query = "SELECT * FROM " . $table_name . " WHERE name=?";

    if (!($stmt = $this->conn->prepare($query))) {
      return false;
    }

    $stmt->bind_param("s", $this->name);

    if (!$stmt->execute()) {
      return [
        "status" => false,
        "message" => "Error in execution.",
        "error" => $this->conn->error,
      ];
    }

    $result = $stmt->get_result();
    $stmt->close();

    if (!$result->num_rows) {
      return [
        "status" => false,
        "message" => "Entry does not exist.",
        "error" => $this->conn->error,
      ];
    }

    return ["status" => true, "message" => "Entry exists."];
  }

  function restock_self($table_name, $increment)
  {
    $query = "UPDATE " . $table_name . " SET quantity=quantity+? WHERE name=?";

    if (!($stmt = $this->conn->prepare($query))) {
      return [
        "status" => false,
        "message" => "Could not prepare query.",
        "error" => $this->conn->error,
      ];
    }

    $stmt->bind_param("is", $increment, $this->name);

    if (!$stmt->execute()) {
      return [
        "status" => false,
        "message" => "Error in execution.",
        "error" => $this->conn->error,
      ];
    }

    $result = $stmt->get_result();
    $stmt->close();
    return ["status" => true, "message" => "Successfully restocked!"];
  }

  function delete_self($table_name)
  {
    $query = "DELETE FROM " . $table_name . " WHERE id=?";

    if (!($stmt = $this->conn->prepare($query))) {
      return [
        "status" => false,
        "message" => "Could not prepare query.",
        "error" => $this->conn->error,
      ];
    }

    $stmt->bind_param("i", $this->id);

    if (!$stmt->execute()) {
      return [
        "status" => false,
        "message" => "Error in execution.",
        "error" => $this->conn->error,
      ];
    }

    $stmt->close();
    return [
      "status" => true,
      "message" => "Successfully deleted!",
    ];
  }

  function update_json(
    $table_name,
    $identifying_column,
    $identifying_data,
    $json_data_object
  ) {
    foreach ($json_data_object as $json_column => $json_data) {
      if ($json_column == "overall_sales_history") {
        $day = array_key_last($json_data);

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
          $day .
          "', ?) " .
          "WHERE " .
          $identifying_column .
          " =?";

        if (!($stmt = $this->conn->prepare($query))) {
          return [
            "status" => false,
            "message" =>
              "Could not prepare query of column `" .
              $json_column .
              "` on table `" .
              $table_name .
              "`.",
            "error" => $this->conn->error,
          ];
        }

        $acronym = "ss";
        $datum =
          "['profit':" .
          $json_data[$day]['profit'] .
          ",'costs':" .
          $json_data[$day]['costs'] .
          "]";

        $stmt->bind_param($acronym, $datum, $identifying_data);

        if (!$stmt->execute()) {
          return [
            "status" => false,
            "Error in execution with column `" .
            $json_column .
            "` on table `" .
            $table_name .
            "`.",
            "error" => $this->conn->error,
          ];
        }
        // $result = $stmt->get_result();
        $stmt->close();
      }
    }
    return ["status" => true, "message" => "Successfully updated json!"];
  }

  function update_self(
    $table_name,
    $identifying_column,
    $identifying_data,
    $acronym,
    $update_data
  ) {
    $update_string = "";
    $update_values = [];

    foreach ($update_data as $key => $val) {
      $update_string .= $key . "=?, ";
      $update_values[] = $val;
    }

    $update_values[] = $identifying_data;

    $update_string = substr($update_string, 0, -2);

    $query =
      "UPDATE " .
      $table_name .
      " SET " .
      $update_string .
      " WHERE " .
      $identifying_column .
      "=?";

    if (!($stmt = $this->conn->prepare($query))) {
      return [
        "status" => false,
        "message" => "Could not prepare query.",
        "error" => $this->conn->error,
      ];
    }

    $stmt->bind_param($acronym, ...$update_values);

    if (!$stmt->execute()) {
      return [
        "status" => false,
        "message" => "Error in execution.",
        "error" => $this->conn->error,
      ];
    }

    $result = $stmt->get_result();
    $stmt->close();
    return ["status" => true, "message" => "Successfully updated!"];
  }

  function update_multiple(
    $table_name,
    $column_to_change,
    $identifying_column,
    $operation,
    $data_obj,
    $new_data_key,
    $data_type
  ) {
    $identifying_data_set = array_keys($data_obj);

    $query =
      "UPDATE " .
      $table_name .
      "
      SET " .
      $column_to_change .
      " = CASE " .
      $identifying_column;

    $acronym = "";
    $new_data_set = [];

    foreach ($identifying_data_set as $identifying_data) {
      if ($operation == "decrement") {
        $query .=
          " WHEN '" .
          $identifying_data .
          "' THEN " .
          $column_to_change .
          " - ?";
      } elseif ($operation == "increment") {
        $query .=
          " WHEN '" .
          $identifying_data .
          "' THEN " .
          $column_to_change .
          " + ?";
      } elseif ($operation == "replace") {
        $query .= " WHEN '" . $identifying_data . "' THEN ?";
      }

      $acronym .= $data_type;

      $new_data_set[] = $data_obj[$identifying_data][$new_data_key];
    }

    $query .=
      " ELSE " .
      $column_to_change .
      " 
    END
    WHERE " .
      $identifying_column .
      " IN('" .
      implode("', '", $identifying_data_set) .
      "')";

    if (!($stmt = $this->conn->prepare($query))) {
      return [
        "status" => false,
        "message" => "Could not prepare query.",
        "error" => $this->conn->error,
      ];
    }

    $stmt->bind_param($acronym, ...$new_data_set);

    if (!$stmt->execute()) {
      return [
        "status" => false,
        "message" => "Error in execution.",
        "error" => $this->conn->error,
      ];
    }

    $result = $stmt->get_result();
    $stmt->close();
    return ["status" => true, "message" => "Successfully updated multiple!"];
  }
}
