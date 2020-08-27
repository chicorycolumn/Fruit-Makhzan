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
echo "<link rel='stylesheet' type='text/css' href='../css/buttons.css' />";
echo "<link rel='stylesheet' type='text/css' href='../css/global.css' />";
echo "<link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Merienda&display=swap'>";
include 'content/mainStats.php';
include 'content/mainBulletin.php';
include 'content/invTable.php';
include 'content/mainGraphs.php';
include 'functions/f1.php';
include 'functions/f2.php';

if ($show_dev_data) {
  $_SESSION['show_dev_data'] = 1;
  $content =
    '<h1> 
  ' .
    $gid .
    '
  </h1>   

  <button onClick="printSingle(null)">Check Game Data</button>';
} else {
  $_SESSION['show_dev_data'] = 0;
  $content = "";
}

$content .=
  '
  <div class="dialogHolder hidden">
    <img class="scroll1" src=".././images/scroll1.png">
    <div class="dialogBox">
      <div class="dialogBoxInner">
        <div class="dialogBoxInnerInner">
          <p class=dialogBoxText></p>
        </div>
      </div>
      <p class="dialogBoxButton" onClick=advance()>☞Very well</p>
    </div>
  </div>

  <div class="mainDiv mainDivStats">
    ' .
  $mainStats .
  '
  </div>

  <div class="holderForHorizontalMainDivs">

    <div class="mainDiv mainDivGraphs">
    ' .
  $mainGraphs .
  '
    </div>

    <div class="mainDiv mainDivBulletin">
      ' .
  $mainBulletin .
  '
    </div>

  </div>
    
  <div class="mainDiv mainDivTable">
    ' .
  $invTable .
  '
  </div>
  ';

include '../master.php';
?>

<script>


let createFruitForm ='<form class="createFruitForm">' +
    
    '<div class="boxBody">' +
      
      '<div class="formGroup createFruitFormGroup">' +
      '<label class="createFruitLabel noMarginPadding">Name of brand new fruit:</label>'+
        '<span><input type="text" class="formControl createFruitInputName" id="name" maxlength=20' +
        'onkeypress="return /[0-9a-zA-Z ]/.test(event.key)" ></span>' +
      "</div>" +
    
      '<div class="formGroup createFruitFormGroup">' +
        "<label class='createFruitLabel'>Which two will affect its popularity?</label>" +
        '<div class="formControl factorsHolder">' +
          
          '<span id="love" class="factorSelect factorDimensions" '+
            'onMouseUp="return selectFactor(event,`Love`)" '+
          '>Love</span>'+

          '<span id="politics" class="factorSelect factorDimensions" '+
            'onMouseUp="return selectFactor(event,`Politics`)" '+
          '>Politics</span>'+

          '<span id="weather" class="factorSelect factorDimensions" '+
            'onMouseUp="return selectFactor(event,`Weather`)" '+
          '>Weather</span>'+

          '<span id="conformity" class="factorSelect factorDimensions" '+
            'onMouseUp="return selectFactor(event,`Conformity`)" '+
          '>Conformity</span>'+

          '<span id="decadence" class="factorSelect factorDimensions" '+
            'onMouseUp="return selectFactor(event,`Decadence`)" '+
          '>Decadence</span>'+
          
        '</div>'+
      "</div>" +
    
    "</div>" +
  
"</form>";

let day_costs = 0;
let week_record = {};
const messageRef = {
  1: "Wahad! You reached sublevel 1!",
  0: "You're a billionaire! As a reward for all your hard work, you buy an island to relax on.",
  4: "You won the whole game! You own five islands and are now king.",
};
let level_record;

level_record = JSON.parse(`<?php echo $_SESSION['level_record']; ?>`);

const rubicons = { 1: 150, 3: 300 };
// const rubicons = {1: 10000, 3: 1000000000}
let current_rubicon = 0;
updateCurrentRubicon();

//'

if (level_record["round"] > level_record["final_round"]) {
  showEndScreen(4);
}

for (let i = 1; i <= level_record["round"]; i++) {
  showIsland(i);
}

$(document).ready(function () {
      // let document = $(document)
    let scale = '0.5';
    document.body.style.zoom = 0.8
  document.body.style.webkitTransform =  scale; 
  document.body.style.msTransform =   scale;    
  document.body.style.transform = scale;

  // console.log("READY: " + level_record['round'] + "~" + level_record['sublevel'])
  for (let key in level_record) {
    let days = dayGrouping($("#daysStat").text(), $("#yearsStat").text(), true);
    if (parseInt(key) == days && parseFloat(level_record["sublevel"]) < 0.9) {
      // console.log("I see we are on day " + key + " which was a rubicon day! It was where we entered " + level_record[key]['round'] + "." + level_record[key]['sublevel'] + " so we should load that somehow.")
      allButtonsDisabled(true);
      $(".dialogHolder").removeClass("hidden");
      $(".dialogHolder")
        .find(".dialogBoxText")
        .text(messageRef[level_record[key]["sublevel"]]);

      if (level_record["round"] == level_record["final_round"] + 1) {
        showIsland();
        $(".newDayButton").addClass("hidden");
        $(".crown").removeClass("hidden");
        $(document).ready(function () {
          allButtonsDisabled(true);
        });
      }
    }
  }
});

function showCreateFruitForm() {
  // console.log("showCreateFruitForm fxn")
  $(".dialogHolder").removeClass("hidden");
  $(".dialogHolder").find(".dialogBoxText").html(createFruitForm);
  $(".factorSelect").each(function () {
    $(this).bind("contextmenu", function (e) {
      return false;
    });
  });
}

function updateCurrentRubicon() {
  let round = level_record["round"];
  let sublevel = level_record["sublevel"];

  for (let i = 1; i <= 3; i++) {
    if ((round >= i - 1 && sublevel >= 1) || round >= i) {
      current_rubicon = i;
    }
  }

  revealSpecificRows();
}

function selectFactor(e, label) {
  function toggleNegation(item, negate) {
    if (negate) {
      item.addClass("factorNegated");
      item.text("↻" + item.text());
    } else {
      item.removeClass("factorNegated");
      item.text(item.text().replace("↻", ""));
    }
  }

  let factor = $("#" + label.toLowerCase());

  if (e.which == 3 || e.button == 2) {
    if (factor.hasClass("factorMajor") || factor.hasClass("factorMinor")) {
      if (factor.hasClass("factorNegated")) {
        toggleNegation(factor, false);
      } else {
        toggleNegation(factor, true);
      }
      return;
    }
    return;
  }

  if (!factor.hasClass("factorMajor") && !factor.hasClass("factorMinor")) {
    $(".factorSelect").each(function () {
      if ($(this).hasClass("factorMinor")) {
        $(this).removeClass("factorMinor");
        $(this).addClass("factorDimensions");
        toggleNegation($(this), false);
      }
    });

    factor.removeClass("factorDimensions");
    factor.addClass("factorMinor");
  } else if (factor.hasClass("factorMinor")) {
    $(".factorSelect").each(function () {
      if ($(this).hasClass("factorMajor")) {
        $(this).removeClass("factorMajor");
        toggleNegation($(this), false);
        $(this).addClass("factorDimensions");
      }
    });

    factor.removeClass("factorDimensions");
    factor.removeClass("factorMinor");
    factor.addClass("factorMajor");
  } else if (factor.hasClass("factorMajor")) {
    factor.removeClass("factorMajor");
    factor.removeClass("factorMinor");
    toggleNegation(factor, false);
    factor.addClass("factorDimensions");
  }
}

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

function dayGrouping(days, years, ungroup){

  days = parseInt(days)

  if (ungroup){return (parseInt(years)*365)+days};

  years = 0

  while (days >= 365){
    days-=365
    years++
  }

  return {days, years}
}

function digitGrouping(num, ungroup){

  if (num == ""){
    return num
  }

  if (ungroup){
    return parseInt(num.replace(/\s/g, ""))
  }

  num = num.toString()
	if (num.length <= 3){return num}else{
	return digitGrouping(num.slice(0, num.length-3)) + " " + num.slice(-3)
	}
}

function newDay() {
  // console.log("NEWDAY FXN: " + level_record['round'] + "~" + level_record['sublevel'])
  // sayHowdy()

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

  // console.log({days, money, new_money_stat, incipient_sales, day_profit, data_object })

  updateGamesTableNewDay(day_profit, data_object); //Increments Money and Days. Also updates displayed table new Pop and Mxb.
  updateInventoryTable(incipient_sales); //Reduces quantities by sold amounts.

  day_costs = 0;

  if (level_record["round"] < level_record["final_round"]) {
    if (new_money_stat >= rubicons[3]) {
      incrementSublevel(messageRef, 0);
    } else if (
      parseFloat(level_record["sublevel"]) < 1 &&
      new_money_stat >= rubicons[1]
    ) {
      incrementSublevel(messageRef, 1);
    }
  } else if (
    level_record["round"] >= level_record["final_round"] &&
    new_money_stat >= rubicons[3]
  ) {
    incrementSublevel(messageRef, 4);
  }
}

function incrementSublevel(messageRef, sublevel) {
  // console.log("incrementSublevel fxn with params:", {messageRef, sublevel})

  if (sublevel < 0.9) {
    level_record["round"]++;
  }

  level_record["sublevel"] = sublevel;
  updateCurrentRubicon();
  allButtonsDisabled(true);

  // tomorrow and rubicon_stamp
  let days = dayGrouping($("#daysStat").text(), $("#yearsStat").text(), true)

  level_record[days + 1] = {
    round: level_record["round"],
    sublevel: level_record["sublevel"],
  };

  $(".dialogHolder").removeClass("hidden");
  $(".dialogHolder").find(".dialogBoxText").text(messageRef[sublevel]);

  if (sublevel == 4) {
    level_record["round"]++;
    showEndScreen();
  }

  updateGamesTable(null, null, level_record);
}

function showEndScreen(sublevel) {
  $(".dialogHolder").removeClass("hidden");
  $(".dialogHolder").find(".dialogBoxText").text(messageRef[sublevel]);
  showIsland();
  $(".newDayButton").addClass("hidden");
  $(".crown").removeClass("hidden");
  $(document).ready(function () {
    allButtonsDisabled(true);
  });
}

function showIsland(num) {
  if (!num) {
    num = level_record["round"];
  }

  $("#island" + num).removeClass("hidden");
}

function advance() {
  // console.log("ADVANCE FXN: " + level_record['round'] + "~" + level_record['sublevel'])

  //Endgame.
  if (
    level_record["round"] >= level_record["final_round"] + 1 ||
    level_record["sublevel"] == 4
  ) {
    $(".dialogHolder").addClass("hidden");
    $(document).ready(function () {
      allButtonsDisabled(true);
    });
    return;
  }

  // console.log("advance fxn says we're NOT in endgame")

  //Round transition.
  if (level_record["sublevel"] == 0) {
    $(".dialogHolder")
      .find(".dialogBoxText")
      .text(
        "With the remaining fortune, you pay a magical fruit laboratory to create a brand new fruit of your choosing!"
      );
    level_record["sublevel"] = 0.1;
    showIsland();
    return;
  }

  if (level_record["sublevel"] == 0.1) {
    level_record["sublevel"] = 0.2;
    showCreateFruitForm();
    return;
  }

  if (level_record["sublevel"] == 0.2) {
    let majorPopJQ = $(".factorMajor");
    let minorPopJQ = $(".factorMinor");
    let nameInput = $(".createFruitInputName")
      .val()
      .replace(/^[\s]+/, "");
    nameInput = nameInput.slice(0, 1).toUpperCase() + nameInput.slice(1);

    if (
      !majorPopJQ.text() ||
      !minorPopJQ.text() ||
      !nameInput ||
      !nameInput.replace(/ /g, "").length
    ) {
      alert(
        "You must pick a name and two factors to affect the popularity of your new fruit."
      );
      return;
    } else {
      level_record["sublevel"] = 0.3;
      submitNewFruit(majorPopJQ, minorPopJQ, nameInput);
      return;
    }
  }

  //Everything that's neither endgame nor round-transition. ie the rubicon transitions.
  $(".dialogHolder").addClass("hidden");
  allButtonsDisabled(false);
}

function submitNewFruit(majorPopJQ, minorPopJQ, nameInput) {
  // console.log("SUBMIT FXN: " + level_record['round'] + "~" + level_record['sublevel'])

  let newFruitPopFactors = {};

  (newFruitPopFactors[majorPopJQ.attr("id")] = !majorPopJQ.hasClass(
    "factorNegated"
  )),
    (newFruitPopFactors[minorPopJQ.attr("id")] = !minorPopJQ.hasClass(
      "factorNegated"
    ));

  let multiplicationFactor = Math.ceil(Math.random() * 26) + 4;
  let Low = Math.ceil(Math.random() * 80);
  let High = Math.round(multiplicationFactor * Low);
  let maxPrices = { Low, High };

  setTimeout(() => {
    addFruit(nameInput, newFruitPopFactors, maxPrices);
  }, 500);
}

function addFruit(name, popularity_factors, max_prices) {
  // console.log("ADDFRUIT FXN: " + level_record['round'] + "~" + level_record['sublevel'])

  let rubicon = Math.floor(current_rubicon) + 0.1

  $.ajax({
    type: "GET",
    url: "../api/fruit/create.php",
    dataType: "json",
    data: {
      table_name: "<?php echo $inv_table_name; ?>",
      name,
      popularity_factors,
      max_prices,
      rubicon
    },
    error: function (result) {
      console.log(
        "There was an error that occurred immediately in $.ajax request.",
        result,
        result.responseText
      );
    },
    success: function (result) {
      if (result["status"]) {
        level_record["sublevel"] = 0.9;
        updateGamesTable(null, null, level_record);
        fillInvTable(false, name);

        $(".dialogHolder").addClass("hidden");
        allButtonsDisabled(false);
        resetToNewRound();
      } else {
        console.log(result["message"], result["error"], result);
      }
    },
  });
}

function resetToNewRound() {
  // console.log("RESET FXN: " + level_record['round'] + "~" + level_record['sublevel'])

  updateGamesTable(null, 100);

  $.ajax({
    type: "GET",
    url: "../api/fruit/new_round.php",
    dataType: "json",
    data: {
      table_name: `<?php echo $_SESSION['inv_table_name']; ?>`,
      column_to_change: "quantity",
      new_value: 0,
      data_type: "i",
    },
    error: function (result) {
      console.log(
        "An error has occurred immediately in $.ajax request.",
        result,
        result.responseText
      );
    },
    success: function (result) {
      if (result["status"]) {
        $(".quantityData").text(0);
        $(".amountInput_restock").val(1);
      } else {
        console.log(result["message"], result["error"], result);
      }
    },
  });
}

function updateGamesTableNewDay(profit, data_object) {

  // if (parseInt(profit) + digitGrouping($("#moneyStat").text(), true) > rubicons[3])

  $.ajax({
    type: "GET",
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
        console.log("I'm gonna call updateGameStat with ", money_stat)
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
    type: "GET",
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

function revealSpecificRows() {
  $("table#inventory tbody tr").each(function () {
    let row = $(this);
    if (
      parseInt(row.find(".rubiconData").text()) <= current_rubicon &&
      row.hasClass("hidden")
    ) {
      row.removeClass("hidden");
      makeSparkly(row);
    }
  });
}

function makeSparkly(row) {
  row.addClass("sparkly");
  setTimeout(() => {
    row.removeClass("sparkly");
  }, 2000);
}

function fillInvTable(shouldWipe, name) {
  // console.log("fillInvTable is called, with name as " + name);

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
  // console.log("addRowToTable fxn")
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
                          printDevDataHTML(popularity, max_buying_price)+
                            
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
                              "class='mediumButtonKind button2 buyButton'"+
                              "onClick=restockFruit('"+formattedName+"')>BUY"+
                              "</button>"+    
                              "<button class='mediumButtonKind button3 maxBuyButton' "+
                                "onclick=setAmount('"+formattedName+"','restock','max') "+
                              ">MAX</button>"+
                              "<button class='mediumButtonKind button4 incBuyButton' "+
                                "onclick=setAmount('"+formattedName+"','restock','increment') "+
                              ">⇧</button>"+
                              "<button class='mediumButtonKind button4 decBuyButton' "+
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

function hideSpecificRows() {
  $("table#inventory tbody tr").each(function () {
    if (parseInt($(this).find(".rubiconData").text()) > current_rubicon) {
      $(this).addClass("hidden");
    }
  });
}

function verifyBuyButtons() {
  if ($(".dialogHolder").hasClass("hidden")) {
    $(".buyButton").each(function () {
      let row = $(this).parents("tr");
      let name = row.find(".nameData").text();
      let restockPrice = digitGrouping(row.find(".restockPriceData").text(), true);
      let restockQuantity = digitGrouping(row.find(".amountInput_restock").val(), true);
      let maxBuyableQuantity = Math.floor(
        digitGrouping($("#moneyStat").text(), true) / restockPrice
      );
      $(this).prop(
        "disabled",
        restockQuantity > maxBuyableQuantity || !restockQuantity
      );
    });
  }
}

function setAmount(formattedName, operation, modifier, forced_amount) {
  name = formattedName.replace(/_/g, " ");

  let row = $("table#inventory tbody tr#" + formattedName);

  let class_name = ".amountInput" + "_" + operation;
  let quantity = digitGrouping(row.find(".quantityData").text(), true);
  let restock_amount = digitGrouping(row.find(".amountInput_restock").val(), true);
  let restock_price = digitGrouping(row.find(".restockPriceData").text(), true);
  let max_buyable_quantity = Math.floor(
    digitGrouping($("#moneyStat").text(), true) / restock_price
  );
  let key = operation + "_amount";
  let reset_value = restock_amount;
  if (forced_amount && operation == "restock") {
    restock_amount = forced_amount;
  }
  if (
    operation == "restock" &&
    (!restock_amount || !parseInt(restock_amount))
  ) {
    reset_value = 1;
  }
  if (operation == "restock" && modifier && modifier == "max") {
    reset_value = max_buyable_quantity || 1;
  }
  if (operation == "restock" && modifier && modifier == "increment") {
    reset_value =
      restock_amount < max_buyable_quantity
        ? restock_amount + 1 || 1
        : restock_amount;
  }
  if (operation == "restock" && modifier && modifier == "decrement") {
    reset_value = restock_amount - 1 || 1;
  }

  row.find(class_name).val(digitGrouping(reset_value));

  verifyBuyButtons();
}

function validateNumbersAndSubmit(e, formattedName, operation) {
  // return false;

  verifyBuyButtons();

  let highlightedText = "";
    if (window.getSelection) {
        highlightedText = window.getSelection().toString();
    } else if (document.selection && document.selection.type != "Control") {
        highlightedText = document.selection.createRange().text;
    }
    if (highlightedText == e.target.value){
      e.target.value = ""
    }

  let k = e.keyCode;
  let w = e.which;

  if (k == 13 || w == 13) {
    if (operation == "restock") {
      restockFruit(formattedName);
    } else if (operation == "selling") {
      submitSellingPrice(formattedName);
    }
  }

  if ((k >= 48 && k <= 57) || (w >= 48 && w <= 57)){

    if (e.target.value.length > 10){return}

    let keyValue

    if (k >= 48 && k <= 57){keyValue = String.fromCharCode(k)}else if (w >= 48 && w <= 57){keyValue = string.fromCharCode(w)}

    e.target.value = digitGrouping(digitGrouping(e.target.value, true) + keyValue)
  }

  if ((k == 8 || k == 46) || (w == 8 || w == 46)){
    e.target.value = digitGrouping((digitGrouping(e.target.value, true)).toString().slice(0, -1))
  }

  return false;
}

function inputDigitGrouping(e){
  setTimeout(() => {
    e.target.value = digitGrouping(digitGrouping(e.target.value, true))
  }, 1);
}

function submitSellingPrice(formattedName) {
  event.preventDefault();

  name = formattedName.replace(/_/g, " ");
  let row = $("table#inventory tbody tr#" + formattedName);
  let sellingPriceData = row.find(".sellingPriceData");
  let sellingPriceData_text = digitGrouping(sellingPriceData.text(), true);
  let form = row.find(".sellingPriceForm");
  let input = row.find(".sellingPriceInput");
  let button = row.find(".sellingPriceButton");

  let putative_price = (digitGrouping(input.val(), true)).toString();

  if (!putative_price || !parseInt(putative_price)) {
    input.val("");
    form.addClass("sellingPriceFormHidden");
    sellingPriceData.removeClass("hidden");
    return;
  }

  if (putative_price.match(/^\d+$/)) {
    $.ajax({
      type: "GET",
      url: "../api/fruit/update.php",
      dataType: "json",
      data: {
        name,
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
        console.log(
          "An error, it occurred immediately in $.ajax request.",
          result.responseText,
          result
        );
      },
      success: function (result) {
        if (result["status"]) {
          input.val("");
          form.addClass("sellingPriceFormHidden");
          sellingPriceData.removeClass("hidden");
          sellingPriceData.text(digitGrouping(result["update_data"]["selling_price"]));
        } else {
          console.log(result["message"], result["error"], result);
        }
      },
    });
  }
}

function changeSellingPrice(showInput, formattedName) {
  name = formattedName.replace(/_/g, " ");

  let row = $("table#inventory tbody tr#" + formattedName);
  let sellingPriceData = row.find(".sellingPriceData");
  let sellingPriceData_text = digitGrouping(sellingPriceData.text(), true);
  let form = row.find(".sellingPriceForm");
  let input = row.find(".sellingPriceInput");
  let button = row.find(".sellingPriceButton");

  if (!showInput) {
    setTimeout(() => {
      sellingPriceData.removeClass("hidden");
      form.addClass("sellingPriceFormHidden");
    }, 200);
  }

  if (showInput && !form.find(":focus").length) {
    sellingPriceData.addClass("hidden");
    form.removeClass("sellingPriceFormHidden");
    input.val(digitGrouping(sellingPriceData_text));
    input.focus();
    input.select();
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
      type: "GET",
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
          updateGamesTable(putative_cost); //Send off the db to change money stat.
        } else {
          console.log(result["message"], result["error"], result);
        }
      },
    });
  }
}

function integeriseObjectValues(obj) {
  for (key in obj) {
    obj[key] = parseInt(obj[key]);
  }
  return obj;
}

function calculateSales() {
  let incipient_sales = {};

  $("table#inventory tbody tr").each(function () {
    let row = $(this);
    let name = row.find(".nameData").text();

    if (row.hasClass("hidden")) {
      return;
    }

    let quantity = digitGrouping(row.find(".quantityData").text(), true);
    let selling_price = digitGrouping(row.find(".sellingPriceData").text(), true);

    let max_prices = integeriseObjectValues(
      JSON.parse(row.find(".maxPricesData").text())
    );
    let popularity_factors = JSON.parse(row.find(".popFactorsData").text());
    let { popularity, max_buying_price, restock_price } = getSalesSubstrates(
      popularity_factors,
      max_prices,
      trend_calculates,
      name
    );

    let price_disparity =
      ((max_buying_price - selling_price) / max_buying_price) * 100;

    let sales_percentage = (popularity + price_disparity * 4) / 5 / 100;

    let sales_quantity = Math.ceil(sales_percentage * quantity);

    let copy_of_sales_quantity_before_plusminus = sales_quantity

    let plusOrMinusFive = Math.round(Math.random() * 10) - 5;

    sales_quantity += Math.round((plusOrMinusFive / 100) * quantity);

    if (sales_quantity < 0) {
      // console.log(">>restablish sales_quantity to zero (min).")
      sales_quantity = 0;
    } else if (sales_quantity > quantity) {
      // console.log(">>restablish sales_quantity to quantity (max).")
      sales_quantity = quantity;
    }

    // if(name == "Grapes"){console.log({name, quantity, selling_price, sales_quantity, copy_of_sales_quantity_before_plusminus})}

    // console.log(name + " has unrounded sales quantity " + sales_quantity)
    sales_quantity = Math.round(sales_quantity);
    // console.log(name + " sales quantity ROUNDED TO " + sales_quantity)

    let profit = Math.round(sales_quantity * selling_price);
    // console.log(name + " has unrounded profit " + profit)
    profit = Math.round(profit);
    // console.log(name + " profit ROUNDED TO " + profit)

    incipient_sales[name] = { sales_quantity, profit };
  });

  return incipient_sales;
}

function getSalesSubstrates(
  popularity_factors,
  max_prices,
  trend_calculates,
  name
) {
  let factor1 = getPopularityFactor(popularity_factors, 0, trend_calculates);
  let factor2 = getPopularityFactor(popularity_factors, 1, trend_calculates);
  let popularity = Math.ceil((factor1 * 3 + factor2) / 4);

  let range = max_prices["High"] - max_prices["Low"];
  let fraction_of_price_range = Math.round(
    (Math.floor((popularity - 1) / 20) / 4) * range
  );
  let max_buying_price = Math.round(
    max_prices["Low"] + fraction_of_price_range
  );
  let restock_price = Math.ceil(0.8 * max_buying_price);

  return { popularity, max_buying_price, restock_price };
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

function updateSalesSubstratesInDisplayedTable() {
  $("table#inventory tbody tr").each(function () {
    let row = $(this);
    let name = row.find(".nameData").text();

    if (row.hasClass("hidden")) {
      return;
    }

    let max_prices = integeriseObjectValues(
      JSON.parse(row.find(".maxPricesData").text())
    );
    let popularity_factors = JSON.parse(row.find(".popFactorsData").text());

    let { popularity, max_buying_price, restock_price } = getSalesSubstrates(
      popularity_factors,
      max_prices,
      trend_calculates,
      name
    );

    row.find(".devdata1").text("P" + popularity + "  M" + max_buying_price);

    row
      .find(".popularityCircleSpan")
      .text(getPopularityColor(popularity).text)
      .css({ "background-color": getPopularityColor(popularity).color });

    function getPopularityColor(pop) {
      if (pop < 20) {
        return { text: "⇊", color: "red" };
      } else if (pop < 40) {
        return { text: "↓", color: "orange" };
      } else if (pop < 60) {
        return { text: "·", color: "yellow" };
      } else if (pop < 80) {
        return { text: "↑", color: "greenyellow" };
      } else if (pop >= 80) {
        return { text: "⇈", color: "cyan" };
      }
    }

    row.find(".restockPriceData").text(digitGrouping(restock_price));
  });
}

function getPopularityFactor(pop_factor_names, i, trend_calculates) {
  let pop_keys = Object.keys(pop_factor_names);
  return pop_factor_names[pop_keys[i]]
    ? trend_calculates[pop_keys[i]]
    : 101 - trend_calculates[pop_keys[i]];
}

function printDevData1() {
  console.log("OLD SESSION FROM PHP:");
  console.log(`<?php print_r($_SESSION); ?>`);
  console.log(" ")

  console.log("TC PROXY:");
  console.log(trend_calculates);
  console.log(" ");
}

function printDevData2() {
  console.log({ current_rubicon });
  console.log(" ");
  console.log(
    "PRINT: " + level_record["round"] + "~" + level_record["sublevel"]
  );
  console.log(" ");
  console.log(level_record);
  console.log(" ");
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
      console.log(
        "A kind of error occurred immediately in $.ajax request.",
        result,
        result.responseText
      );
    },
    success: function (result) {
      if (result["status"]) {
        console.log("Result from fruit->read_single:", result["data"][0]);
      } else {
        console.log(result, result["message"], result["error"]);
      }
    },
  });
}

function bindUsefulJqueriesAfterLoadingDataIntoTable() {
  $(".buttonTD").bind("mousewheel", function (e) {
    let delta = e.originalEvent.wheelDelta;

    let scrollingOverTheTop = delta > 0 && this.scrollTop == 0;
    let scrollingOverTheBottom =
      delta < 0 && this.scrollTop >= this.scrollHeight - this.offsetHeight;
    if (scrollingOverTheBottom || scrollingOverTheTop) {
      e.preventDefault();
      e.stopPropagation();
    }

    let current_val = digitGrouping($(this).find("input").val(), true);
    let max_buyable_quantity = Math.floor(
      digitGrouping($("#moneyStat").text(), true) /
      digitGrouping($(this).parents("tr").find(".restockPriceData").text(), true)
    );

    if (delta / 120 > 0) {
      current_val < max_buyable_quantity &&
        $(this)
          .find("input")
          .val(digitGrouping(current_val + 1));
    } else {
      current_val > 1 &&
        $(this)
          .find("input")
          .val(digitGrouping(current_val - 1));
    }
  });
}


function printDevDataHTML(popularity, max_buying_price) {
  let show = <?php echo $_SESSION['show_dev_data']; ?>;

  if (show) {
    return (
      "<p class='devdata1'>P" + popularity + "  M" + max_buying_price + "</p>"
    );
  } else {
    return "";
  }
}

function allButtonsDisabled(toggle) {
  $(document).ready(function () {
    if (toggle) {
      // console.log("GONNA DISABLE ALL BUTTONS");
      $("button").attr("disabled", true);
    } else {
      // console.log("GONNA enable ALL BUTTONS");

      $("button").removeAttr("disabled");
    }
  });
}

</script>