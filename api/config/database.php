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

    $max_prices_json = json_encode(["Low" => 1, "High" => 5]);
    $pop_factors_json = json_encode(["weather" => true, "love" => false]);

    $create_table_querystring =
      " (
      `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `rubicon` int(11) NOT NULL,
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
        "name" => "Grapes", //x5.0
        "rubicon" => 0,
        "max_prices" => ["Low" => 1, "High" => 5],
        "popularity_factors" => [
          "weather" => true,
          "love" => true,
        ],
      ],

      [
        "name" => "Fig", //x5.3
        "rubicon" => 0,
        "max_prices" => ["Low" => 3, "High" => 16],
        "popularity_factors" => [
          "politics" => true,
          "conformity" => true,
        ],
      ],
      [
        "name" => "Peach", //x5.75
        "rubicon" => 0,
        "max_prices" => ["Low" => 4, "High" => 23],
        "popularity_factors" => [
          "decadence" => true,
          "conformity" => true,
        ],
      ],
      [
        "name" => "Orange", //x5.9
        "rubicon" => 0,
        "max_prices" => ["Low" => 10, "High" => 59],
        "popularity_factors" => [
          "conformity" => true,
          "love" => true,
        ],
      ],
      [
        "name" => "Pistachio", //x6.1
        "rubicon" => 0,
        "max_prices" => ["Low" => 20, "High" => 122],
        "popularity_factors" => [
          "weather" => false,
          "politics" => true,
        ],
      ],
      [
        "name" => "Rare Melon", //x8
        "rubicon" => 1,
        "max_prices" => ["Low" => 30, "High" => 240],
        "popularity_factors" => [
          "politics" => true,
          "decadence" => true,
        ],
      ],
      [
        "name" => "Albino Almond", //x11
        "rubicon" => 2,
        "max_prices" => ["Low" => 60, "High" => 660],
        "popularity_factors" => [
          "conformity" => true,
          "weather" => true,
        ],
      ],
      [
        "name" => "Flammable Walnut", //x15
        "rubicon" => 3,
        "max_prices" => ["Low" => 100, "High" => 1500],
        "popularity_factors" => [
          "politics" => false,
          "conformity" => true,
        ],
      ],
      [
        "name" => "Enchanted Pomegranate", //x20
        "rubicon" => 4,
        "max_prices" => ["Low" => 150, "High" => 3000],
        "popularity_factors" => [
          "decadence" => true,
          "love" => true,
        ],
      ],
      [
        "name" => "The Perfect Date", //x25
        "rubicon" => 5,
        "max_prices" => ["Low" => 200, "High" => 5000],
        "popularity_factors" => [
          "love" => false,
          "weather" => true,
        ],
      ],
    ];

    $query_array = [];

    foreach ($seed_data as $seed_item) {
      $query_array[] =
        "INSERT INTO " .
        $table_name .
        " (`name`, `rubicon`, `selling_price`, `max_prices`, `popularity_factors`) VALUES
      ('" .
        $seed_item['name'] .
        "', " .
        $seed_item['rubicon'] .
        ", " .
        16 .
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
      "INSERT INTO games (`game_id`, `last_accessed`, `trend_calculates`, `money_stat`, `days_stat`, `level_record`) VALUES (?, ?, ?, ?, ?, ?)";

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

    $money_initial = 199;
    $days_initial = 0;
    $level_record_initial = json_encode([
      "round" => 0,
      "sublevel" => 0,
      "final_round" => 4,
    ]);

    $gid = $_SESSION["gid"];
    $timestamp = time();
    $stmt->bind_param(
      "sisiis",
      $gid,
      $timestamp,
      $trends,
      $money_initial,
      $days_initial,
      $level_record_initial
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
    $_SESSION['level_record'] = $level_record_initial;

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
