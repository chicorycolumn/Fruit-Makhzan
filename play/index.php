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

$show_dev_data = false;

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
echo "<link rel='stylesheet' type='text/css' href='../css/buttons.css' />";
echo "<link rel='stylesheet' type='text/css' href='../css/global.css' />";
echo "<link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Long+Cang&display=swap'>";
include 'content/mainStats.php';
include 'content/mainBulletin.php';
include 'content/mainButton.php';
include 'content/invTable.php';

if ($show_dev_data) {
  $_SESSION['show_dev_data'] = 1;
  $content =
    '<h1> 
  ' .
    $gid .
    '
  </h1>   

  <button onClick="checkSession()">Check Session</button>
  <button onClick="checkTCs()">Check TCs and Seed Data</button>';
} else {
  $_SESSION['show_dev_data'] = 0;
  $content = "";
}

$content .=
  '
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

let day_costs = 0;
let week_record = {}

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
    "conformity_history": parseInt(`<?php print_r(
      ((array) json_decode($_SESSION['trend_calculates']))['conformity_history']
    ); ?>`),
}

fillInvTable()
updateGameStats( //**************** */
  "<?php echo $_SESSION['money_stat']; ?>", 
  "<?php echo $_SESSION['days_stat']; ?>",
  null
)

function newDay() {

  let incipient_sales = calculateSales();
  let day_profit = Object.values(incipient_sales).reduce(
    (sum, obj) => sum + obj.profit,
    0
  );

  let days = parseInt($("#daysStat").text())

  if (days % 7 == 0){week_record = {}}
  week_record[days+1] = {profit: day_profit, costs: day_costs}

  fillQuantityYesterday(incipient_sales); //Moves current quantities to the qy column.
  updateGamesTable(day_profit, "new day", week_record); //Increments Money and Days. Also updates displayed table new Pop and Mxb.
  updateInventoryTable(incipient_sales); //Reduces quantities by sold amounts.

  day_costs = 0
}

function fillQuantityYesterday(incipient_sales) {
  $("table#inventory tbody tr").each(function () {
    let row = $(this)
    
    let qy1 = row.find(".qy1")
    let qy2 = row.find(".qy2")
    let qy3 = row.find(".qy3")

    qy3.text(qy2.text())
    qy2.text(qy1.text())
    qy1.text(incipient_sales[row.find(".nameData").text()]['sales_quantity'] || 0)
  });
}

function updateGamesTable(money_crement, operation, week_record) {
  
  if (operation == "new day") {
    console.log("week_record", week_record)
    $.ajax({
      type: "GET",
      url: "../api/fruit/new_day_supertable.php",
      dataType: "json",
      data: {
        table_name: "games",
        identifying_column: "game_id",
        identifying_data: `<?php echo $_SESSION['gid']; ?>`,
        profit: money_crement,
        week_record: week_record,
        json_column: "overall_sales_history"
      },
      error: function (result) {
        console.log("An error occurred immediately in $.ajax request.", result, result.responseText);
      },
      success: function (result) {

        if (result["status"]) {

          let { money_stat, days_stat, trend_calculates } = result[
            "update_data"
          ];
          updateGameStats(money_stat, days_stat, trend_calculates);
        } else {
          console.log(result["message"], result["error"]);
        }
      },
    });
  } else if ((operation = "restock" || operation == "decrement")) {

    $.ajax({
      type: "GET",
      url: "../api/fruit/update.php",
      dataType: "json",
      data: {
        table_name: "games",
        identifying_column: "game_id",
        identifying_data: `<?php echo $_SESSION['gid']; ?>`,
        acronym: "is",
        update_data: {
          money_stat: parseInt($("#moneyStat").text()) - money_crement,
        },
        should_update_session: true,
      },
      error: function (result) {
        console.log(
          "An error occurred immediately in this $.ajax request.",
          result, result.responseText
        );
      },
      success: function (result) {
        console.log(result);
        if (result["status"]) {

          let { money_stat } = result["update_data"];
          updateGameStats(money_stat);
        } else {
          console.log(result["message"], result["error"]);
        }
      },
    });
  }
}

function updateInventoryTable(incipient_sales) {

  $.ajax({
    type: "POST",
    url: "../api/fruit/new_day_subtable.php",
    dataType: "json",
    data: {
      table_name: `<?php echo $_SESSION['inv_table_name']; ?>`,
      column_to_change: "quantity",
      new_data_key: "sales_quantity",
      identifying_column: "name",
      operation: "decrement",
      data_obj: incipient_sales,
      data_type: "i",
    },
    error: function (result) {
      console.log(
        "An error occurred immediately in $.ajax request.",
        result, result.responseText
      );
    },
    success: function (result) {

      if (result["status"]) {
        let names = Object.keys(result["update_data"]);

        names.forEach((name) => {
          let formattedName = name.replace(/\s/g, "_");
          let row = $("table#inventory tbody tr#"+formattedName)
          let current_quantity = parseInt(
            row.find(".quantityData").text()
          );
          let new_quantity =
            current_quantity -
            parseInt(result["update_data"][name]["sales_quantity"]);
          row.find(".quantityData").text(new_quantity);
        });
        verifyBuyButtons()
      } else {
        console.log(result["message"], result["error"]);
      }
    },
  });
}

function fillInvTable(shouldWipe) {
  if (shouldWipe) {
    $("#inventory tbody tr").remove();
  }
  $.ajax({
    type: "GET",
    url: "../api/fruit/read.php",
    dataType: "json",
    data: {
      table_name: "<?php echo $inv_table_name; ?>",
      get_full: false,
    },
    error: function (result) {
      console.log("An error occurred immediately in $.ajax request.", result, result.responseText);
    },
    success: function (result) {

      if (result["status"]) {
        result["data"].forEach((fruit) => {
          let response = "";
          let {
            id,
            name,
            quantity,
            selling_price,
            resilience,
            max_prices,
            popularity_factors,
          } = fruit;
          let formattedName = name.replace(/\s/g, "_");
          let {
            popularity,
            max_buying_price,
            restock_price,
          } = getSalesSubstrates(
            popularity_factors,
            max_prices,
            trend_calculates
          );
                    response += 
                    "<tr id='"+formattedName+"'>"+


                      "<td class='regularTD nameTD'>"+
                        "<div class='invSubtd nameSubtd'>"+
                          "<p class='invData nameData'>"+name+"</p>"+
                        "</div>"+
                      "</td>"+


                      "<td class='regularTD'>"+
                        "<div class='invSubtd quantitySubtd'>"+
                          "<p class='invData quantityData noMarginPadding'>"+quantity+"</p>"+
                          // "<div class='qyHolder'>"+
                          //   "<div class='qySubHolder qyColor1'><p class='qy qy1 noMarginPadding'> · </p></div>"+
                          //   "<div class='qySubHolder qyColor2'><p class='qy qy2 noMarginPadding'> · </p></div>"+
                          //   "<div class='qySubHolder qyColor3'><p class='qy qy3 noMarginPadding'> · </p></div>"+
                          // "</div>"+
                        "</div>"+
                      "</td>"+
                      
                      
                      "<td class='regularTD sellingPriceTD clickable highlighted' "+
                      
                        "onClick=changeSellingPrice(true,'"+formattedName+"')>"+

                          "<div class='invSubtd sellingPriceSubtd'>"+
                          
                            "<p class='invData sellingPriceData clickable shown'>"+selling_price+"</p>"+
                            
                            "<form class='sellingPriceForm noMarginPadding sellingPriceFormHidden' "+
                              "onsubmit=submitSellingPrice(`"+formattedName+"`) "+
                              "onfocusout=changeSellingPrice(false,'"+formattedName+"')>"+
                                
                              "<textarea class='sellingPriceInput noMarginPadding' "+
                              "onkeypress='return validateNumbersAndSubmit(event,`"+formattedName+"`,`selling`)' "+
                              "maxlength=10 maxlength=10 type='text'>"+"</textarea>"+
                              
                              "<button type='submit' class='mediumButtonKind sellingPriceButton noMarginPadding' "+
                              "onclick=submitSellingPrice(`"+formattedName+"`)>OK"+
                              "</button>"+
                            
                            "</form>"+

                          "</div>"+
                      
                      "</td>"+
                      

                      "<td class='regularTD'>"+
                        "<div class='invSubtd restockPriceSubtd'>"+
                          "<div class='popHolder'>"+
                            "<p class='invData restockPriceData'>"+restock_price+"</p>"+
                            "<div class='popularityCircleSpan'></div>"+
                          "</div>"+
                        "</div>"+
                      "</td>"+
                      

                      "<td class='regularTD' style='cursor:help;' "+                       
                      "onClick=printSingle('"+formattedName+"')>"+
                        
                        "<div class='invSubtd resilienceSubtd'>"+
                          "<p class='invData resilienceData'>"+resilience+"</p>"+
                        "</div>"+
                      
                      "</td>"+


                      "<td class='buttonTD'>"+
                          
                          "<div class='buttonSuperHolder'>"+
                          printDevDataHTML(popularity, max_buying_price)+
                            
                            "<div class='buttonSubHolder'>"+
                              
                              "<button "+
                              "id='buyButton' class='mediumButtonKind button2 buyButton'"+
                              "onClick=restockFruit('"+formattedName+"')>Buy"+
                              "</button>"+            
                              
                              "<input value="+seed_data.filter(item => item['name']==name)[0]['restock_amount']+" "+
                                "class='amountInput amountInput_restock' "+
                                "onclick=this.select() "+
                                "onkeyup='return validateNumbersAndSubmit(event,`"+formattedName+"`,`restock`)' "+
                                "onblur=setAmount('"+formattedName+"','restock') "+
                                "maxlength=10>"+
                            
                            "</div>"+ 
                            
                            "<div class='buttonSubHolder'>"+
                              "<button id='maxBuyButton' class='mediumButtonKind button3' "+
                                "onclick=setAmount('"+formattedName+"','restock','max') "+
                              ">MAX</button>"+
                              "<button id='incBuyButton' class='mediumButtonKind button4' "+
                                "onclick=setAmount('"+formattedName+"','restock','increment') "+
                              ">⇧</button>"+
                              "<button id='decBuyButton' class='mediumButtonKind button4' "+
                                "onclick=setAmount('"+formattedName+"','restock','decrement') "+
                              ">⇩</button>"+
                              
                            "</div>"+                            
                          
                          "</div>"+
                      
                      "</td>"+
                    
                    "</tr>";
                  
                    $(response).appendTo($("#inventory"));              
        });
                    setTimeout(() => {
                      bindUsefulJqueriesAfterLoadingDataIntoTable()
                    }, 500);
                    
                    updateSalesSubstratesInDisplayedTable()
                    verifyBuyButtons()



      } else {
        console.log(result["message"], result["error"]);
      }
    },
  });
}

function verifyBuyButtons(){

  $(".buyButton").each(function(){

    let row = $(this).parents("tr")
    let name = row.find(".nameData").text()
    let restockPrice = parseInt(row.find(".restockPriceData").text())
    let restockQuantity = parseInt(row.find(".amountInput_restock").val())
    let maxBuyableQuantity = Math.floor(parseInt($("#moneyStat").text()) / restockPrice)
    $(this).prop("disabled", restockQuantity > maxBuyableQuantity || !restockQuantity)
  })
}

function setAmount(formattedName, operation, modifier, forced_amount) {

  name = formattedName.replace(/_/g, " ");

  let row = $("table#inventory tbody tr#"+formattedName)

  let class_name = ".amountInput" + "_" + operation;
  let quantity = parseInt(row.find(".quantityData").text());
  let restock_amount = parseInt(row.find(".amountInput_restock").val())
  let restock_price = parseInt(row.find(".restockPriceData").text())
  let throw_amount = parseInt(row.find(".amountInput_throw").val())
  let max_buyable_quantity = Math.floor(parseInt($("#moneyStat").text()) / restock_price)
  let key = operation + "_amount";
  let reset_value = restock_amount
  if (forced_amount && operation == "restock"){
    restock_amount = forced_amount
  } if (forced_amount && operation == "throw"){
    restock_amount = throw_amount
  } if (operation == "restock" && (!restock_amount || !parseInt(restock_amount)) || operation == "throw" && (!throw_amount || !parseInt(throw_amount))) {
    reset_value = 1;
  } if (operation == "throw" && parseInt(throw_amount) > quantity) {
    reset_value = quantity || 1;
  } if (operation == "restock" && modifier && modifier == "max") {
    reset_value = max_buyable_quantity || 1
  } if (operation == "restock" && modifier && modifier == "increment") {
    reset_value = restock_amount < max_buyable_quantity ? restock_amount + 1 || 1 : restock_amount
  } if (operation == "restock" && modifier && modifier == "decrement") {
    reset_value = restock_amount - 1 || 1
  }
  seed_data.filter((item) => item["name"] == name)[0][key] = reset_value;
  row.find(class_name).val(reset_value);

  verifyBuyButtons() 
}

function validateNumbersAndSubmit(e, formattedName, operation){

  verifyBuyButtons()

  let k = e.keyCode
  let w = e.which

  if (k == 13 || w == 13){
    
    if (operation == "restock"){
      restockFruit(formattedName)
  } else if (operation == "selling"){
    submitSellingPrice(formattedName)
  }
  
  }

  function checkKey(key){
    return key == 13 || (key >= 48 && key <= 57)
  }

  return checkKey(k) || checkKey(w) 
}

function submitSellingPrice(formattedName){
    event.preventDefault()

    name = formattedName.replace(/_/g, " ");
    let row = $("table#inventory tbody tr#"+formattedName)
    let span = row.find(".sellingPriceData");
    let span_text = span.text();
    let form = row.find(".sellingPriceForm");
    let input = row.find(".sellingPriceInput");
    let button = row.find(".sellingPriceButton");

    let putative_price = input.val();

  if (!
  putative_price || !parseInt(putative_price)) {
    input.val("");
    form.addClass("sellingPriceFormHidden");
    span.removeClass("hidden");
    return;
  }

  if (putative_price.match(/^\d+$/)) {
    $.ajax({
      type: "GET",
      url: "../api/fruit/update.php",
      dataType: "json",
      data: {
        name: name,
        table_name: "<?php echo $inv_table_name; ?>",
        identifying_column: "name",
        identifying_data: name,
        update_data: {
          selling_price: putative_price,
        },
        acronym: "is",
        should_update_session: 0,
      },
      error: function (result) {
        console.log("An error occurred immediately in $.ajax request.", result.responseText, result);
      },
      success: function (result) {
        if (result["status"]) {
          input.val("");
          form.addClass("sellingPriceFormHidden");
          span.removeClass("hidden");
          span.text(result["update_data"]["selling_price"]);
        } else {
          console.log(result["message"], result["error"]);
        }
      },
    });
  }
}

function changeSellingPrice(showInput, formattedName) {
  name = formattedName.replace(/_/g, " ");

  let row = $("table#inventory tbody tr#"+formattedName)
  let span = row.find(".sellingPriceData");
  let span_text = span.text();
  let form = row.find(".sellingPriceForm");
  let input = row.find(".sellingPriceInput");
  let button = row.find(".sellingPriceButton");

  if (!showInput){
      setTimeout(() => {
    span.removeClass("hidden");
    form.addClass("sellingPriceFormHidden");
      }, 200);
  }

  if (showInput && !form.find(":focus").length){
    span.addClass("hidden");
    form.removeClass("sellingPriceFormHidden");
    input.val(span_text)
    input.focus();
    input.select();
  }
}

function bindUsefulJqueriesAfterLoadingDataIntoTable(){
  $('.buttonSubHolder').bind('mousewheel', function(e){
        
        let current_val = parseInt($(this).find("input").val())
        let max_buyable_quantity = Math.floor(parseInt($("#moneyStat").text()) / parseInt($(this).parents("tr").find(".restockPriceData").text()))

        if(e.originalEvent.wheelDelta/120 > 0) {
          current_val < max_buyable_quantity && $(this).find("input").val(current_val+1)
        }
        else{
          current_val > 1 && $(this).find("input").val(current_val-1)
        }
  });
}

function printDevDataHTML(popularity, max_buying_price){

  let show = <?php echo $_SESSION['show_dev_data']; ?>

  if (show){
    return "<p class='devdata1'>P"+popularity+"  M"+max_buying_price+"</p>"
  }else{
    return ""
  }
}

function restockFruit(formattedName) {
  name = formattedName.replace(/_/g, " ");

  let row = $("table#inventory tbody tr#"+formattedName)

  let requested_amount = parseInt(row.find(".amountInput_restock").val());

  if (!requested_amount){return}

  let restock_price = parseInt(
    row.find(".restockPriceData").text()
  );
  let putative_cost = requested_amount * restock_price;
  let money = parseInt($("#moneyStat").text());

  setAmount(formattedName, "restock", "", requested_amount);

  if (putative_cost > money) {
    console.log("insuff funds")
    return
  } else {
    $.ajax({
      type: "GET",
      url: "../api/fruit/restock.php",
      dataType: "json",
      data: {
        name: name,
        table_name: "<?php echo $inv_table_name; ?>",
        increment: requested_amount,
      },
      error: function (result) {
        console.log("An error occurred immediately in $.ajax request.", result.responseText, result);
      },
      success: function (result) {
        day_costs += putative_cost
        if (result["status"]) {
          let fruit = result["data"][0];

          let row = $("table#inventory tbody tr#"+formattedName)
          row.find(".quantityData").text(fruit["quantity"]);

          let maxPossibleToBuy = Math.floor(
            (money - putative_cost) / restock_price
          );

          if (requested_amount > maxPossibleToBuy) {
            let reset_value = maxPossibleToBuy || 1;

            row.find(".amountInput_restock").val(reset_value);
            seed_data.filter((item) => item["name"] == name)[0][
              "restock_amount"
            ] = reset_value;
          }
          updateGamesTable(putative_cost, "decrement", null); //Send off the db to change money stat.
          
        } else {
          console.log(result["message"], result["error"]);
        }
      },
    });
  }
}

function calculateSales() {
  let incipient_sales = {};

  $("table#inventory tbody tr").each(function () {
    let row = $(this);
    let name = row.find(".nameData").text();
    let quantity = parseInt(row.find(".quantityData").text());
    let selling_price = parseInt(
      row.find(".sellingPriceData").text()
    );

    let max_prices = seed_data.filter((item) => item.name == name)[0]
      .max_prices;
    let popularity_factors = seed_data.filter((item) => item.name == name)[0]
      .popularity_factors;
    let { popularity, max_buying_price, restock_price } = getSalesSubstrates(
      popularity_factors,
      max_prices,
      trend_calculates
    );

    let price_disparity =
      ((max_buying_price - selling_price) / max_buying_price) * 100;

    let sales_percentage = (popularity + price_disparity * 4) / 5 / 100;

    let sales_quantity = Math.ceil(sales_percentage * quantity);

    if (sales_quantity < 0) {
      sales_quantity = 0;
    } else if (sales_quantity > quantity) {
      sales_quantity = quantity;
    }

    let profit = sales_quantity * selling_price;

    incipient_sales[name] = { sales_quantity, profit };
  });

  return incipient_sales;
}

function printSingle(name) {
  name = name.replace(/_/g, " ");
  $.ajax({
    type: "GET",
    url: "../api/fruit/read_single.php",
    dataType: "json",
    data: {
      table_name: "<?php echo $inv_table_name; ?>",
      identifying_column: "name",
      identifying_data: name,
      acronym: "s",
      get_full: false,
    },
    error: function (result) {
      console.log("An error occurred immediately in $.ajax request.", result, result.responseText);
    },
    success: function (result) {
      if (result["status"]) {
        console.log(result);
      } else {
        console.log(result, result["message"], result["error"]);
    }},
  });
}

function checkSession() {
  console.log(">>>Old session is:");
  console.log(`<?php print_r($_SESSION); ?>`);
}

function checkTCs() {
  console.log(">>>TC proxy:", trend_calculates);
  console.log(">>>Seed data proxy:", seed_data);
}

function getSalesSubstrates(popularity_factors, max_prices, trend_calculates) {
  let factor1 = getPopularityFactor(popularity_factors, 0, trend_calculates);
  let factor2 = getPopularityFactor(popularity_factors, 1, trend_calculates);
  let popularity = Math.ceil((factor1 * 3 + factor2) / 4);

  let popularity_word =
    popularity > 67 ? "High" : popularity < 33 ? "Low" : "Medium";
  let max_buying_price = max_prices[popularity_word];
  let restock_price = Math.ceil(0.8 * max_buying_price);

  return { popularity, max_buying_price, restock_price };
}

function getColumnIndexes() {
  function getIndex(match) {
    return $("table#inventory thead tr th")
      .filter(function () {
        return $(this).text().toLowerCase() == match;
      })
      .index();
  }

  let columnIndexRef = {};

  let labels = ["quantity", "selling price", "name", "restock price"];

  labels.forEach((label) => {
    columnIndexRef[label] = getIndex(label);
  });

  return columnIndexRef;
}

function updateGameStats(new_money_stat, new_days_stat, new_trend_calculates) {
  $("#moneyStat").text(new_money_stat);

  if (new_days_stat) {
    $("#daysStat").text(new_days_stat);
  }

  verifyBuyButtons()

  if (new_trend_calculates) {
    trend_calculates = new_trend_calculates;
    updateSalesSubstratesInDisplayedTable();
  }
}

function updateSalesSubstratesInDisplayedTable() {
  $("table#inventory tbody tr").each(function () {
    let row = $(this)
    let name = row.find(".nameData").text();

    let max_prices = seed_data.filter((item) => item.name == name)[0].max_prices;
    let popularity_factors = seed_data.filter(
      (item) => item.name == name
    )[0].popularity_factors;
    let { popularity, max_buying_price, restock_price } = getSalesSubstrates(
      popularity_factors,
      max_prices,
      trend_calculates
    );

    row.find(".devdata1")
      .text("P" + popularity + "  M" + max_buying_price);

    row.find(".popularityCircleSpan")
      .text(getPopularityColor(popularity).text)
      .css({"background-color": getPopularityColor(popularity).color})

      function getPopularityColor(pop){
        if (pop < 20){
          return {text: "⇊", color: "red"}
        } else if (pop < 40){
          return {text: "↓", color: "orange"}
        }  else if (pop < 60){
          return {text: "·", color: "yellow"}
        }  else if (pop < 80){
          return {text: "↑", color: "greenyellow"}
        }  else if (pop >= 80){
          return {text: "⇈", color: "cyan"}
        } 
      }

    row.find(".restockPriceData")
      .text(restock_price);
  });
}

function getPopularityFactor(pop_factor_names, i, trend_calculates) {
  let pop_keys = Object.keys(pop_factor_names);
  return pop_factor_names[pop_keys[i]]
    ? trend_calculates[pop_keys[i]]
    : 101 - trend_calculates[pop_keys[i]];
}

function throwFruit(formattedName) {
  name = formattedName.replace(/_/g, " ");

  let columnIndexRef = getColumnIndexes();

  let row = $("table#inventory tbody tr").filter(function () {
    return $(this).find(".nameData").text() == name;
  });

  let throw_amount = parseInt(row.find(".amountInput_throw").val());
  let quantity = parseInt(row.find(".quantityData").text());

  if (!quantity) {
    return;
  }

  setAmount(formattedName, "throw", "", throw_amount);

  if (throw_amount <= quantity) {
    if (
      throw_amount > 0.2 * quantity &&
      !confirm("Chuck " + throw_amount + " " + name + " into the street?")
    ) {
      return;
    }

    $.ajax({
      type: "GET",
      url: "../api/fruit/restock.php",
      dataType: "json",
      data: {
        name: name,
        table_name: "<?php echo $inv_table_name; ?>",
        increment: "-" + throw_amount.toString(),
      },
      error: function (result) {
        console.log("An error occurred immediately in $.ajax request.", result.responseText, result);
      },
      success: function (result) {
        if (result["status"]) {

          let fruit = result["data"][0];
          let row = $("table#inventory tbody tr#"+formattedName)
          row.find(".quantityData").text(fruit["quantity"]);

          if (throw_amount > parseInt(fruit["quantity"])) {
            let reset_value = parseInt(fruit["quantity"]) || 1;
            row.find(".amountInput_throw").val(reset_value);
            seed_data.filter((item) => item["name"] == name)[0][
              "restock_amount"
            ] = reset_value;
          }
        } else {
          console.log(result["message"], result["error"]);
        }
      },
    });
  }
}
</script>