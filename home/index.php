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
<a href="../play"><button style="height:150px;" onClick=makeConnection()>START NEW GAME</button></a>
';

include '../master.php';
?>


<script>
  function makeConnection(){
    $.ajax(
        {
            type: "GET",
            url: '../api/fruit/make_connection.php',
            dataType: 'json',
            data: {},
            error: function (result) {
              console.log("error", result)
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
            }
        });
  }
</script>

