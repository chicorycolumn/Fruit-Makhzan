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
      "game_id",
      $putative_gid,
      "games",
      "last_accessed",
      "s"
    );

    if (!$result || !$result["status"] || !$result["rows"]) {
      setcookie("makhzan", "", time() - 3600);
    } elseif (!isset($_SESSION["gid"])) {
      $_SESSION["gid"] = $_COOKIE["makhzan"];
      $_SESSION["inv_table_name"] = $_COOKIE["makhzan"] . "__inv";
    }
    $database->closeConnection();
  }
}

$gid = $_SESSION['gid'];

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
                console.log("a1 success")
                if (result["status"]) { 
      console.log("a1 true")
      window.location = "../play";

  // $.ajax(
  //       {
  //           type: "GET",
  //           url: '../api/fruit/read_single.php',
  //           dataType: 'json',
  //           data: {
  //               table_name: "games",
  //               identifying_column: "game_id",
  //               identifying_data: "<?php echo $gid; ?>",
  //               acronym: "s"
  //           },
  //           error: function (result) {
  //             console.log("An error occurred immediately in $.ajax request.", result)
  //             console.log(result.responseText)
  //           },
  //           success: function (result) {
  //             console.log("a2 success")
  //             console.log("a2 res", result)
           
  //             if (result["status"]){ 
  //               console.log("a2 true")



        //         $.ajax(
        // {
        //     type: "GET",
        //     url: '../utils/set_session.php',
        //     dataType: 'json',
        //     data: {
        //       money_stat: 0,
        //       days_stat: 0,
        //       trend_calculates: '{}'
        //     },
        //     error: function (result) {
        //       console.log("An error occurred immediately in this $.ajax request.", result)
        //       console.log(result.responseText)
        //       // window.location = "../play";
        //     },
        //     success: function (result) {
        //       console.log("a3 success")
        //       window.location = "../play";
           
        //       if (result["status"]){ 
        //       } else {
        //         console.log(result["message"])
        //         console.log(result["error"])
        //       }
        //   }}) 


          //     } else {
          //       console.log(result["message"])
          //       console.log(result["error"])
          //     }
          // }}) 















                 
                  
                } else {
                  console.log(result["message"]);
                  console.log(result["error"]);
                  console.log(result["error"]["responseText"]);
                    
                }
               
            }
        });
  }
</script>

