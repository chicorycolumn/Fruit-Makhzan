<?php
include_once '../api/config/database.php';
include '../utils/table_utils.php';

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['gid'])) {
  header("Location: ../home");
  exit();
}

setcookie("makhzan", $_SESSION['gid'], time() + 3600 * 24 * 30, "/");

function update_timestamp()
{
  $database = new Database();
  $db = $database->getConnection();
  $result = update_row(
    $db,
    "Last_Accessed",
    time(),
    "Game_ID",
    $_SESSION['gid'],
    "Games",
    "is"
  );
  $database->closeConnection();
  return $result;
}

$result = update_timestamp();

if (!$result) {
  echo "Error in updating game's timestamp.";
  die();
}
?>

<?php
$money = 30;
$days = 756;
$version = $_SESSION["gid"];
echo "<link rel='stylesheet' type='text/css' href='../css/playIndex.css' />";
include 'content/mainStats.php';
include 'content/mainBulletin.php';
include 'content/mainButton.php';
include 'content/invTable.php';
include 'content/nstTable.php';

$content =
  '
<h1> 
  ' .
  $version .
  '
</h1>

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
  ' .
  $invTable .
  '
</div>

<div class="mainDiv mainDivTable2">
  ' .
  $nstTable .
  '
</div>
';

include '../master.php';
?>


<script>
fillInvTable()
fillNstTable()

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
           
              if (result["status"]){ 
              
                result["data"].forEach((fruit)=>{
                    let response="";
                    let formattedName = fruit.name.replace(/\s/g, "%20")

                    response += "<tr>"+
                    "<td>"+fruit.id+"</td>"+
                    "<td>"+fruit.name+"</td>"+
                    "<td>"+fruit.quantity+"</td>"+
                    "<td>"+fruit.selling_price+"</td>"+
                    "<td>"+fruit.total_sales+"</td>"+
                    "<td><button class='button1' onClick=printSingle('"+formattedName+"','inv')>Print single</button> <button class='button1' onClick=restockFruit('"+formattedName+"')>Buy more</button> <button class='button1' onClick=deleteFruit('"+fruit.id+"','"+formattedName+"')>Throw away</button></td>"+
                    "</tr>";

                    $(response).appendTo($("#inventory"));
                })
              } else {
                console.log(result["message"])
                console.log(result["error"])
              }
          }})   
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

              if (result["status"]){ 
              
                result["data"].forEach((fruit)=>{
                    let response="";
                    let formattedName = fruit.name.replace(/\s/g, "%20")

                    response += "<tr>"+
                    "<td>"+fruit.id+"</td>"+
                    "<td>"+fruit.name+"</td>"+
                    "<td>"+fruit.stock_price+"</td>"+
                    "<td>"+fruit.popularity+"</td>"+
                    "<td>"+fruit.durability+"</td>"+
                    "<td><button class='button1' onClick=printSingle('"+formattedName+"','nst')>Print single</button> <button class='button1' onClick=buyFromStock('"+formattedName+"')>Buy</button> </td>"+
                    "</tr>";

                    $(response).appendTo($("#new_stock"));
                })
              } else {
                console.log(result["message"])
                console.log(result["error"]);
              }
          }})   
}

function printSingle(name, table){
  name = name.replace(/%20/g, " ")
    $.ajax(
      {
          type: "GET",
          url: '../api/fruit/read_single.php',
          dataType: 'json',
          data: {
            name: name,
            table: table
          },
          error: function (result) {
            console.log("An error occurred immediately in $.ajax request.", result)
            console.log(result.responseText)
          },
          success: function (result) {
            if (result["status"]) {
            console.log(result);
            } else {
              console.log(result["message"]);
              console.log(result["error"]);
            }
          }
      });
}

function restockFruit(name){
  name = name.replace(/%20/g, " ")
  $.ajax(
      {
          type: "GET",
          url: '../api/fruit/restock.php',
          dataType: 'json',
          data: {
              name: name,
              table: "inv"
          },
          error: function (result) {
            console.log("An error occurred immediately in $.ajax request.")
            console.log(result.responseText)
            console.log(result)
          },
          success: function (result) {
          console.log("success")
              if (result['status']) {     
                let fruit = result["data"][0]
                let el = $("table tr td").filter(function() {
                    return $(this).text() == fruit['name'] && $(this).parent('tr').parent().parent().is("#inventory");
                })
                
                el.parent('tr').children().eq(2).text(fruit['quantity'])
              
              } else {
                console.log(result["message"]);
                console.log(result["error"]);
              }
          }
      });
}

function buyFromStock(name){
  name = name.replace(/%20/g, " ")

  let el = $("table tr td").filter(function() {
                    return $(this).text() == name && $(this).parent('tr').parent().parent().is("#inventory");
                })

  if(el.length){
    restockFruit(name)
  } else {
    $.ajax(
      {
          type: "GET",
          url: '../api/fruit/create.php',
          dataType: 'json',
          data: {
            table: "inv",
              name: name,
              quantity: 10,
      
          },
          error: function (result) {
            console.log("An error occurred immediately in $.ajax request.", result)
            console.log(result.responseText)
          },
          success: function (result) {
                console.log("success")
                if (result["status"]){   
                          
                let response="";
                let fruit = result["data"][0];
            
                let formattedName = fruit.name.replace(/\s/g, "%20")

                    response += "<tr>"+
                    "<td>"+fruit.id+"</td>"+
                    "<td>"+fruit.name+"</td>"+
                    "<td>"+fruit.quantity+"</td>"+
                    "<td>"+fruit.selling_price+"</td>"+
                    "<td>"+fruit.total_sales+"</td>"+
                    "<td><button class='button1' onClick=printSingle('"+formattedName+"','inv')>Print single</button> <button class='button1' onClick=restockFruit('"+formattedName+"')>Buy more</button> <button class='button1' onClick=deleteFruit('"+fruit.id+"','"+formattedName+"')>Throw away</button></td>"+
                    "</tr>";
    
                $(response).prependTo($("#inventory"));
                } else {
                  console.log(result['message']);
                  console.log(result["error"]);
              }
          }
      });
  }
}

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
            console.log("success")
              if (result['status']) {
                let el = $("table tr td").filter(function() {
                    return $(this).text() == name && $(this).parent('tr').parent().parent().is("#inventory");
                })
  el.parent('tr').remove()      
              } else {
                  console.log(result["message"])
                  console.log(result["error"]);
              }
          }
      });
  }
}
</script>

<script>
//  woooooooooooooooooooooooooooooooooooo
  // function exampleGameID(){
  //   console.log('
  //<
  //?php echo include "../utils/get_gid.php"; ?>')
  // }
  // function checkSession(){
  // console.log(`
  // <
  // ?php echo json_encode($_SESSION); ?>`);
  // }
</script>