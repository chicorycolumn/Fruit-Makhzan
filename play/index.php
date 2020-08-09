<?php
include_once '../api/config/database.php';
include '../utils/table_utils.php';
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
delete_manipulated_cookie();

if (!isset($_SESSION['gid'])) {
  header("Location: ../home");
  exit();
}

setcookie("makhzan", $_SESSION['gid'], time() + 3600 * 24 * 30, "/");
$gid = $_SESSION['gid'];
$inv_table_name = $_SESSION['inv_table_name'];
$animal = "doggy";

function update_timestamp()
{
  $database = new Database();
  $db = $database->getConnection();
  $result = update_row(
    $db,
    "last_accessed",
    time(),
    "game_id",
    $_SESSION['gid'],
    "games",
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
echo "<link rel='stylesheet' type='text/css' href='../css/playIndex.css' />";
include 'content/mainStats.php';
include 'content/mainBulletin.php';
include 'content/mainButton.php';
include 'content/invTable.php';

$content =
  '
<h1> 
  ' .
  $gid .
  '
</h1>

<button onClick="checkSession()">Check Session</button>

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
';

include '../master.php';
?>


<script>
//' For some reason this is necessary.
fillInvTable()
getGameStats()

function fillInvTable(shouldWipe){
  if (shouldWipe){$('#inventory tbody > tr').remove();}
  $.ajax(
        {
            type: "GET",
            url: '../api/fruit/read.php',
            dataType: 'json',
            data: {
                table_name: "<?php echo $inv_table_name; ?>",
                get_full: false
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

                    let {id, name, quantity, selling_price, resilience} = fruit

                    let formattedName = name.replace(/\s/g, "%20")


                    //get TCs and use to calculate the below

                    let popularity = 33
                    let max_selling_price = 11
                    let restock_price = 5

                    response += "<tr>"+
                    "<td>"+name+" ("+popularity+"%)"+"</td>"+
                    "<td>"+quantity+"</td>"+
                    "<td>"+quantity+"</td>"+
                    "<td>"+selling_price+"</td>"+
                    "<td>"+restock_price+"</td>"+
                    "<td>"+resilience+"</td>"+
                    "<td><button class='button1' onClick=printSingle('"+formattedName+"')>Print single</button> <button class='button1' onClick=restockFruit('"+formattedName+"')>Buy more</button> <button class='button1' onClick=deleteFruit('"+id+"','"+formattedName+"')>Throw away</button></td>"+
                    "</tr>";

                    $(response).appendTo($("#inventory"));
                })
              } else {
                console.log(result["message"])
                console.log(result["error"])
              }
          }})   
}

function getGameStats(){

let money_stat = "<?php echo $_SESSION['money_stat']; ?>"
let days_stat = "<?php echo $_SESSION['days_stat']; ?>"
let trend_calculates = `<?php echo $_SESSION['trend_calculates']; ?>`

  let el = $("p").filter(function() {
                    return $(this).is("#moneyStat");
                })
  el.text(money_stat + " Gold Dinar")  

  el = $("p").filter(function() {
      return $(this).is("#daysStat");
  })
  el.text(days_stat + " Days")  

  console.log("getGameStats fxn says TCs are:", trend_calculates)
}

function newDay(){
  //Calculate sales for each fruit, based its pop, max_selling_price.
  //Store the quantity for each fruit, and copy those to the quantity_yest column.
  
  //Send off to inv table, the new quantity for each fruit, which is quantity minus sold.
  //Then download these anew.

  //Run evolveTrendCalculates
  //Send off to games table, the new TCs, and Money increased by sales, and Days increased by 1.
  //Then download these three data from games table anew. One source of truth.
}

function printSingle(name){
  name = name.replace(/%20/g, " ")
    $.ajax(
      {
          type: "GET",
          url: '../api/fruit/read_single.php',
          dataType: 'json',
          data: {
            table_name:  "<?php echo $inv_table_name; ?>",
            identifying_column: "name",
            identifying_data: name,
            acronym: "s",
            get_full: false

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
              table_name: "<?php echo $inv_table_name; ?>"
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
            table_name:  "<?php echo $inv_table_name; ?>",
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
                    "<td><button class='button1' onClick=printSingle('"+formattedName+"')>Print single</button> <button class='button1' onClick=restockFruit('"+formattedName+"')>Buy more</button> <button class='button1' onClick=deleteFruit('"+fruit.id+"','"+formattedName+"')>Throw away</button></td>"+
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
              table_name:  "<?php echo $inv_table_name; ?>"
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

function checkSession(){
  console.log(`<?php print_r($_SESSION); ?>`)
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