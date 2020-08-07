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
// let trend_calculates = null

fillInvTable()

function setTrendCalculates(data){
  trend_calculates = data
}

function seeTrendCalculates(){
  console.log(trend_calculates)
}

function fillInvTable(shouldWipe){
  if (shouldWipe){$('#inventory tbody > tr').remove();}
  $.ajax(
        {
            type: "GET",
            url: '../api/fruit/read.php',
            dataType: 'json',
            data: {
                table_name: "<?php echo $inv_table_name; ?>"
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

                    let trend_calculates = "<?php echo $inv_table_name; ?>"
                    let animal = "<?php echo $animal; ?>"

                    let popularity = 33
                    let max_selling_price = 11
                    let restock_price = 5

                    response += "<tr>"+
                    "<td>"+name+" ("+popularity+"%)"+"</td>"+
                    "<td>"+trend_calculates+"</td>"+
                    "<td>"+animal+"</td>"+
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
  return;
  $.ajax(
        {
            type: "GET",
            url: '../api/fruit/read_single.php',
            dataType: 'json',
            data: {
                table_name: "games",
                identifying_column: "game_id",
                identifying_data: "<?php echo $gid; ?>",
                acronym: "s"
            },
            error: function (result) {
              console.log("An error occurred immediately in $.ajax request.", result)
              console.log(result.responseText)
            },
            success: function (result) {
              console.log("a success")
              console.log(result)
           
              if (result["status"]){ 

                let el = $("p").filter(function() {
                    return $(this).is("#moneyStat");
                })
                el.text(result["data"][0]["money_stat"] + " gold dinar")  

                el = $("p").filter(function() {
                    return $(this).is("#daysStat");
                })
                el.text(result["data"][0]["days_stat"] + " days")  

//Now make ajax to set_session to store these money and days in session.

                $.ajax(
        {
            type: "GET",
            url: '.././utils/set_session.php',
            dataType: 'json',
            data: {
              money_stat: "111",
              days_stat: "222",
              trend_calculates: "333"
            },
            error: function (result) {
              console.log("An error occurred immediately in the $.ajax request.", result)
              console.log(result.responseText)
            },
            success: function (result) {
              console.log("a3 success")
              // window.location = "../play";
           
              if (result["status"]){ 
              } else {
                console.log(result["message"])
                console.log(result["error"])
              }
          }}) 








     //Okay, now we've got the trend calculates

    //  let trend_calculates = result["data"][0]["trend_calculates"];
    // console.log("*", trend_calculates)
    setTrendCalculates(13)
     
     //So waht do we want to do with them?
     //Well, calculate popularity, and set popularity for inv table. 
     //Then evolve the TCs, and reupload them to db.

     //Maybe you only pull the games table data from db once, upon New Game / Continue.
     //Then you increment money, days, TCs all locally.
     //And every "day" you upload the modified game stats to db.
     //But you don't bother pulling down. That's only at start of new session.

     //So we could indeed make this an async request, which if it stalls wyould indeed stall the game.
     //But I think that's fine because if you can't load the game stats, you can't play the game.

     //Aha! So upon click from home/index, load the game stats and store them on SESSION.


                //     //get TCs and use to calculate the below

                //     let trend_calculates = "<?php echo $inv_table_name; ?>"
                //     let animal = "<?php echo $animal; ?>"

                //     let popularity = 33
                //     let max_selling_price = 11
                //     let restock_price = 5

                //     response += "<tr>"+
                //     "<td>"+name+" ("+popularity+"%)"+"</td>"+
                //     "<td>"+trend_calculates+"</td>"+
                //     "<td>"+animal+"</td>"+
                //     "<td>"+selling_price+"</td>"+
                //     "<td>"+restock_price+"</td>"+
                //     "<td>"+resilience+"</td>"+
                //     "<td><button class='button1' onClick=printSingle('"+formattedName+"','inv')>Print single</button> <button class='button1' onClick=restockFruit('"+formattedName+"')>Buy more</button> <button class='button1' onClick=deleteFruit('"+id+"','"+formattedName+"')>Throw away</button></td>"+
                //     "</tr>";

                //     $(response).appendTo($("#inventory"));
                // })
              } else {
                console.log(result["message"])
                console.log(result["error"])
              }
          }})   
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
            acronym: "s"

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