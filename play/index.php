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
<button onClick="checkTCs()">Check TCs and Seed Data</button>

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
//' For some reason this apostrophe is necessary.

let seed_data = <?php print_r(json_encode($_SESSION['seed_data'])); ?>

let trend_calculates = {
  "weather": parseInt(`<?php print_r(
    ((array) json_decode($_SESSION['trend_calculates']))['weather']
  ); ?>`),
  "love": parseInt(`<?php print_r(
    ((array) json_decode($_SESSION['trend_calculates']))['love']
  ); ?>`),
    "politics": parseInt(`<?php print_r(
      ((array) json_decode($_SESSION['trend_calculates']))['politics']
    ); ?>`),
  "conformity": parseInt(`<?php print_r(
    ((array) json_decode($_SESSION['trend_calculates']))['conformity']
  ); ?>`),
    "decadence": parseInt(`<?php print_r(
      ((array) json_decode($_SESSION['trend_calculates']))['decadence']
    ); ?>`),
}


fillInvTable()
updateGameStats(
  "<?php echo $_SESSION['money_stat']; ?>", 
  "<?php echo $_SESSION['days_stat']; ?>",
  null
)

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
                    let {id, name, quantity, selling_price, resilience, max_prices, popularity_factors} = fruit
                    let formattedName = name.replace(/\s/g, "%20")

                    
                    let {popularity, popularity_word, max_buying_price, restock_price} = getSalesSubstrates(popularity_factors, max_prices)
                    
                    console.dir({
                      popularity, popularity_word, max_prices, max_buying_price, restock_price
                    })

                    response += "<tr>"+
                    "<td>"+name+"</td>"+
                    "<td>"+"qy"+"</td>"+
                    "<td>"+quantity+"</td>"+
                    "<td>"+selling_price+"</td>"+
                    "<td>"+restock_price+"</td>"+
                    "<td>"+resilience+" (pop "+popularity+")"+" (mxb "+max_buying_price+")"+"</td>"+
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

function updateGameStats(new_money_stat, new_days_stat, new_trend_calculates){
  let el = $("p").filter(function() {
                    return $(this).is("#moneyStat");
                })
  el.text(new_money_stat + " Gold Dinar")  

  el = $("p").filter(function() {
      return $(this).is("#daysStat");
  })
  el.text(new_days_stat + " Days")  

  if (new_trend_calculates){  
    trend_calculates = new_trend_calculates
  }
    
console.log("updateGameStats fxn says TCs are:", trend_calculates)
}

function newDay(){

//We will use seed_data - available - which has Price Set and Pop Factors for each fruit.
//We will also use TC proxy - available.
//Hmmm? What we need to make available, is the selling_price and quantity for each fruit.

//With those three sets of things, we will calculate the Sales Percentage for each fruit.
//We will send off a POST with the new data, to update the inventory table.

//And then jquery change all the things.




//Calculate sales for each fruit and console log it.
//Perhaps ajax to db for fruit data (even though we have it right here, I know, but is hard to access),
//and then do the sales calculations... and then a second ajax request to update db?
//Maybe just one api/sales that does both.


  $.ajax(
    {
          type: "GET",
          url: '../api/fruit/new_day.php',
          dataType: 'json',
          data: {
            table_name: "games",
            identifying_column: "game_id",
            identifying_data: `<?php echo $_SESSION['gid']; ?>`,
          },
          error: function (result) {
            console.log("An error occurred immediately in $.ajax request.", result)
            console.log(result.responseText)
          },
          success: function (result) {
            console.log("success")
            if (result["status"]) {
            console.log(result);

let {money_stat, days_stat, trend_calculates} = result['update_data']
updateGameStats(money_stat, days_stat, trend_calculates)

            } else {
              console.log(result["message"]);
              console.log(result["error"]);
            }
          }
      }
  )

  //Okay, I will now create an Update api, to update the db (games table with new Days eg) and also session.

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
              table_name: "<?php echo $inv_table_name; ?>",
              increment: 1
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
                    return $(this).text() == fruit['name']
                     && $(this).parent('tr').parent().parent().is("#inventory");
                })

                // el.parent('tr').remove()  
                el.parent('tr').children().eq(2).text(fruit['quantity'])
              
              } else {
                console.log(result["message"]);
                console.log(result["error"]);
              }
          }
      });
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
  console.log(">>>Old session is:")
  console.log(`<?php print_r($_SESSION); ?>`)
}

function checkTCs(){
  console.log(">>>Current TCs are:")
  console.log(trend_calculates)
  console.log(">>>Seed data is:")
  console.log(seed_data)
}

function getSalesSubstrates(popularity_factors, max_prices){
  let factor1 = getPopularityFactor(popularity_factors, 0)
  let factor2 = getPopularityFactor(popularity_factors, 1)
  let popularity = Math.ceil(( factor1 + factor1 + factor1 + factor2  ) / 4);

  let popularity_word = popularity > 67 ? "High" : popularity < 33 ? "Low" : "Medium"
  let max_buying_price = max_prices[popularity_word]
  let restock_price = Math.ceil(0.8*max_buying_price)

  return {popularity: popularity, popularity_word: popularity_word, max_buying_price: max_buying_price, restock_price: restock_price}
}

function getPopularityFactor(pop_factor_names, i){
  let pop_keys = Object.keys(pop_factor_names)
  return pop_factor_names[pop_keys[i]] ? trend_calculates[pop_keys[i]] : 101-trend_calculates[pop_keys[i]]
}
</script>