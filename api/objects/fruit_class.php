<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

class Fruit
{
  private $conn;
  private $inv_table_name;
  private $nst_table_name;

  public $id;
  public $name;
  public $quantity;
  public $selling_price;
  public $total_sales;
  public $created;

  public function __construct($db)
  {
    $this->conn = $db;

    // foreach (["inv_table_name", "nst_table_name"] as $table_name) {
    //   if (isset($_SESSION[$table_name])) {
    //     $this->$table_name = $_SESSION[$table_name];
    //   } else {
    // echo "Error. No inv_table is set.";
    // exit();
    //   }
    // }

    if (isset($_SESSION["inv_table_name"])) {
      $this->inv_table_name = $_SESSION["inv_table_name"];
    } else {
      echo "Error. No inv_table is set.";
      exit();
    }

    if (isset($_SESSION["nst_table_name"])) {
      $this->nst_table_name = $_SESSION["nst_table_name"];
    } else {
      echo "Error. No inv_table is set.";
      exit();
    }
  }

  function read($table_suffix)
  {
    $table_name = $table_suffix . "_table_name";

    $query = "SELECT * FROM " . $this->$table_name . " ORDER BY id DESC";

    if ($stmt = $this->conn->prepare($query)) {
      if ($stmt->execute()) {
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
      } else {
        return [
          "status" => false,
          "message" => "Error in execution.",
        ];
      }
    } else {
      return [
        "status" => false,
        "message" => "Could not prepare query.",
      ];
    }
  }

  function read_single($table_suffix)
  {
    $table_name = $table_suffix . "_table_name";
    $query = "SELECT * FROM " . $this->$table_name . " WHERE name=?";

    if ($stmt = $this->conn->prepare($query)) {
      $stmt->bind_param("s", $this->name);
      if ($stmt->execute()) {
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
      } else {
        return [
          "status" => false,
          "message" => "Error in execution.",
        ];
      }
    } else {
      return [
        "status" => false,
        "message" => "Could not prepare query.",
      ];
    }
  }

  function create_self($table_suffix)
  {
    $table_name = $table_suffix . "_table_name";
    if ($this->does_entry_exist($table_suffix)["status"]) {
      return [
        "status" => false,
        "message" => "Could not create. A fruit of that name already exists.",
      ];
    } elseif ($this->does_entry_exist($table_suffix)["status"] == false) {
      $query =
        "INSERT INTO  " .
        $this->$table_name .
        " ( `name`, `quantity`, `selling_price`) VALUES (?, ?, ?)";

      if ($stmt = $this->conn->prepare($query)) {
        $stmt->bind_param(
          "sii",
          $this->name,
          $this->quantity,
          $this->selling_price
        );
        if ($stmt->execute()) {
          return [
            "status" => true,
            "message" => "Successfully created fruit!",
          ];
        } else {
          return [
            "status" => false,
            "message" => "Error in execution.",
          ];
        }
      } else {
        return [
          "status" => false,
          "message" => "Could not prepare query.",
        ];
      }
    } else {
      return [
        "status" => false,
        "message" => "Error in does_entry_exist.",
      ];
    }
  }

  function does_entry_exist($table_suffix)
  {
    $table_name = $table_suffix . "_table_name";
    $query = "SELECT * FROM " . $this->$table_name . " WHERE name=?";

    if ($stmt = $this->conn->prepare($query)) {
      $stmt->bind_param("s", $this->name);
      if ($stmt->execute()) {
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows) {
          return ["status" => true, "message" => "Entry exists."];
        } else {
          return ["status" => false, "message" => "Entry does not exist."];
        }
      }
    }
    return false;
  }

  function restock_self($table_suffix, $new_quantity)
  {
    $table_name = $table_suffix . "_table_name";
    $query = "UPDATE " . $this->$table_name . " SET quantity=? WHERE name=?";

    if ($stmt = $this->conn->prepare($query)) {
      $stmt->bind_param("is", $new_quantity, $this->name);
      if ($stmt->execute()) {
        $result = $stmt->get_result();
        $stmt->close();
        return ["status" => true, "message" => "Successfully restocked!"];
      } else {
        $response = [
          "status" => false,
          "message" => "Error in execution.",
        ];
      }
    }
    return ["status" => false, "message" => "Could not prepare query."];
  }

  function delete_self($table_suffix)
  {
    $table_name = $table_suffix . "_table_name";
    $query = "DELETE FROM " . $this->$table_name . " WHERE id=?";

    if ($stmt = $this->conn->prepare($query)) {
      $stmt->bind_param("i", $this->id);

      if ($stmt->execute()) {
        $response = [
          "status" => true,
          "message" => "Successfully deleted!",
        ];
      } else {
        $response = [
          "status" => false,
          "message" => "Error in execution.",
        ];
      }

      $stmt->close();
      return $response;
    } else {
      return ["status" => false, "message" => "Could not prepare query."];
    }
  }

  function update_self($table_suffix)
  {
    // $table_name = $table_suffix . "_table_name";
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

    // $stmt = $this->conn->prepare($query);
    // if ($stmt->execute()) {
    //   return $stmt;
    // }

    // return false;
  }
}
