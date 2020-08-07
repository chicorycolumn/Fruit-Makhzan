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

  function read($table_name)
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
    $acronym
  ) {
    $query =
      "SELECT * FROM " . $table_name . " WHERE " . $identifying_column . "=?";

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

  function restock_self($table_name, $new_quantity)
  {
    $query = "UPDATE " . $table_name . " SET quantity=? WHERE name=?";

    if (!($stmt = $this->conn->prepare($query))) {
      return [
        "status" => false,
        "message" => "Could not prepare query.",
        "error" => $this->conn->error,
      ];
    }

    $stmt->bind_param("is", $new_quantity, $this->name);

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

  function update_self($table_suffix)
  {
    // $query =
    //   "UPDATE " .
    //   $this->$table_name .
    //   " SET name='" .
    //   $this->name .
    //   "', quantity='" .
    //   $this->quantity .
    //   "', selling_price='" .
    //   $this->selling_price .
    //   "'
    //             WHERE
    //                 id='" .
    //   $this->id .
    //   "'";
  }
}
