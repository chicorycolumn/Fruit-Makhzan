<?php
class Database{
 
    private $host = "localhost";
    private $db_name = "fruit_makhzan_db";
    private $username = "root";
    private $password = "";
    public $connection;
 
    // $this->conn->exec("set names utf8");

    public function execute_query($query, $con)
    {
        $res = $con->query($query);
    
        if (!$res) {
            echo "failed to execute query: $query\n";
        } else {
            return $res;
            // echo "Query: $query executed\n";
        }
    
        if (is_object($res)) {
            $res->close();
        }
    }

    public function getConnection(){
 
        $this->connection = mysqli_connect($this->host, $this->username, $this->password, $this->db_name);

        if (!$this->connection) {
            echo "Error: Unable to connect to MySQL." . PHP_EOL;
            echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
            echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
            exit;
        }
        
        // echo "Success: A proper connection to MySQL was made! The my_db database is great." . PHP_EOL;
        // echo "Host information: " . mysqli_get_host_info($this->connection) . PHP_EOL;

        // $sql = "INSERT INTO fruit(name)VALUES ('".$_POST["name"]."')";

        // $query = "INSERT INTO fruit(name, quantity, selling_price, total_sales) VALUES('jumpy', 5, 6, 7)";
        // $this->execute_query($query, $this->connection);

        return $this->connection;

    }
}
?>