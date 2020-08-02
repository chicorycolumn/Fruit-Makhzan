<?php
session_start();
if (!isset($_SESSION['gid'])) {
  header("Location: ../home");
  exit();
}
?>

<?php
$version = 1851;
$money = 30;
$days = 756;
echo "<link rel='stylesheet' type='text/css' href='../css/playIndex.css' />";
include 'content/mainStats.php';
include 'content/mainBulletin.php';
include 'content/mainButton.php';

$content =
  '
<h1> version ' .
  $version .
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
                    <table id="inventory" class="table">
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
                    <table id="new_stock" class="table">
                      <thead>
                      <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Stock price</th>
                        <th>Popularity</th>
                        <th>Durability</th>
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
fillNstTable()
fillInvTable()
function fillInvTable(shouldWipe){
  
  if (shouldWipe){$('#inventory tbody > tr').remove();}

  $.ajax(
        {
            type: "GET",
            url: '../api/fruit/read.php',
            dataType: 'json',
            data: {
                table: "inv"
            },
            error: function (result) {
              console.log("An error occurred immediately in $.ajax request.", result)
              console.log(result.responseText)
            },
            success: function (result) {
              console.log("success")
           
              if (result.length){                 
                  let response="";

                  for (let fruit in result){
                  let formattedName = result[fruit].name.replace(/\s/g, "%20")

                      response += "<tr>"+
                      "<td>"+result[fruit].id+"</td>"+
                      "<td>"+result[fruit].name+"</td>"+
                      "<td>"+result[fruit].quantity+"</td>"+
                      "<td>"+result[fruit].selling_price+"</td>"+
                      "<td>"+result[fruit].total_sales+"</td>"+
                      "<td><button class='button1' onClick=printSingle('"+result[fruit].id+"','inv')>Print single</button> <button class='button1' onClick=restockFruit('"+result[fruit].id+"')>Buy more</button> <button class='button1' onClick=deleteFruit('"+result[fruit].id+"','"+formattedName+"')>Throw away</button></td>"+
                      "</tr>";
                  }
                  $(response).appendTo($("#inventory"));}

                else if (result['status'] == false) {
                  console.log(result["message"]);
                }else {
                  console.log("Not even a false status came back.")
                  console.log(result)
                }
            }
        });
}
function fillNstTable(shouldWipe){
  
  if (shouldWipe){$('#new_stock tbody > tr').remove();}

  $.ajax(
        {
            type: "GET",
            url: '../api/fruit/read.php',
            dataType: 'json',
            data: {
                table: "nst"
            },
            error: function (result) {
              console.log("An error occurred immediately in $.ajax request.", result)
              console.log(result.responseText)
            },
            success: function (result) {
              console.log("success")
           
              if (result.length){                 
                  let response="";

                  for (let fruit in result){
                  let formattedName = result[fruit].name.replace(/\s/g, "%20")

                      response += "<tr>"+
                      "<td>"+result[fruit].id+"</td>"+
                      "<td>"+result[fruit].name+"</td>"+
                      "<td>"+result[fruit].stock_price+"</td>"+
                      "<td>"+result[fruit].popularity+"</td>"+
                      "<td>"+result[fruit].durability+"</td>"+
                      "<td><button class='button1' onClick=printSingle('"+result[fruit].id+"','nst')>Print single</button> <button class='button1' onClick=buyFromStock('"+formattedName+"')>Buy</button> </td>"+
                      "</tr>";
                  }
                  $(response).appendTo($("#new_stock"));}

                else if (result['status'] == false) {
                  console.log(result["message"]);
                }else {
                  console.log("Not even a false status came back.")
                  console.log(result)
                }
            }
        });
}
</script>


<script>
  function printSingle(id, table){ $.ajax(
        {
            type: "GET",
            url: '../api/fruit/read_single.php',
            dataType: 'json',
            data: {
                id: id,
                table: table
            },
            error: function (result) {
              console.log("An error occurred immediately in $.ajax request.", result)
              console.log(result.responseText)
            },
            success: function (result) {
                if (result) {
                console.log(result);
                }
                else {
                  console.log("In the success clause, but no data came back.")
                }
            }
        });}
</script>


<script>
  function restockFruit(id){
    
    $.ajax(
        {
            type: "GET",
            url: '../api/fruit/restock.php',
            dataType: 'json',
            data: {
                id: id,
                table: "inv"
            },
            error: function (result) {
              console.log("An error occurred immediately in $.ajax request.")
              console.log(result.responseText)
              console.log(result)
            },
            success: function (result) {
                if (result['quantity']) {     

                  let el = $("table tr td").filter(function() {
                      return $(this).text() == result['name'] && $(this).parent('tr').parent().parent().is("#inventory");
                  })
                  
                  el.parent('tr').children().eq(2).text(result['quantity'])
                
                } else if (result['status'] == false) {
                  console.log(result["message"]);
                }else {
                   console.log("The data that came back did not have the right keys.")
                }
            }
        });
      }
</script>

<script>
  function buyFromStock(name){
    name = name.replace(/%20/g, " ")

    let el = $("table tr td").filter(function() {
                      return $(this).text() == name && $(this).parent('tr').parent().parent().is("#inventory");
                  })


console.log(el)
return

    if(el.length){
      restockFruit(id)
      return
    }
 

    return;
    
    $.ajax(
        {
            type: "GET",
            url: '../api/fruit/restock.php',
            dataType: 'json',
            data: {
                id: id,
                table: "inv"
            },
            error: function (result) {
              console.log("An error occurred immediately in $.ajax request.")
              console.log(result.responseText)
              console.log(result)
            },
            success: function (result) {
                if (result['quantity']) {     

                  let el = $("table tr td").filter(function() {
                      return $(this).text() == result['name'] && $(this).parent('tr').parent().parent().is("#inventory");
                  })
                  
                  el.parent('tr').children().eq(2).text(result['quantity'])
                
                } else if (result['status'] == false) {
                  console.log(result["message"]);
                }else {
                   console.log("The data that came back did not have the right keys.")
                }
            }
        });
      }
</script>


<script>
  function deleteFruit(id, name){

   name = name.replace(/%20/g, " ")
    
    if (result = confirm("Chuck all " + name + " into the street?")) { 
        $.ajax(
        {
            type: "GET",
            url: '../api/fruit/delete.php',
            dataType: 'json',
            data: {
                id: id,
                table: "inv"
            },
            error: function (result) {
              console.log("An error occurred immediately in $.ajax request.")
              console.log(result.responseText)
              console.log(result)
            },

            success: function (result) {
                if (result['status'] == true) {
                  let el = $("table tr td").filter(function() {
                      return $(this).text() == name && $(this).parent('tr').parent().parent().is("#inventory");
                  })
   el.parent('tr').remove()      
                } else {
                    alert(result['message']);
                    console.log(result)
                }
            }
        });
    }
  }
</script>

<script>
  // function exampleGameID(){
  //   console.log('<?php echo include "../utils/get_gid.php"; ?>')
  // }
  function checkSession(){
  console.log(`<?php echo json_encode($_SESSION); ?>`);
}
</script>