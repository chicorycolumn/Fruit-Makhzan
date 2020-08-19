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

    //                                                            Make and populate Inventory table.

    $max_prices_json = json_encode(["Low" => 1, "Medium" => 2, "High" => 5]);
    $pop_factors_json = json_encode(["weather" => true, "love" => false]);

    $create_table_querystring =
      " (
      `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `name` varchar(100) NOT NULL,
      `quantity` int(11) DEFAULT 0,
      `selling_price` int(11) DEFAULT 0,
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

    $seed_data = [
      [
        "name" => "The Perfect Date",
        "max_prices" => ["Low" => 200, "Medium" => 1000, "High" => 5000],
        "popularity_factors" => [
          "love" => false,
          "weather" => true,
        ],
      ],
      [
        "name" => "Enchanted Pomegranate",
        "max_prices" => ["Low" => 150, "Medium" => 600, "High" => 2500],
        "popularity_factors" => [
          "decadence" => true,
          "love" => true,
        ],
      ],
      [
        "name" => "Flammable Walnut",
        "max_prices" => ["Low" => 100, "Medium" => 300, "High" => 1100],
        "popularity_factors" => [
          "politics" => false,
          "conformity" => true,
        ],
      ],
      [
        "name" => "Albino Almond",
        "max_prices" => ["Low" => 60, "Medium" => 150, "High" => 500],
        "popularity_factors" => [
          "conformity" => true,
          "weather" => true,
        ],
      ],
      [
        "name" => "Rare Melon",
        "max_prices" => ["Low" => 30, "Medium" => 70, "High" => 200],
        "popularity_factors" => [
          "politics" => true,
          "decadence" => true,
        ],
      ],
      [
        "name" => "Pistachio",
        "max_prices" => ["Low" => 20, "Medium" => 40, "High" => 100],
        "popularity_factors" => [
          "weather" => false,
          "politics" => true,
        ],
      ],
      [
        "name" => "Orange",
        "max_prices" => ["Low" => 10, "Medium" => 20, "High" => 50],
        "popularity_factors" => [
          "conformity" => true,
          "love" => true,
        ],
      ],
      [
        "name" => "Peach",
        "max_prices" => ["Low" => 4, "Medium" => 8, "High" => 20],
        "popularity_factors" => [
          "decadence" => true,
          "conformity" => true,
        ],
      ],
      [
        "name" => "Fig",
        "max_prices" => ["Low" => 2, "Medium" => 4, "High" => 10],
        "popularity_factors" => [
          "politics" => true,
          "conformity" => true,
        ],
      ],
      [
        "name" => "Grapes",
        "max_prices" => ["Low" => 1, "Medium" => 2, "High" => 5],
        "popularity_factors" => [
          "weather" => true,
          "love" => true,
        ],
      ],
    ];

    $query_array = [];

    foreach ($seed_data as $seed_item) {
      $query_array[] =
        "INSERT INTO " .
        $table_name .
        " (`name`, `selling_price`, `quantity`, `max_prices`, `popularity_factors`) VALUES
      ('" .
        $seed_item['name'] .
        "', " .
        random_int(1, 5) .
        ", " .
        random_int(1, 100) .
        ", '" .
        json_encode($seed_item['max_prices']) .
        "', '" .
        json_encode($seed_item['popularity_factors']) .
        "')";
    }

    make_table(
      $table_name,
      $create_table_querystring,
      $this->connection,
      $query_array
    );

    //                                                            Add row to games table.
    $query =
      "INSERT INTO games (`game_id`, `last_accessed`, `trend_calculates`, `money_stat`, `days_stat`) VALUES (?, ?, ?, ?, ?)";

    if (!($stmt = $this->connection->prepare($query))) {
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
      "conformity" => random_int(1, 100),
      "decadence" => random_int(1, 100),
      "conformity_history" => "ss",
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
      return [
        "status" => false,
        "message" => "An error in execution.",
        "error" => $this->connection->error,
      ];
    }

    $stmt->close();

    $_SESSION["trend_calculates"] = $trends;
    $_SESSION["money_stat"] = $money_initial;
    $_SESSION["days_stat"] = $days_initial;

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
