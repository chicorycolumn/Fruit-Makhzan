<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

class Fruit
{
  private $conn;
  private $table_name;
  private $use_oop = true;

  public $id;
  public $name;
  public $quantity;
  public $selling_price;
  public $total_sales;
  public $created;

  public function __construct($dbwhole)
  {
    $this->conn = $dbwhole->connection;
    $this->table_name = $dbwhole->table_name;

    if (isset($_SESSION['table_name'])) {
      $this->table_name = $_SESSION['table_name'];
    } else {
      return "Error. No table is set.";
    }
  }

  function read()
  {
    $query = "SELECT * FROM " . $this->table_name;

    $stmt = $this->conn->prepare($query);

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
  }

  function read_single()
  {
    $query =
      "SELECT
            `id`, `name`, `quantity`, `selling_price`, `total_sales`, `created`
        FROM
            " .
      $this->table_name .
      " 
        WHERE id=?";

    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $this->id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
  }

  function create_self()
  {
    if ($this->does_entry_exist()) {
      return false;
    }

    $query =
      "INSERT INTO  " .
      $this->table_name .
      " ( `name`, `quantity`, `selling_price`) VALUES ('" .
      $this->name .
      "', '" .
      $this->quantity .
      "', '" .
      $this->selling_price .
      "')";

    $stmt = $this->conn->prepare($query);
    if ($stmt->execute()) {
      $this->id = $this->conn->lastInsertId();
      return true;
    }
    return false;
  }

  function update_self()
  {
    $query =
      "UPDATE " .
      $this->table_name .
      " SET name='" .
      $this->name .
      "', quantity='" .
      $this->quantity .
      "', selling_price='" .
      $this->selling_price .
      "'
                WHERE
                    id='" .
      $this->id .
      "'";

    $stmt = $this->conn->prepare($query);
    if ($stmt->execute()) {
      return $stmt;
    }
    return false;
  }

  function restock_self($new_quantity)
  {
    $query = "UPDATE " . $this->table_name . " SET quantity=? WHERE id=?";

    if ($stmt = $this->conn->prepare($query)) {
      $stmt->bind_param("ii", $new_quantity, $this->id);
      $stmt->execute();
      $result = $stmt->get_result();
      $stmt->close();
      return true;
    }
    return false;
  }

  function delete_self()
  {
    $query = "DELETE FROM " . $this->table_name . " WHERE id=?";

    $stmt = $this->conn->prepare($query);

    $stmt->bind_param("i", $this->id);

    if ($stmt->execute()) {
      $bool = true;
    } else {
      $bool = false;
    }

    $stmt->close();
    return $bool;
  }

  function does_entry_exist()
  {
    $query =
      "SELECT *
            FROM
                " .
      $this->table_name .
      " 
            WHERE
                name='" .
      $this->name .
      "'";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }
}
