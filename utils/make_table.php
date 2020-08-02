<?php

function make_table(
  $table_name,
  $create_table_querystring,
  $connection,
  $query_array
) {
  $query = "CREATE TABLE " . $table_name . $create_table_querystring;

  if (mysqli_query($connection, $query)) {
    foreach ($query_array as $query) {
      mysqli_query($connection, $query);
    }
  } else {
    echo "Error: Unable to mysqli_query(Sthis->connection, Squery). Possibly there was already a table with this game id, in which case just try clicking the New Game button again." .
      PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit();
  }
  return true;
}

?>
