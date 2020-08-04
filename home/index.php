<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

include '../utils/table_utils.php';

if (isset($_COOKIE["makhzan"])) {
  $putative_gid = $_COOKIE["makhzan"];

  if (preg_match("/[^\w]/i", $putative_gid) || strlen($putative_gid) != 15) {
    setcookie("makhzan", "", time() - 3600);
  } else {
    include_once '../api/config/database.php';
    $database = new Database();
    $db = $database->getConnection();

    $result = check_row_exists(
      $db,
      "Game_ID",
      $putative_gid,
      "Games",
      "Last_Accessed",
      "s"
    );

    if (!$result || !$result["status"] || !$result["rows"]) {
      setcookie("makhzan", "", time() - 3600);
    } elseif (!isset($_SESSION["gid"])) {
      $_SESSION["gid"] = $_COOKIE["makhzan"];
      $_SESSION["inv_table_name"] = $_COOKIE["makhzan"] . "__INV";
      $_SESSION["nst_table_name"] = $_COOKIE["makhzan"] . "__NST";
    }
    $database->closeConnection();
  }
}

$content =
  '
<br/>
You are Ibn al-Baitar (b. 1197 AD), Andalusian botanist and scientist.
<br/>
<br/>
On a flight of fancy, you use your botanical knowledge to open a Fruit Makhzan (storehouse).
<br/>
<br/>
From here you aim to become the best fruit seller in all of Al-Andalus!
<br/>
<br/>
<img src="../images/pineapple.png" style="height:75px; width:50px;" />
<br/>
<button style="height:150px;width:200px;" ' .
  (isset($_SESSION["gid"]) ? "" : "disabled") .
  ' onClick=loadPrevious()>CONTINUE</button>
<button style="height:150px;width:200px;" onClick=startNewGame()>NEW GAME</button>
';

include '../master.php';
?>

<script>
    function loadPrevious() {
      window.location = "../play";
     }
</script>

<script>
  function startNewGame(){
    $.ajax(
        {
            type: "POST",
            url: '../api/fruit/new_game.php',
            dataType: 'json',
            error: function (result) {
              console.log("Immediate error from request to new_game. Try clicking New Game button again.", result)
              console.log(result["responseText"]);
              console.log(result);
            },
            success: function (result) {
                if (result["status"]) { 
                  window.location = "../play";
                  
                } else {
                  console.log(result["message"]);
                  console.log(result["error"]);
                  console.log(result["error"]["responseText"]);
                    
                }
               
            }
        });
  }
</script>

