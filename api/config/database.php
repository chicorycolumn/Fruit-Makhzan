<?php
class Database{
 
    // private $username = "root";
    // private $password = "";
    // private $host = "localhost";
    // private $db_name = "fruit_makhzan_db";

    private $username = "b4709ad1452782";
    private $password = "7d6b0f7d";
    private $host = "us-cdbr-east-02.cleardb.com";
    private $db_name = "heroku_cb0feae1098e18e";

    public $table_name = null;
    public $connection;

    // $this->conn->exec("set names utf8");

    public function __construct(){
        $this->table_name = "v".(time()-1590000000);
    }

    public function execute_query($query, $con){
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

        //CREATE THE v1 TABLE
        $query = "CREATE TABLE ".$this->table_name." (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` varchar(255) NOT NULL,
            `quantity` int(11) NOT NULL,
            `selling_price` int(11) NOT NULL,
            `total_sales` int(11) DEFAULT 0,
            `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
          )";  

         if(mysqli_query($this->connection, $query)){  

            $query_array = [
                "INSERT INTO ".$this->table_name." (`name`, `quantity`, `selling_price`, `total_sales`) VALUES
            ('Morangines', 50, 5, 20)",
            
            "INSERT INTO ".$this->table_name." (`name`, `quantity`, `selling_price`) VALUES
            ('Miwiwoos', 50, 5)",
            
            "INSERT INTO ".$this->table_name." (`name`, `quantity`, `selling_price`) VALUES
            ('Matey-wateys', 30, 10)"
            ];
            
            foreach($query_array as $query){
                mysqli_query($this->connection, $query);
            }

 
        //********* */
        //  mysqli_close($this->connection);  
        
        // echo "Success: A proper connection to MySQL was made! The my_db database is great." . PHP_EOL;
        // echo "Host information: " . mysqli_get_host_info($this->connection) . PHP_EOL;

        // $sql = "INSERT INTO fruit(name)VALUES ('".$_POST["name"]."')";

        // $query = "INSERT INTO fruit(name, quantity, selling_price, total_sales) VALUES('jumpy', 5, 6, 7)";
        // $this->execute_query($query, $this->connection);

        //return $this->connection;
        return $this;
    }
}
}
?>