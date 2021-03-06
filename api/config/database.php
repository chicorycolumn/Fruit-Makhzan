<?php

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

class Database
{
  private $username = "root";
  private $password = "";
  private $host = "localhost";
  private $db_name = "fruit_makhzan_db";
  public $inv_table_name;
  public $connection;

  public function __construct()
  {
    $db_url = null;

    if (array_key_exists("CLEARDB_DATABASE_URL", getenv())) {
      $db_url = getenv()["CLEARDB_DATABASE_URL"];
    } elseif (array_key_exists("DATABASE_URL", getenv())) {
      $db_url = getenv()["DATABASE_URL"];
    }

    if ($db_url) {
      $db_url = getenv()["CLEARDB_DATABASE_URL"];

      preg_match("/:\/\/(\w+):/", $db_url, $matches);
      $username_from_getenv = substr($matches[0], 3, -1);

      preg_match("/:(\w+)@/", $db_url, $matches);
      $password_from_getenv = substr($matches[0], 1, -1);

      preg_match("/@(.+)\//", $db_url, $matches);
      $host_from_getenv = substr($matches[0], 1, -1);

      preg_match("/\/\/(.+)\?/", $db_url, $matches);
      $db_name_from_getenv = substr($matches[0], 2);

      $db_name_from_getenv = preg_match(
        "/\/(.+)\?/",
        $db_name_from_getenv,
        $matches
      );

      $db_name_from_getenv = substr($matches[0], 1, -1);

      $this->username = $username_from_getenv;
      $this->password = $password_from_getenv;
      $this->host = $host_from_getenv;
      $this->db_name = $db_name_from_getenv;
    }
  }

  public function checkOrMakeGamesTable()
  {
    $this->connection = mysqli_connect(
      $this->host,
      $this->username,
      $this->password,
      $this->db_name
    );

    if (!$this->connection) {
      echo "Error: Was unable to connect to MySQL." . PHP_EOL;
      echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
      echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
      exit();
    }

    $query = "SELECT TABLE_NAME FROM information_schema.tables";

    function check($conn, $query)
    {
      if (!($stmt = $conn->prepare($query))) {
        return [
          "status" => false,
          "message" => "Unable to prepare query.",
          "error" => $conn->error,
        ];
      }

      if (!$stmt->execute()) {
        return [
          "status" => false,
          "message" => "Error in execution emerged.",
          "error" => $conn->error,
        ];
      }

      $result = $stmt->get_result();

      $stmt->close();
      return [
        "status" => true,
        "data" => $result,
      ];
    }

    function build($db, $result)
    {
      if (!$result["status"]) {
        return $result;
      }

      if (!$result['data']->num_rows) {
        return [
          "status" => false,
          "message" => "There are no rows from reading the db.",
          "error" => $db->error,
        ];
      }

      if (!($result_arr = build_table_array($result["data"]))) {
        return [
          "status" => false,
          "message" => "Error in build_table_array.",
          "error" => $db->error,
        ];
      }

      return [
        "status" => true,
        "data" => $result_arr,
      ];
    }

    $response = check($this->connection, $query);
    $response = build($this->connection, $response);

    function test($arr)
    {
      if (
        array_key_exists("TABLE_NAME", $arr) &&
        $arr["TABLE_NAME"] == "games"
      ) {
        return true;
      }
    }

    if (!count(array_filter($response['data'], "test"))) {
      $table_name = "games";

      $create_table_querystring = " (
        `game_id` varchar(32) PRIMARY KEY,
        `last_accessed` int(11) DEFAULT 0,
        `money_stat` int(11) DEFAULT 0,
        `days_stat` int(11) DEFAULT 0,
        `trend_calculates` longtext NOT NULL,
        `level_record` longtext NOT NULL,
        `overall_sales_history` longtext NOT NULL
        )";

      return $res = make_table(
        $table_name,
        $create_table_querystring,
        $this->connection,
        []
      );
    } else {
      return true;
    }
  }

  public function startNewGame()
  {
    include "../../utils/utils.php";

    if (!$this->checkOrMakeGamesTable()) {
      print_r([
        "status" => false,
        "message" =>
          "false result coming from checkOrMakeGamesTable fxn which called make_table util fxn, when trying to check or make games table.",
      ]);
    }

    $this->connection = mysqli_connect(
      $this->host,
      $this->username,
      $this->password,
      $this->db_name
    );

    if (!$this->connection) {
      echo "Error: Am unable to connect to MySQL." . PHP_EOL;
      echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
      echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
      exit();
    }

    if (isset($_SESSION["gid"]) && isset($_SESSION["inv_table_name"])) {
      wipe_previous_game($this->connection);
    }

    $_SESSION["gid"] = get_gid();
    setcookie("makhzan", $_SESSION['gid'], time() + 3600 * 24 * 30, "/");
    $_SESSION["inv_table_name"] = $_SESSION["gid"] . "__inv";
    $table_name = $_SESSION["inv_table_name"];

    //                                                            Make and populate Inventory table.

    $max_prices_json = json_encode(["Low" => 1, "High" => 5]);
    $pop_factors_json = json_encode(["weather" => true, "love" => false]);

    $create_table_querystring = " (
      `rubicon` float(11,1) DEFAULT -1,
      `name` varchar(100) PRIMARY KEY,
      `quantity` int(11) DEFAULT 0,
      `selling_price` int(11) DEFAULT 0,
      `max_prices` longtext NOT NULL,
      `popularity_factors` longtext NOT NULL,
      `popularity_history` longtext,
      `price_history` longtext,
      `quantity_sold_history` longtext,
      `from_quantity_sold_history` longtext
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
        "name" => "Fig", //x5.5
        "rubicon" => 0.1,
        "max_prices" => ["Low" => 2, "High" => 11],
        "popularity_factors" => [
          "politics" => true,
          "conformity" => true,
        ],
      ],
      [
        "name" => "Pistachio", //x6.0
        "rubicon" => 0.2,
        "max_prices" => ["Low" => 5, "High" => 30],
        "popularity_factors" => [
          "weather" => false,
          "politics" => true,
        ],
      ],
      [
        "name" => "Flammable Walnut", //x10
        "rubicon" => 1,
        "max_prices" => ["Low" => 11, "High" => 110],
        "popularity_factors" => [
          "politics" => false,
          "conformity" => true,
        ],
      ],
      [
        "name" => "Enchanted Pomegranate", //x16
        "rubicon" => 2,
        "max_prices" => ["Low" => 36, "High" => 576],
        "popularity_factors" => [
          "decadence" => true,
          "love" => true,
        ],
      ],
      [
        "name" => "The Perfect Date", //x25
        "rubicon" => 3,
        "max_prices" => ["Low" => 80, "High" => 2000],
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
        0 .
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
      "INSERT INTO games (`game_id`, `last_accessed`, `trend_calculates`, `money_stat`, `days_stat`, `level_record`, `overall_sales_history`) VALUES (?, ?, ?, ?, ?, ?, ?)";

    if (!($stmt = $this->connection->prepare($query))) {
      return [
        "status" => false,
        "message" => "Was unable to prepare query.",
        "error" => $this->connection->error,
      ];
    }

    $trends_example = json_encode([
      "weather" => random_int(1, 100),
      "love" => random_int(1, 100),
      "politics" => random_int(1, 100),
      "conformity" => random_int(1, 100),
      "decadence" => random_int(1, 100),
      "conformity_history" => "ss",
    ]);

    $level_record_example = json_encode([
      "round" => 0,
      "sublevel" => 0,
      "history" => [],
      "final_round" => 3,
    ]);

    $overall_sales_history_example = '{}';

    $money_initial = 100;
    $days_initial = 0;

    $gid = $_SESSION["gid"];
    $timestamp = time();
    $stmt->bind_param(
      "sisiiss",
      $gid,
      $timestamp,
      $trends_example,
      $money_initial,
      $days_initial,
      $level_record_example,
      $overall_sales_history_example
    );

    if (!$stmt->execute()) {
      return [
        "status" => false,
        "message" => "An error in execution emerged.",
        "error" => $this->connection->error,
      ];
    }

    $stmt->close();

    $_SESSION["trend_calculates"] = $trends_example;
    $_SESSION["money_stat"] = $money_initial;
    $_SESSION["days_stat"] = $days_initial;
    $_SESSION['level_record'] = $level_record_example;
    $_SESSION['overall_sales_history'] = $overall_sales_history_example;

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
      echo "Error: It is unable to connect to MySQL." . PHP_EOL;
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
