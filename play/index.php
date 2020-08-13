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

let {quantity_column_index, selling_price_column_index, name_column_index} = getColumnIndexes()

fillInvTable()
updateGameStats(
  "<?php echo $_SESSION['money_stat']; ?>", 
  "<?php echo $_SESSION['days_stat']; ?>",
  null
)


function newDay(){
  
  let incipient_sales = calculateSales()
  let total_profit = Object.values(incipient_sales).reduce((sum, obj) => sum + obj.profit, 0)

  console.log("About to gain " + total_profit + "Ð.")

  updateGamesTable(total_profit)  //Increments Money and Days.

  //Now we need to minus the quantities sold from quantities in inv table. 
  updateInventoryTable(incipient_sales)

}

function updateGamesTable(profit){
  $.ajax(
    {
          type: "GET",
          url: '../api/fruit/new_day_supertable.php',
          dataType: 'json',
          data: {
            table_name: "games",
            identifying_column: "game_id",
            identifying_data: `<?php echo $_SESSION['gid']; ?>`,
            profit: profit
          },
          error: function (result) {
            console.log("An error occurred immediately in $.ajax request.", result)
            console.log(result.responseText)
          },
          success: function (result) {
            // console.log("success")
            if (result["status"]) {
            // console.log(result);

            let {money_stat, days_stat, trend_calculates} = result['update_data']
            updateGameStats(money_stat, days_stat, trend_calculates)

            } else {
              console.log(result["message"]);
              console.log(result["error"]);
            }
          }
      }
  )
}

function updateInventoryTable(incipient_sales){
  console.log("### updateInventoryTable fxn invoked")
  console.log(incipient_sales)
  // return
  $.ajax(
    {
          type: "POST",
          url: '../api/fruit/new_day_subtable.php',
          dataType: 'json',
          data: {
            table_name: `<?php echo $_SESSION['inv_table_name']; ?>`,
            column_to_change: "quantity",
            new_data_key: "sales_quantity",
            identifying_column: "name",
            operation: "decrement",
            data_obj: incipient_sales,
            data_type: "i"
          },
          error: function (result) {
            console.log("###An error occurred immediately in $.ajax request.", result)
            console.log(result.responseText)
          },
          success: function (result) {
            console.log("###success")
            if (result["status"]) {
              let names = Object.keys(result["update_data"]);

              names.forEach(name=>{
                let row = $("table#inventory tr").filter(function(){
                  return $(this).children().eq(name_column_index).text() == name;
                })

                let current_quantity = parseInt(row.children().eq(quantity_column_index).text())
                let new_quantity = current_quantity - parseInt(result["update_data"][name]["sales_quantity"])
                row.children().eq(quantity_column_index).text(new_quantity)
              })

            } else {
              console.log("###else")
              console.log(result["message"]);
              console.log(result["error"]);
            }
          }
      }
  )
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
    console.log("old TCs", trend_calculates)
    console.log("new TCs", new_trend_calculates)
    trend_calculates = new_trend_calculates
  }
  console.log("updateGameStats fxn says TCs are:", trend_calculates)
}

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
              // console.log("success")
           
              if (result["status"]){ 
              
                result["data"].forEach((fruit)=>{
                    let response="";
                    let {id, name, quantity, selling_price, resilience, max_prices, popularity_factors} = fruit
                    let formattedName = name.replace(/\s/g, "%20")

                    
                    let {popularity, popularity_word, max_buying_price, restock_price} = getSalesSubstrates(popularity_factors, max_prices, trend_calculates)
                    
                    // console.dir({
                    //   popularity, popularity_word, max_prices, max_buying_price, restock_price
                    // })

                    response += "<tr id='"+formattedName+"'>"+
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

function calculateSales(){

  let num_rows = $("table#inventory tr").length-1
  console.log(num_rows)

  let incipient_sales = {}

  for (let i = 1; i <= num_rows; i++){
    let row = $("table tr:eq("+i+")")
    let name = row.children().eq(name_column_index).text()
    let quantity = parseInt(row.children().eq(quantity_column_index).text())
    let selling_price = parseInt(row.children().eq(selling_price_column_index).text())

    let max_prices = seed_data.filter(item => item.name==name)[0].max_prices
    let popularity_factors = seed_data.filter(item => item.name==name)[0].popularity_factors
    let {popularity, popularity_word, max_buying_price, restock_price} = getSalesSubstrates(popularity_factors, max_prices, trend_calculates)

    let price_disparity = ( (max_buying_price - selling_price)/max_buying_price )*100

    console.log("calculateSales fxn: Popularity of "+name+" is "+popularity)
    // console.log("calculateSales fxn, check variables:", {
    //   name,
    //   quantity,
    //   selling_price,
    //   max_prices,
    //   popularity_factors,
    //   popularity, popularity_word, max_buying_price, restock_price,
    //   price_disparity
    // })



    let sales_percentage = (
      (popularity + (price_disparity*4))/5/100
      )

    let sales_quantity = Math.ceil(sales_percentage * quantity)
  
    if (sales_quantity < 0){
      sales_quantity = 0
    } else if (sales_quantity > quantity){
      sales_quantity = quantity
    }

    console.log("calculateSales fxn: Sell "+ sales_quantity + " from " + quantity + " of " + name)

    let profit = sales_quantity * selling_price

    incipient_sales[name] = {sales_quantity, profit}

  }
  return incipient_sales
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
          // console.log("success")
              if (result['status']) {   
          let fruit = result["data"][0]

              
                let el = $("table#inventory tr td").filter(function() {
                    return $(this).text() == fruit['name']
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
            // console.log("success")
              if (result['status']) {
                let el = $("table#inventory tr td").filter(function() {
                    return $(this).text() == name
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
  console.log(">>>TC proxy:")
  console.log(trend_calculates)
  console.log(">>>Seed data proxy:")
  console.log(seed_data)
}

function getSalesSubstrates(popularity_factors, max_prices, trend_calculates){

  let factor1 = getPopularityFactor(popularity_factors, 0, trend_calculates)
  let factor2 = getPopularityFactor(popularity_factors, 1, trend_calculates)
  let popularity = Math.ceil(( (factor1 * 3) + factor2  ) / 4);

  let popularity_word = popularity > 67 ? "High" : popularity < 33 ? "Low" : "Medium"
  let max_buying_price = max_prices[popularity_word]
  let restock_price = Math.ceil(0.8*max_buying_price)

  return {popularity, popularity_word, max_buying_price, restock_price}
}

function getColumnIndexes(){
  let quantity_column_index = $("table#inventory thead tr th").filter(function(){
    return $(this).text().toLowerCase()=="quantity"
  }).index()

  let selling_price_column_index = $("table#inventory thead tr th").filter(function(){
    return $(this).text().toLowerCase()=="selling price"
  }).index()

  let name_column_index = $("table#inventory thead tr th").filter(function(){
    return $(this).text().toLowerCase()=="name"
  }).index()

  return {quantity_column_index, selling_price_column_index, name_column_index}
}

function getPopularityFactor(pop_factor_names, i, trend_calculates){
  let pop_keys = Object.keys(pop_factor_names)
  return pop_factor_names[pop_keys[i]] ? trend_calculates[pop_keys[i]] : 101-trend_calculates[pop_keys[i]]
}
</script>