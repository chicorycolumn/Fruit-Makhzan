<?php
$money = 30;
$days = 756;
echo "<link rel='stylesheet' type='text/css' href='../css/playIndex.css' />";
include('content/mainStats.php');
include('content/mainBulletin.php');
include('content/mainButton.php');

$content = '
<h1>'.$days.'</h1>
<div class="mainDiv">
  '.$mainStats.'
</div>

<div class="mainDiv">
  '.$mainBulletin.'
</div>

<div class="mainDiv">
  '.$mainButton.'
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

include('../master.php');
?>

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
                alert(result);
            },
            success: function (result) {
              console.log("success result is", result)
                if (result['quantity']) {
                  console.log(result['quantity'])
                  
                  let specificName = result['name']

                  $("table tr td").filter(function() {
                      return $(this).text() == specificName;
                  }).parent('tr').children().eq(2).text(result['quantity'])

                }
                else {
                    alert(result['message']);
                }
            }
        });}
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

    // notice.innerHTML = this.responseText
    let data = JSON.parse(this.responseText)
  
    let response="";

    for(let fruit in data){

    let formattedName = data[fruit].name.replace(" ", "%20")

        response += "<tr>"+
        "<td>"+data[fruit].id+"</td>"+
        "<td>"+data[fruit].name+"</td>"+
        "<td>"+data[fruit].quantity+"</td>"+
        "<td>"+data[fruit].selling_price+"</td>"+
        "<td>"+data[fruit].total_sales+"</td>"+
        "<td><button class='button1' onClick=restockFruit('"+data[fruit].id+"')>Buy more</button> <button class='button1' onClick=deleteFruit('"+data[fruit].id+"','"+formattedName+"')>Throw away</button></td>"+
        "</tr>";
    }
    $(response).appendTo($("#fruit"));
  }
  
}

//BROWSER PATH: http://localhost/fruit_makhzan/api/fruit/TARGET
XHreq.open("GET", "../api/fruit/read.php", true)
XHreq.send();

}

</script>

<script>

  function deleteFruit(id, name){

   name = name.replace("%20", " ")
   console.log(name)

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
                alert(result.responseText);
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