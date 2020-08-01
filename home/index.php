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
<button style="height:150px;" onClick=makeConnection(false)>NEW TABLE BUT STAY ON THIS PAGE</button>
<button style="height:150px;" onClick=makeConnection(true)>START NEW GAME</button>
';

include '../master.php';
?>

<script>
function sayHello(){
  console.log("way eai man")
}
</script>


<script>
  function makeConnection(shouldNavigate){
    $.ajax(
        {
            type: "POST",
            url: '../api/fruit/make_connection.php',
            dataType: 'json',
            data: {lemon: 0},
            error: function (result) {
              console.log("immediate error from request to make_connection", result)
                // alert(result);
            },
            success: function (result) {

              console.log("success result is", result)
                if (result) {
                console.log(result);
                }
                else {
                  console.log("else")
                    // alert(result['message']);
                }
                if (shouldNavigate){
                window.location = "../play";}
            }
        });
  }
</script>

