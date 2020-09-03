<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

class Fruit
{
  private $conn;
  private $inv_table_name;

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
    $query = "SELECT * FROM " . $table_name . " ORDER BY rubicon DESC";

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
    $type_definition_string,
    $get_full
  ) {
    if (substr($table_name, -3) == "inv" && !$get_full) {
      $columns =
        "`rubicon`, `name`, `quantity`, `selling_price`, `max_prices`, `popularity_factors`";
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

    $stmt->bind_param($type_definition_string, $identifying_data);

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

  function create_self(
    $table_name,
    $name,
    $popularity_factors,
    $max_prices,
    $rubicon
  ) {
    if (!$this->is_fruit_in_table($table_name, $name)) {
      return [
        "status" => false,
        "message" => "Error in is_fruit_in_table for name " . $name,
        "error" => $this->conn->error,
      ];
    }

    if ($this->is_fruit_in_table($table_name, $name)["status"]) {
      return [
        "status" => false,
        "message" =>
          "Could not create. A fruit of name " . $name . " already exists.",
        "error" => $this->conn->error,
      ];
    }

    $query =
      "INSERT INTO  " .
      $table_name .
      " ( `name`, `popularity_factors`, `max_prices`, `rubicon` ) VALUES (?, ?, ?, ?)";

    if (!($stmt = $this->conn->prepare($query))) {
      return [
        "status" => false,
        "message" => "Could not prepare query with name " . $name,
        "error" => $this->conn->error,
      ];
    }

    $stmt->bind_param(
      "sssd",
      $name,
      $popularity_factors,
      $max_prices,
      $rubicon
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

  function is_fruit_in_table($table_name, $name)
  {
    $query = "SELECT * FROM " . $table_name . " WHERE name=?";

    if (!($stmt = $this->conn->prepare($query))) {
      return false;
    }

    $stmt->bind_param("s", $name);

    if (!$stmt->execute()) {
      return [
        "status" => false,
        "message" =>
          "Error in execution at is_fruit_in_table for name " . $name,
        "error" => $this->conn->error,
      ];
    }

    $result = $stmt->get_result();
    $stmt->close();

    if (!$result->num_rows) {
      return [
        "status" => false,
        "message" => "Entry with name " . $name . " does not exist.",
        "error" => $this->conn->error,
      ];
    }

    return ["status" => true, "message" => "Entry exists."];
  }

  function restock_self($name, $table_name, $increment)
  {
    $query = "UPDATE " . $table_name . " SET quantity=quantity+? WHERE name=?";

    if (!($stmt = $this->conn->prepare($query))) {
      return [
        "status" => false,
        "message" => "Could not prepare query.",
        "error" => $this->conn->error,
      ];
    }

    $stmt->bind_param("is", $increment, $name);

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

  function delete_self($name, $table_name)
  {
    $query = "DELETE FROM " . $table_name . " WHERE name=?";

    if (!($stmt = $this->conn->prepare($query))) {
      return [
        "status" => false,
        "message" => "Could not prepare query.",
        "error" => $this->conn->error,
      ];
    }

    $stmt->bind_param("s", $name);

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
    $json_data_object,
    $json_data_object_name
  ) {
    if ($json_data_object_name == "overall_sales_history") {
      if (
        !($result = $this->read_single(
          $table_name,
          $identifying_column,
          $identifying_data,
          "s",
          false
        ))
      ) {
        return [
          "status" => false,
          "message" => "Error when calling Sfruit->read_single.",
          "error" => $this->conn->error,
        ];
      }

      if (!$result["status"]) {
        return $result;
      }

      if (!$result['data']->num_rows) {
        return [
          "status" => false,
          "message" =>
            "There are no rows from reading the db. The identifying data (" .
            $identifying_data .
            ") at identifying column (" .
            $identifying_column .
            ") does not correspond to anything in the table (" .
            $table_name .
            ").",
          "error" => $this->conn->error,
        ];
      }

      if (!($result_arr = build_table_array($result["data"]))) {
        return [
          "status" => false,
          "message" => "Error in build_table_array.",
          "error" => $this->conn->error,
        ];
      }

      $json = json_decode($result_arr[0][$json_data_object_name]);
      $json = $json_data_object;

      if (!isset($update_data)) {
        $update_data = new stdClass();
      }

      $update_data->$json_data_object_name = json_encode($json);

      if (
        !($result = $this->update_self(
          $table_name,
          $identifying_column,
          $identifying_data,
          "ss",
          $update_data
        ))
      ) {
        return [
          "status" => false,
          "message" => "Error when calling Sfruit->read_single.",
          "error" => $this->conn->error,
        ];
      }

      if (!$result["status"]) {
        return $result;
      }
    }

    return ["status" => true, "message" => "Successfully updated json!"];
  }

  function update_self(
    $table_name,
    $identifying_column,
    $identifying_data,
    $type_definition_string,
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

    if (count($update_values) == 1) {
      $stmt->bind_param($type_definition_string, $update_values);
    } elseif (count($update_values) > 1) {
      $stmt->bind_param($type_definition_string, ...$update_values);
    } else {
      return [
        "status" => false,
        "message" => "Nothing in Supdate_values.",
        "error" => $this->conn->error,
      ];
    }

    if (!$stmt->execute()) {
      return [
        "status" => false,
        "message" => "Error in execution. " . $query,
        "update_values" => $update_values,
        "error" => $this->conn->error,
      ];
    }

    $result = $stmt->get_result();
    $stmt->close();
    return ["status" => true, "message" => "Successfully updated!"];
  }

  function set_to_default(
    $table_name,
    $column_to_change,
    $new_value,
    $data_type
  ) {
    $query =
      "UPDATE " . $table_name . " SET " . $column_to_change . " =DEFAULT";

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

    $type_definition_string = "";
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

      $type_definition_string .= $data_type;

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

    $stmt->bind_param($type_definition_string, ...$new_data_set);

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
