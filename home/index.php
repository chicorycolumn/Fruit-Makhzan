<?php
include '../utils/table_utils.php';
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
delete_manipulated_cookie();

include '../play/functions/includes_home.php';
?>

<?php
$content =
  '  <div class="dialogHolder">
<img class="scroll" src=".././images/scroll2a.png">
<div class="dialogBox">

  <div class="dialogBoxInner dialogBoxInnerHome">
    <div class="dialogBoxInnerInner">
      <p class=dialogBoxTextSmall>You are Ibn al-Baitar, medieval Andalusian scientist.</p>
      <p class=dialogBoxTextSmall>With your botanical knowledge, on a whim you open a Fruit Makhzan (storehouse).</p>
      <p class=dialogBoxTextSmall>You aim to become the best fruit seller in all of Al-Andalus!</p>
    </div>
  </div>

  <div class="homeButtonHolder">

    <button class="homeButton" onClick=loadPrevious() ' .
  (isset($_COOKIE["makhzan"]) ? "" : "disabled") .
  '>
      
      <img class="homeButtonImage" src="../images/cherry_sized_shadow2a.png" style="height:75px; width:75px;" />
      <p class="noMarginPadding homeButtonText">CONTINUE</p>
    
    </button>

    <button class="homeButton" onClick=startNewGame()>
      <img class="homeButtonImage" src="../images/banana_sized_shadow2.png" style="height:75px; width:75px;" />
      <p class="noMarginPadding homeButtonText">NEW GAME</p>
    </button>

  </div>



</div>
</div>
';

// '
// <br/>
// You are Ibn al-Baitar (b. 1197 AD), Andalusian botanist and scientist.
// <br/>
// <br/>
// On a flight of fancy, you use your botanical knowledge to open a Fruit Makhzan (storehouse).
// <br/>
// <br/>
// From here you aim to become the best fruit seller in all of Al-Andalus!
// <br/>
// <br/>
// <img src="../images/pineapple_sized_shadow2.png" style="height:75px; width:75px;" />
// <br/>
// <button style="height:150px;width:200px;" ' .
//   (isset($_COOKIE["makhzan"]) ? "" : "disabled") .
//   ' onClick=loadPrevious()>CONTINUE</button>
// <button style="height:150px;width:200px;" onClick=startNewGame()>NEW GAME</button>
// ';

include '../master.php';
?>

<script>

function loadPrevious() {
  $.ajax({
    type: "GET",
    url: "../api/fruit/read_single.php",
    dataType: "json",
    data: {
      table_name: "games",
      identifying_column: "game_id",
      identifying_data: "<?php if (isset($_COOKIE['makhzan'])) {
        echo $_COOKIE['makhzan'];
      } else {
        echo "NO GID ON COOKIE";
      } ?>",
      acronym: "s",
      get_full: false,
      load_session_from_db: true,
    },
    error: function (result) {
      console.log(
        "Immediate error from request to read_single. Try clicking New Game button again.", result, result["responseText"]
      );
    },
    success: function (result) {
      if (result["status"]) {
        window.location = "../play";
      } else {
        console.log(result, result["message"], result["error"]);
      }
    },
  });
}

function startNewGame() {
  $.ajax({
    type: "POST",
    url: "../api/fruit/new_game.php",
    dataType: "json",
    error: function (result) {
      console.log(
        "Immediate error from request to new_game. Try clicking New Game button again.",
        result, result["responseText"]
      );
    },
    success: function (result) {
      if (result["status"]) {
        // console.log(result)
        window.location = "../play";
      } else {
        console.log(result["message"], result["error"], result);
      }
    },
  });
}

setZoom();

</script>

