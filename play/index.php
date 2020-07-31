<?php
session_start();
if (!isset($_SESSION['game_begun'])) {
  header("Location: ../home");
  exit();
}
?>

<?php
$money = 30;
$days = 756;
echo "<link rel='stylesheet' type='text/css' href='../css/playIndex.css' />";
include 'content/mainStats.php';
include 'content/mainBulletin.php';
include 'content/mainButton.php';

$content =
  '
<h1>' .
  $days .
  '</h1>
<button onClick=checkSession()>CHECK SESSION</button>
<div class="mainDiv">
  ' .
  $mainStats .
  '
</div>

<div class="mainDiv">
  ' .
  $mainBulletin .
  '
</div>

<div class="mainDiv">
  ' .
  $mainButton .
  '
</div>
  
<div class="mainDiv mainDivTable1">
  <div class="row">
                <div class="col">
                <div class="box">
                  <div class="boxHeader">
                    <h3 class="boxTitle">Your Inventory</h3>
                  </div>

                  <div class="boxBody">
                    <table id="fruit" class="table">
                      <thead>
                      <tr>
                      <th>ID</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Selling price</th>
                        <th>Total sales</th>
                        <th>Action</th>
                      </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    
                    </table>
                  </div>
                </div>
              </div>
            </div>
</div>
  
<div class="mainDiv mainDivTable2">
  <div class="row">
                <div class="col">
                <div class="box">
                  <div class="boxHeader">
                    <h3 class="boxTitle">New stock</h3>
                  </div>

                  <div class="boxBody">
                    <table id="new stock" class="table">
                      <thead>
                      <tr>
                      <th>ID</th>
                        <th>Name</th>
                        <th>Popularity</th>
                        <th>Durability</th>
                        <th>Stock price</th>
                        <th>Action</th>
                      </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    
                    </table>
                  </div>
                </div>
              </div>
            </div>
</div>

';

include '../master.php';
?>

<script>
function checkSession(){
  console.log("<?php echo $_SESSION['favcolor']; ?>");
}
</script>

<script>
fillTable()
function fillTable(shouldWipe){
  
  if (shouldWipe){$('#fruit tbody > tr').remove();}

  let XHreq = new XMLHttpRequest();

  XHreq.onreadystatechange = function(){
  console.log("readyState is", this.readyState);
  if (this.readyState == 4 && this.status == 200){

    // console.log(this);
    // return;

    let data = JSON.parse(this.responseText)
  
    let response="";

    for(let fruit in data){

    let formattedName = data[fruit].name.replace(/\s/g, "%20")

        response += "<tr>"+
        "<td>"+data[fruit].id+"</td>"+
        "<td>"+data[fruit].name+"</td>"+
        "<td>"+data[fruit].quantity+"</td>"+
        "<td>"+data[fruit].selling_price+"</td>"+
        "<td>"+data[fruit].total_sales+"</td>"+
        "<td><button class='button1' onClick=printSingle('"+data[fruit].id+"')>Print single</button> <button class='button1' onClick=restockFruit('"+data[fruit].id+"')>Buy more</button> <button class='button1' onClick=deleteFruit('"+data[fruit].id+"','"+formattedName+"')>Throw away</button></td>"+
        "</tr>";
    }
    $(response).appendTo($("#fruit"));
  }
  
}
XHreq.open("GET", "../api/fruit/read.php", true)
XHreq.send();
}
</script>


<script>
  function printSingle(id){ $.ajax(
        {
            type: "POST",
            url: '../api/fruit/read_single.php',
            dataType: 'json',
            data: {
                id: id
            },
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
        });}
</script>


<script>
  function restockFruit(id){
    
    $.ajax(
        {
            type: "POST",
            url: '../api/fruit/restock.php',
            dataType: 'json',
            data: {
                id: id
            },
            error: function (result) {
              console.log("error", result)
            },
            success: function (result) {

                if (result['quantity']) {     
                  let specificName = result['name']
                  $("table tr td").filter(function() {
                      return $(this).text() == specificName;
                  }).parent('tr').children().eq(2).text(result['quantity'])
                
                } else if (!result['status']) {
                  console.log(result["message"]);
               
                }else {
                   console.log("no quantity key was detected")
                }
            }
        });
      }
</script>


<script>
  function deleteFruit(id, name){

   name = name.replace(/%20/g, " ")

    let result = confirm("Chuck all " + name + " into the street?"); 
    if (result == true) { 
        $.ajax(
        {
            type: "POST",
            url: '../api/fruit/delete.php',
            dataType: 'json',
            data: {
                id: id
            },
            error: function (result) {
                console.log(result.responseText);
            },
            success: function (result) {
                if (result['status'] == true) {
                  $("table tr td").filter(function() {
                      return $(this).text() == name;
                  }).parent('tr').remove()
                }
                else {
                    alert(result['message']);
                }
            }
        });
    }
  }
</script>