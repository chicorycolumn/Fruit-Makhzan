<?php

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

class Database
{
  private $use_clear_db = 0;

  private $username = "root";
  private $password = "";
  private $host = "localhost";
  private $db_name = "fruit_makhzan_db";
  public $inv_table_name;
  public $nst_table_name;
  public $connection;
  // $this->conn->exec("set names utf8");

  public function __construct()
  {
    if ($this->use_clear_db) {
      $this->username = "b4709ad1452782";
      $this->password = "7d6b0f7d";
      $this->host = "us-cdbr-east-02.cleardb.com";
      $this->db_name = "heroku_cb0feae1098e18e";
    }
  }

  public function startNewGame()
  {
    include "../../utils/get_gid.php";
    include "../../utils/make_table.php";

    $_SESSION["gid"] = $gid;
    $_SESSION["inv_table_name"] = $_SESSION["gid"] . "__INV";
    $_SESSION["nst_table_name"] = $_SESSION["gid"] . "__NST";

    // $this->inv_table_name = $_SESSION["inv_table_name"];
    // $this->nst_table_name = $_SESSION["nst_table_name"];

    $this->connection = mysqli_connect(
      $this->host,
      $this->username,
      $this->password,
      $this->db_name
    );

    if (!$this->connection) {
      echo "Error: Unable to connect to MySQL." . PHP_EOL;
      echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
      echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
      exit();
    }

    $table_name = $_SESSION["inv_table_name"];
    $create_table_querystring = " (
      `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `name` varchar(255) NOT NULL,
      `quantity` int(11) NOT NULL,
      `selling_price` int(11) NOT NULL,
      `total_sales` int(11) DEFAULT 0,
      `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
    )";
    $connection = $this->connection;
    $query_array = [
      "INSERT INTO " .
      $table_name .
      " (`name`, `quantity`, `selling_price`, `total_sales`) VALUES
      ('Morangines', 50, 5, 20)",

      "INSERT INTO " .
      $table_name .
      " (`name`, `quantity`, `selling_price`) VALUES
      ('Miwiwoos', 50, 5)",

      "INSERT INTO " .
      $table_name .
      " (`name`, `quantity`, `selling_price`) VALUES
      ('Misty Vistas', 50, 5)",

      "INSERT INTO " .
      $table_name .
      " (`name`, `quantity`, `selling_price`) VALUES
      ('My Old Man The Mango', 80, 4)",

      "INSERT INTO " .
      $table_name .
      " (`name`, `quantity`, `selling_price`) VALUES
      ('Moloko', 80, 4)",

      "INSERT INTO " .
      $table_name .
      " (`name`, `quantity`, `selling_price`) VALUES
      ('Manchurianos', 200, 100)",

      "INSERT INTO " .
      $table_name .
      " (`name`, `quantity`, `selling_price`) VALUES
      ('Matey-wateys', 30, 10)",
    ];

    make_table(
      $table_name,
      $create_table_querystring,
      $connection,
      $query_array
    );

    $table_name = $_SESSION["nst_table_name"];
    $create_table_querystring = " (
      `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `name` varchar(255) NOT NULL,
      `stock_price` int(11) NOT NULL,
      `durability` int(11) NOT NULL,
      `popularity` int(11) DEFAULT 0
    )";
    $connection = $this->connection;
    $query_array = [
      "INSERT INTO " .
      $table_name .
      " (`name`, `stock_price`, `popularity`, `durability` ) VALUES
      ('Funkalites', 5, 3, 1)",

      "INSERT INTO " .
      $table_name .
      " (`name`, `stock_price`, `popularity`, `durability` ) VALUES
      ('Froobs', 10, 4, 2)",

      "INSERT INTO " .
      $table_name .
      " (`name`, `stock_price`, `popularity`, `durability` ) VALUES
      ('My Old Man The Mango', 1, 5, 3)",

      "INSERT INTO " .
      $table_name .
      " (`name`, `stock_price`, `popularity`, `durability` ) VALUES
      ('Moloko', 1, 5, 4)",

      // "INSERT INTO " .
      // $table_name .
      // " (`name`, `stock_price`, `popularity`, `durability` ) VALUES
      // ('Frangipanis', 1, 5, 5)",

      // "INSERT INTO " .
      // $table_name .
      // " (`name`, `stock_price`, `popularity`, `durability` ) VALUES
      // ('Hunkalites', 5, 3, 6)",

      // "INSERT INTO " .
      // $table_name .
      // " (`name`, `stock_price`, `popularity`, `durability` ) VALUES
      // ('Hoobs', 10, 4, 7)",

      // "INSERT INTO " .
      // $table_name .
      // " (`name`, `stock_price`, `popularity`, `durability` ) VALUES
      // ('Hye Old Man The Mango', 1, 5, 8)",

      // "INSERT INTO " .
      // $table_name .
      // " (`name`, `stock_price`, `popularity`, `durability` ) VALUES
      // ('Holoko', 1, 5, 9)",

      // "INSERT INTO " .
      // $table_name .
      // " (`name`, `stock_price`, `popularity`, `durability` ) VALUES
      // ('Hangipanis', 1, 5, 10)",
    ];

    make_table(
      $table_name,
      $create_table_querystring,
      $connection,
      $query_array
    );
    mysqli_close($connection);
    return true;
  }

  public function getConnection()
  {
    $this->connection = mysqli_connect(
      $this->host,
      $this->username,
      $this->password,
      $this->db_name
    );

    if (!$this->connection) {
      echo "Error: Unable to connect to MySQL." . PHP_EOL;
      echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
      echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
      exit();
    }

    return $this->connection;
  }

  public function closeConnection()
  {
    mysqli_close($this->connection);
  }
}
?>
