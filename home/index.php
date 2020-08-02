<?php

$content = '
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
<button style="height:150px;" onClick=startNewGame(false)>NEW TABLE BUT STAY ON THIS PAGE</button>
<button style="height:150px;" onClick=startNewGame(true)>START NEW GAME</button>
';

include '../master.php';
?>

<script>
function sayHello(){
  console.log("way eai man")
}
</script>


<script>
  function startNewGame(shouldNavigate, dontTryAgain){
    $.ajax(
        {
            type: "POST",
            url: '../api/fruit/new_game.php',
            dataType: 'json',
            error: function (result) {
              console.log("Immediate error from request to new_game. Try clicking New Game button again.", result)
            },
            success: function (result) {
                if (result["status"]) {
                if (!shouldNavigate){
                  alert("Sucessfully created new game!")
                }else{
                  window.location = "../play";
                }
                }
                else {

                  if (!dontTryAgain){
                    startNewGame(true, true)
                  }

                  console.log(result["message"]);
                    
                }
               
            }
        });
  }
</script>

