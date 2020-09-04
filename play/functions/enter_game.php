<script>

function loadPrevious() {

  if (!"<?php echo isset($_COOKIE['makhzan']); ?>"){
    return
  }

  console.log("loadPrevious", "<?php if (isset($_COOKIE['makhzan'])) {
    echo $_COOKIE['makhzan'];
  } ?>" )
  
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
      type_definition_string: "s",
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

        console.log(result['data'][0])

        setTimeout(() => {
          window.location = "../play";
        }, 5000);
      } else {
        console.log(result, result["message"], result["error"]);
      }
    },
  });
}

function startNewGame() {

  console.log("startNewGame")

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
        window.location = "../play";
      } else {
        console.log(result["message"], result["error"], result);
      }
    },
  });
}

</script>