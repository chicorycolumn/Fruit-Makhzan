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
$_SESSION['show_dev_data'] = 0;

setcookie("makhzan", $_SESSION['gid'], time() + 3600 * 24 * 30, "/");
$gid = $_SESSION['gid'];
$inv_table_name = $_SESSION['inv_table_name'];

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

include 'functions/includes.php';
include '../master.php';
?>

<script>

let day_costs = 0;
let week_record = {};
let level_record = JSON.parse(`<?php echo $_SESSION['level_record']; ?>`);
let current_rubicon = 0;
updateCurrentRubicon();


let envi = JSON.parse(`<?php echo json_encode(getenv()); ?>`)

console.log({envi})


//'

let sessionMoney = "<?php echo $_SESSION['money_stat']; ?>"
let sessionDays = "<?php echo $_SESSION['days_stat']; ?>"
let trend_calculates = JSON.parse(`<?php print_r(
  $_SESSION['trend_calculates']
); ?>`)

fillInvTable()
updateGameStats(sessionMoney, sessionDays, null)

$(document).ready(function () {
  setZoom()
  loadRubiconIfAt()
});

function newDay() {
  let incipient_sales = calculateSales();
  let day_profit = Object.values(incipient_sales).reduce(
    (sum, obj) => sum + obj.profit,
    0
  );

  let days = dayGrouping($("#daysStat").text(), $("#yearsStat").text(), true);

  let money = digitGrouping($("#moneyStat").text(), true);

  if (days % 7 == 0) {
    week_record = {};
  }
  week_record[days + 1] = { profit: day_profit, costs: day_costs };

  let new_money_stat = money + day_profit;

  let data_object = { overall_sales_history: week_record };

  updateGamesTableNewDay(day_profit, data_object);
  updateInventoryTable(incipient_sales);

  day_costs = 0;

  if (level_record["round"] < level_record["final_round"]) {
    if (new_money_stat >= rubicons[2]) {
      incrementSublevel(rubiconMessageRef, 0);
    } else if (
      parseFloat(level_record["sublevel"]) < 1 &&
      new_money_stat >= rubicons[1]
    ) {
      incrementSublevel(rubiconMessageRef, 1);
    }
  } else if (
    level_record["round"] >= level_record["final_round"] &&
    new_money_stat >= rubicons[2]
  ) {
    incrementSublevel(rubiconMessageRef, 4);
  }
}

function updateGamesTableNewDay(profit, data_object) {

  $.ajax({
    type: "POST",
    url: "../api/fruit/new_day_supertable.php",
    dataType: "json",
    data: {
      table_name: "games",
      identifying_column: "game_id",
      identifying_data: `<?php echo $_SESSION['gid']; ?>`,
      profit,
      json_data_object: data_object, //week_record
      level_record,
    },
    error: function (result) {
      console.log(
        "A kind of error which occurred immediately in $.ajax request.",
        result,
        result.responseText
      );
    },
    success: function (result) {
      if (result["status"]) {
        let { money_stat, days_stat, trend_calculates } = result["update_data"];
        updateGameStats(money_stat, days_stat, trend_calculates);
      } else {
        console.log(result["message"], result["error"], result);
      }
    },
  });
}

function updateGamesTable(money_crement, money_absolute, new_level_record) {
  let acronym;
  let update_data;

  if (new_level_record) {
    acronym = "ss";
    update_data = {
      level_record: new_level_record,
    };
  } else {
    let new_money_stat = 666;

    if (money_absolute) {
      new_money_stat = money_absolute;
    } else {
      new_money_stat = digitGrouping($("#moneyStat").text(), true) - money_crement;
    }

    acronym = "is";
    update_data = {
      money_stat: new_money_stat,
    };
  }

  $.ajax({
    type: "POST",
    url: "../api/fruit/update.php",
    dataType: "json",
    data: {
      table_name: "games",
      identifying_column: "game_id",
      identifying_data: `<?php echo $_SESSION['gid']; ?>`,
      acronym,
      update_data,
      should_update_session: true,
    },
    error: function (result) {
      console.log(
        "An error that occurred immediately in this $.ajax request.",
        result,
        result.responseText
      );
    },
    success: function (result) {
      if (result["status"]) {
        
        if (!new_level_record){ updateGameStats(result["update_data"]['money_stat']);}
       
      } else {
        console.log(result["message"], result["error"], result);
      }
    },
  });
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
        "An error, which occurred immediately in $.ajax request.",
        result,
        result.responseText
      );
    },
    success: function (result) {
      if (result["status"]) {
        let names = Object.keys(result["update_data"]);

        names.forEach((name) => {
          let formattedName = name.replace(/\s/g, "_");
          let row = $("table#inventory tbody tr#" + formattedName);
          let current_quantity = digitGrouping(row.find(".quantityData").text(), true);
          let new_quantity =
            current_quantity -
            parseInt(result["update_data"][name]["sales_quantity"]);

          let formattedNewQuantity = digitGrouping(new_quantity)

          if (parseInt(formattedNewQuantity) < 0 || (/-/).test(formattedNewQuantity)){
            console.log("WOAH! the new quantity is negative.")
            formattedNewQuantity = "0"
          }

          row.find(".quantityData").text(formattedNewQuantity);
        });
        verifyBuyButtons();
      } else {
        console.log(result["message"], result["error"], result);
      }
    },
  });
}

function fillInvTable(shouldWipe, name) {

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
      console.log(
        "An error, it has occurred immediately in $.ajax request.",
        result,
        result.responseText
      );
    },
    success: function (result) {
      if (result["status"]) {
        if (name) {
          result["data"]
            .filter((fruit) => fruit["name"] == name)
            .forEach((fruit) => {
              addRowToTable(fruit, true);
            });
        } else {
          result["data"].forEach((fruit) => {
            addRowToTable(fruit);
          });
        }

        setTimeout(() => {
          bindUsefulJqueriesAfterLoadingDataIntoTable();
        }, 500);

        hideSpecificRows();
        updateSalesSubstratesInDisplayedTable();
        verifyBuyButtons();
      } else {
        console.log(result["message"], result["error"], result);
      }
    },
  });
}

function addRowToTable(fruit, shouldPrepend){
  let response = "";
          let {
            name,
            quantity,
            selling_price,
            max_prices,
            popularity_factors,
            rubicon
          } = fruit;
          let formattedName = name.replace(/\s/g, "_");
          max_prices = integeriseObjectValues(max_prices)
          let {
            popularity,
            max_buying_price,
            restock_price,
          } = getSalesSubstrates(
            popularity_factors,
            max_prices,
            trend_calculates,
            name
          );
          
          let pop_factor_keys = Object.keys(popularity_factors)
          let pf1 = pop_factor_keys[0]
          let pf2 = pop_factor_keys[1]

          for (let key in popularity_factors){
            if (popularity_factors[key] == "false"){popularity_factors[key] = false}else
            if (popularity_factors[key] == "true"){popularity_factors[key] = true} 
          }

          pf1 = "<p class='popFactor1 noMarginPadding"+(popularity_factors[pf1]?" popFactorPositive":" popFactorNegative")+"'>"+(popularity_factors[pf1]?"":"↻")+pf1+"</p>"
          pf2 = "<p class='popFactor2 noMarginPadding"+(popularity_factors[pf2]?" popFactorPositive":" popFactorNegative")+"'>"+(popularity_factors[pf2]?"":"↻")+pf2+"</p>"

                    response += 
                    "<tr id='"+formattedName+"'>"+


                      "<td class='regularTD nameTD'>"+
                        "<div class='invSubtd nameSubtd'>"+
                          "<p class='invData nameData'>"+name+"</p>"+
                          "<p class='invData hiddenData rubiconData'>"+rubicon+"</p>"+
                          "<p class='invData hiddenData popFactorsData'>"+JSON.stringify(popularity_factors)+"</p>"+
                          "<p class='invData hiddenData maxPricesData'>"+JSON.stringify(max_prices)+"</p>"+
                        "</div>"+
                      "</td>"+


                      "<td class='regularTD'>"+
                        "<div class='invSubtd quantitySubtd'>"+
                          "<p class='invData quantityData noMarginPadding'>"+digitGrouping(quantity)+"</p>"+
                        "</div>"+
                      "</td>"+
                      
                      
                      "<td class='regularTD sellingPriceTD clickable highlighted' "+
                      
                        "onClick=changeSellingPrice(true,'"+formattedName+"')>"+

                          "<div class='invSubtd sellingPriceSubtd'>"+
                          
                            "<p class='invData sellingPriceData clickable shown'>"+digitGrouping(selling_price)+"</p>"+
                            
                            "<form class='sellingPriceForm noMarginPadding sellingPriceFormHidden' "+
                              "onsubmit=submitSellingPrice(`"+formattedName+"`) "+
                              "onfocusout=changeSellingPrice(false,'"+formattedName+"')>"+
                                
                              "<textarea class='sellingPriceInput noMarginPadding' "+
                              "onkeydown='return validateNumbersAndSubmit(event,`"+formattedName+"`,`selling`)' "+
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
                            "<p class='invData restockPriceData'>"+digitGrouping(restock_price)+"</p>"+
                            "<div class='popularityCircleSpan'></div>"+
                          "</div>"+
                        "</div>"+
                      "</td>"+
                      

                      "<td class='regularTD' style='cursor:help;' "+                       
                      "onclick=printSingle('"+formattedName+"')>"+
                        
                        "<div class='invSubtd factorsSubtd'>"+
                          pf1+pf2+
                        "</div>"+
                      
                      "</td>"+


                      "<td class='buttonTD'>"+
                          
                          "<div class='buttonSuperHolder'>"+
                            
                            "<div class='buttonSubHolder'>"+        
                              
                              "<input value=1 "+
                                "class='amountInput amountInput_restock' "+
                                "onclick=this.select() "+
                                "onkeydown='return validateNumbersAndSubmit(event,`"+formattedName+"`,`restock`)' "+
                                "onblur=setAmount('"+formattedName+"','restock') "+
                                "maxlength=10>"+
                            
                            "</div>"+ 
                            
                            "<div class='buttonSubHolder'>"+
                              "<button "+
                              "class='mediumButtonKind buyButton'"+
                              "onClick=restockFruit('"+formattedName+"')>BUY"+
                              "</button>"+    
                              "<button class='mediumButtonKind maxBuyButton' "+
                                "onclick=setAmount('"+formattedName+"','restock','max') "+
                              ">MAX</button>"+
                              "<button class='mediumButtonKind buttonCrement incBuyButton' "+
                                "onclick=setAmount('"+formattedName+"','restock','increment') "+
                              ">⇧</button>"+
                              "<button class='mediumButtonKind buttonCrement decBuyButton' "+
                                "onclick=setAmount('"+formattedName+"','restock','decrement') "+
                              ">⇩</button>"+
                              
                            "</div>"+                            
                          
                          "</div>"+
                      
                      "</td>"+
                    
                    "</tr>";

                    if (shouldPrepend) {
                      $(response).prependTo($("#inventory"));
                      let newRow = $("table#inventory tbody tr#" + formattedName);
                      makeSparkly(newRow);
                    } else {
                      $(response).appendTo($("#inventory"));
                    }             
}

function restockFruit(formattedName) {
  name = formattedName.replace(/_/g, " ");

  let row = $("table#inventory tbody tr#" + formattedName);

  let requested_amount = digitGrouping(row.find(".amountInput_restock").val(), true);

  if (!requested_amount) {
    return;
  }

  let restock_price = digitGrouping(row.find(".restockPriceData").text(), true);
  let putative_cost = requested_amount * restock_price;
  let money = digitGrouping($("#moneyStat").text(), true);

  setAmount(formattedName, "restock", "", requested_amount);

  if (putative_cost > money) {
    alert("Insufficient funds!");
    return;
  } else {
    $.ajax({
      type: "POST",
      url: "../api/fruit/restock.php",
      dataType: "json",
      data: {
        name,
        table_name: "<?php echo $inv_table_name; ?>",
        increment: requested_amount,
      },
      error: function (result) {
        console.log(
          "An error which has occurred immediately in $.ajax request.",
          result.responseText,
          result
        );
      },
      success: function (result) {
        day_costs += putative_cost;
        if (result["status"]) {
          let fruit = result["data"][0];

          let row = $("table#inventory tbody tr#" + formattedName);
          row.find(".quantityData").text(digitGrouping(fruit["quantity"]));

          let maxPossibleToBuy = Math.floor(
            (money - putative_cost) / restock_price
          );

          if (requested_amount > maxPossibleToBuy) {
            let reset_value = maxPossibleToBuy || 1;

            row.find(".amountInput_restock").val(digitGrouping(reset_value));
          }
          updateGamesTable(putative_cost);
        } else {
          console.log(result["message"], result["error"], result);
        }
      },
    });
  }
}

function updateGameStats(new_money_stat, new_days_stat, new_trend_calculates) {
  $("#moneyStat").text(digitGrouping(new_money_stat));

  if (new_days_stat) {

    let {days, years} = dayGrouping(new_days_stat)

    $("#daysStat").text(days);
    $("#yearsStat").text(years);
  }

  verifyBuyButtons();

  if (new_trend_calculates) {
    trend_calculates = new_trend_calculates;
    updateSalesSubstratesInDisplayedTable();
  }
}

</script>