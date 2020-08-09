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
  public $connection;
  // $this->conn->exec("set names utf8");

  public function __construct()
  {
    if ($this->use_clear_db) {
      $this->username = "b3845dbeabd54b";
      $this->password = "c8ee23f4";
      $this->host = "eu-cdbr-west-03.cleardb.net";
      $this->db_name = "heroku_73f57e9b43b49b3";
    }
  }

  public function startNewGame()
  {
    include "../../utils/get_gid.php";
    include "../../utils/table_utils.php";

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

    if (isset($_SESSION["gid"]) && isset($_SESSION["inv_table_name"])) {
      wipe_previous_game($this->connection);
    }

    $_SESSION["gid"] = $gid;
    $_SESSION["inv_table_name"] = $_SESSION["gid"] . "__inv";
    $table_name = $_SESSION["inv_table_name"];

    // $create_table_querystring = " (
    //   `id` int(11) NOT NULL AUTO_INCREMENT NUMERIC(1, 10) PRIMARY KEY,
    //   `name` varchar(255) NOT NULL,
    //   `quantity` int(11) NOT NULL,
    //   `selling_price` int(11) NOT NULL,
    //   `total_sales` int(11) DEFAULT 0,
    //   `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
    // )";

    // $json = '{"name": "Jo", "12": 32}';
    // $day = 300;
    // $val = 50;
    // $arr = [$day => $val];
    // $json = json_encode($arr);

    //Make and populate Inventory table./////////////////////////////////////////////////

    $max_prices_json = json_encode(["Low" => 1, "Medium" => 2, "High" => 5]);
    $pop_factors_json = json_encode(["weather" => true, "love" => false]);

    $create_table_querystring =
      " (
      `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `name` varchar(100) NOT NULL,
      `quantity` int(11) DEFAULT 0,
      `selling_price` int(11) DEFAULT 0,
      `resilience` int(3) DEFAULT 50,
      `max_prices` json DEFAULT '" .
      $max_prices_json .
      "',
      `popularity_factors` json DEFAULT '" .
      $pop_factors_json .
      "',
      `popularity_history` json DEFAULT '{}',
      `price_history` json DEFAULT '{}',
      `quantity_sold_history` json DEFAULT '{}',
      `from_quantity_sold_history` json DEFAULT '{}'
    )";

    $query_array = [
      "INSERT INTO " .
      $table_name .
      " (`name`, `quantity`, `selling_price`, `resilience`, `max_prices`, `popularity_factors`) VALUES
      ('Melton-1', 50, 5, 20, '" .
      json_encode(["Low" => 1, "Medium" => 2, "High" => 5]) .
      "', '" .
      json_encode(["weather" => true, "love" => false]) .
      "')",

      "INSERT INTO " .
      $table_name .
      " (`name`, `quantity`, `selling_price`, `resilience`, `max_prices`, `popularity_factors`) VALUES
      ('Magga-2', 50, 5, 20, '" .
      json_encode(["Low" => 10, "Medium" => 20, "High" => 50]) .
      "', '" .
      json_encode(["weather" => true, "politics" => true]) .
      "')",

      "INSERT INTO " .
      $table_name .
      " (`name`, `quantity`, `selling_price`, `resilience`, `max_prices`, `popularity_factors`) VALUES
      ('Met Mit Motts 3', 50, 5, 20, '" .
      json_encode(["Low" => 100, "Medium" => 200, "High" => 500]) .
      "', '" .
      json_encode(["decadence" => true, "conformity" => false]) .
      "')",
    ];

    make_table(
      $table_name,
      $create_table_querystring,
      $this->connection,
      $query_array
    );

    //Add row to games table./////////////////////////////////////////////////
    $query =
      "INSERT INTO games (`game_id`, `last_accessed`, `trend_calculates`, `money_stat`, `days_stat`) VALUES (?, ?, ?, ?, ?)";

    if (!($stmt = $this->connection->prepare($query))) {
      // mysqli_close($this->connection);
      return [
        "status" => false,
        "message" => "Couldn't prepare query.",
        "error" => $this->connection->error,
      ];
    }

    $trends = json_encode([
      "weather" => random_int(1, 100),
      "love" => random_int(1, 100),
      "politics" => random_int(1, 100),
      "decadence" => random_int(1, 100),
      "conformity" => random_int(1, 100),
    ]);

    $money_initial = 0;
    $days_initial = 0;

    $gid = $_SESSION["gid"];
    $timestamp = time();
    $stmt->bind_param(
      "sisii",
      $gid,
      $timestamp,
      $trends,
      $money_initial,
      $days_initial
    );

    if (!$stmt->execute()) {
      // mysqli_close($this->connection);
      return [
        "status" => false,
        "message" => "An error in execution.",
        "error" => $this->connection->error,
      ];
    }

    $stmt->close();
    // $this->connection->close(); //mysqli_close($this->connection);

    $_SESSION["trend_calculates"] = $trends; ///////////////////////////////////success!
    $_SESSION["money_stat"] = $money_initial; //////////////////////////////////success!
    $_SESSION["days_stat"] = $days_initial; ////////////////////////////////////success!

    return [
      "status" => true,
      "message" => "Successfully started a new game.",
    ];
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
    $this->connection->close();
  }
}
?>
