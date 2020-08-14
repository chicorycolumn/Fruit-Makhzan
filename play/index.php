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

// seed_data.forEach(item => item['restock_amount'] = 1)

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

let columnIndexRef = getColumnIndexes()

function newDay() {
  let num_rows = $("table#inventory tbody tr").filter(function () {
    return this;
  }).length;
  let incipient_sales = calculateSales();
  let total_profit = Object.values(incipient_sales).reduce(
    (sum, obj) => sum + obj.profit,
    0
  );
  console.log("About to gain " + total_profit + "Ð.");

  fillQuantityYesterday(); //Moves current quantities to the qy column.
  updateGamesTable(total_profit, "new day"); //Increments Money and Days. Also updates displayed table new Pop and Mxb.
  updateInventoryTable(incipient_sales); //Reduces quantities by sold amounts.
}

function fillQuantityYesterday() {
  let num_rows = $("table#inventory tbody tr").filter(function () {
    return this;
  }).length;
  for (let i = 0; i < num_rows; i++) {
    let row = $("table#inventory tbody tr:eq(" + i + ")");
    row
      .children()
      .eq(columnIndexRef.qy)
      .text(row.children().eq(columnIndexRef.quantity).text());
  }
}

function updateGamesTable(money_crement, operation) {
  if (operation == "new day") {
    $.ajax({
      type: "GET",
      url: "../api/fruit/new_day_supertable.php",
      dataType: "json",
      data: {
        table_name: "games",
        identifying_column: "game_id",
        identifying_data: `<?php echo $_SESSION['gid']; ?>`,
        profit: money_crement,
      },
      error: function (result) {
        console.log("An error occurred immediately in $.ajax request.", result);
        console.log(result.responseText);
      },
      success: function (result) {
        // console.log("success")
        if (result["status"]) {
          // console.log(result);

          let { money_stat, days_stat, trend_calculates } = result[
            "update_data"
          ];
          updateGameStats(money_stat, days_stat, trend_calculates);
        } else {
          console.log(result["message"]);
          console.log(result["error"]);
        }
      },
    });
  } else if ((operation = "decrement")) {
    console.log({
      money_stat: parseInt($("#moneyStat").text()) - money_crement,
    });
    // return
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
          result
        );
        console.log(result.responseText);
      },
      success: function (result) {
        console.log("A success");
        console.log(result);
        if (result["status"]) {
          console.log(result);

          let { money_stat } = result["update_data"];
          updateGameStats(money_stat);
        } else {
          console.log(result["message"]);
          console.log(result["error"]);
        }
      },
    });
  }
}

function updateInventoryTable(incipient_sales) {
  console.log("### updateInventoryTable fxn invoked");
  console.log(incipient_sales);
  // return
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
        "###An error occurred immediately in $.ajax request.",
        result
      );
      console.log(result.responseText);
    },
    success: function (result) {
      console.log("###success");
      if (result["status"]) {
        let names = Object.keys(result["update_data"]);

        names.forEach((name) => {
          let row = $("table#inventory tr").filter(function () {
            return $(this).children().eq(columnIndexRef.name).text() == name;
          });

          let current_quantity = parseInt(
            row.children().eq(columnIndexRef.quantity).text()
          );
          let new_quantity =
            current_quantity -
            parseInt(result["update_data"][name]["sales_quantity"]);
          row.children().eq(columnIndexRef.quantity).text(new_quantity);
        });
      } else {
        console.log("###else");
        console.log(result["message"]);
        console.log(result["error"]);
      }
    },
  });
}

function fillInvTable(shouldWipe) {
  if (shouldWipe) {
    $("#inventory tbody > tr").remove();
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
      console.log("An error occurred immediately in $.ajax request.", result);
      console.log(result.responseText);
    },
    success: function (result) {
      // console.log("success")

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
          let formattedName = name.replace(/\s/g, "%20");
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
                      "<td>"+name+"</td>"+
                      "<td>"+"-"+"</td>"+
                      "<td>"+quantity+"</td>"+
                      "<td class='clickable highlighted' onClick=changeSellingPrice('"+formattedName+"')>"+
                        "<span class='shown sellingPriceSpan clickable'>"+selling_price+"</span>"+
                        "<form class='hidden sellingPriceForm'>"+
                          "<input onkeypress='return /[0-9]/.test(event.key)' maxlength=10 class='sellingPriceInput' maxlength=10 type='text'>"+
                          "<button type='submit' class='sellingPriceButton'>OK</button>"+
                        "</form></td>"+
                      "<td>"+restock_price+"</td>"+
                      "<td style='cursor:help;' onClick=printSingle('"+formattedName+"')>"+resilience+"</td>"+
                      "<td>"+
                          "<div class='buttonSuperHolder'>"+
                            "<p class='devdata1'>P"+popularity+"  M"+max_buying_price+"</p>"+
                            "<div class='buttonSubHolder'>"+
                              "<button class='button2' onClick=restockFruit('"+formattedName+"')>Buy</button>"+
                              "<input value="+seed_data.filter(item => item['name']==name)[0]['restock_amount']+" "+
                                "class='amountInput amountInput_restock' "+
                                "onclick=this.select() "+
                                "onkeypress='return /[0-9]/.test(event.key)' "+
                                "onkeyup=setAmount(false,'"+formattedName+"',this.value,'restock') "+
                                "onblur=setAmount(true,'"+formattedName+"',this.value,'restock') "+
                                "maxlength=10>"+
                            "</div>"+
                          "</div>"+
                      "</td>"+
                      "<td>"+
                                              
                        "<div class='buttonSubHolder'>"+
                        "<button class='button2' onClick=throwFruit('"+formattedName+"')>Throw</button>"+
                          "<input value="+seed_data.filter(item => item['name']==name)[0]['throw_amount']+" "+
                            "class='amountInput amountInput_throw' "+
                            "onclick=this.select() "+
                            "onkeypress='return /[0-9]/.test(event.key)' "+
                            "onkeyup=setAmount(false,'"+formattedName+"',this.value,'throw') "+
                            "onblur=setAmount(true,'"+formattedName+"',this.value,'throw') "+
                            "maxlength=10>"+
                        "</div>"+
                                            
                      "</td>"+
                    "</tr>";
                    $(response).appendTo($("#inventory"));
        });
      } else {
        console.log(result["message"]);
        console.log(result["error"]);
      }
    },
  });
}

function setAmount(setIntoSession, formattedName, amount, operation) {
  console.log(setIntoSession, formattedName, amount, operation);

  name = formattedName.replace(/%20/g, " ");
  let columnIndexRef = getColumnIndexes();
  let row = $("table#inventory tbody tr").filter(function () {
    return $(this).children().eq(columnIndexRef["name"]).text() == name;
  });
  let quantity = parseInt(row.children().eq(columnIndexRef["quantity"]).text());
  let class_name = ".amountInput" + "_" + operation;
  let key = operation + "_amount";

  if (!setIntoSession) {
    if (amount && parseInt(amount)) {
      seed_data.filter((item) => item["name"] == name)[0][key] = parseInt(
        amount
      );
    }
  } else {
    if (!amount || !parseInt(amount)) {
      let columnIndexRef = getColumnIndexes();

      let row = $("table#inventory tbody tr").filter(function () {
        return $(this).children().eq(columnIndexRef["name"]).text() == name;
      });

      let reset_value = 1;

      seed_data.filter((item) => item["name"] == name)[0][key] = reset_value;
      row.find(class_name).val(reset_value);
    } else if (operation == "throw" && parseInt(amount) > quantity) {
      let reset_value = quantity || 1;

      seed_data.filter((item) => item["name"] == name)[0][key] = reset_value;
      row.find(class_name).val(reset_value);
    } else {
      $.ajax({
        type: "GET",
        url: "../utils/set_session.php",
        dataType: "json",
        data: {
          seed_data: seed_data,
        },
        error: function (result) {
          console.log(
            "An error occurred immediately in this $.ajax request.",
            result
          );
          console.log(result.responseText);
          // window.location = "../play";
        },
        success: function (result) {
          console.log("a3 success");
          console.log(result);
          if (result["status"]) {
            console.log("status true");
          } else {
            console.log(result["message"]);
            console.log(result["error"]);
          }
        },
      });
    }
  }
}

function throwFruit(formattedName) {
  name = formattedName.replace(/%20/g, " ");

  let columnIndexRef = getColumnIndexes();

  let row = $("table#inventory tbody tr").filter(function () {
    return $(this).children().eq(columnIndexRef["name"]).text() == name;
  });

  let throw_amount = parseInt(row.find(".amountInput_throw").val());
  let quantity = parseInt(row.children().eq(columnIndexRef["quantity"]).text());

  if (!quantity) {
    return;
  }

  setAmount(true, formattedName, throw_amount, "throw");

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
        console.log("An error occurred immediately in $.ajax request.");
        console.log(result.responseText);
        console.log(result);
      },
      success: function (result) {
        // console.log("success")
        if (result["status"]) {
          let fruit = result["data"][0];

          let el = $("table#inventory tr td").filter(function () {
            return $(this).text() == fruit["name"];
          });

          el.parent("tr")
            .children()
            .eq(columnIndexRef["quantity"])
            .text(fruit["quantity"]);

          if (throw_amount > parseInt(fruit["quantity"])) {
            let reset_value = parseInt(fruit["quantity"]) || 1;
            el.parent("tr").find(".amountInput_throw").val(reset_value);
            seed_data.filter((item) => item["name"] == name)[0][
              "restock_amount"
            ] = reset_value;
          }
        } else {
          console.log(result["message"]);
          console.log(result["error"]);
        }
      },
    });
  }
}

function restockFruit(formattedName) {
  name = formattedName.replace(/%20/g, " ");

  let columnIndexRef = getColumnIndexes();

  let row = $("table#inventory tbody tr").filter(function () {
    return $(this).children().eq(columnIndexRef["name"]).text() == name;
  });

  let requested_amount = parseInt(row.find(".amountInput_restock").val());
  let restock_price = parseInt(
    row.children().eq(columnIndexRef["restock price"]).text()
  );
  let putative_cost = requested_amount * restock_price;
  let money = parseInt($("#moneyStat").text());

  setAmount(true, formattedName, requested_amount, "restock");
  console.log(money, putative_cost);

  if (putative_cost > money) {
    alert("Insufficient funds!");
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
        console.log("An error occurred immediately in $.ajax request.");
        console.log(result.responseText);
        console.log(result);
      },
      success: function (result) {
        // console.log("success")
        if (result["status"]) {
          let fruit = result["data"][0];

          let el = $("table#inventory tr td").filter(function () {
            return $(this).text() == fruit["name"];
          });

          el.parent("tr")
            .children()
            .eq(columnIndexRef["quantity"])
            .text(fruit["quantity"]);

          let maxPossibleToBuy = Math.floor(
            (money - putative_cost) / restock_price
          );

          if (requested_amount > maxPossibleToBuy) {
            let reset_value = maxPossibleToBuy || 1;

            console.log("*******", reset_value);

            el.parent("tr").find(".amountInput_restock").val(reset_value);
            seed_data.filter((item) => item["name"] == name)[0][
              "restock_amount"
            ] = reset_value;
          }
          // ***It was successful transaction. So we must send off the db to change money stat now.
          updateGamesTable(putative_cost, "decrement");
        } else {
          console.log(result["message"]);
          console.log(result["error"]);
        }
      },
    });
  }
}

function changeSellingPrice(name) {
  name = name.replace(/%20/g, " ");
  let columnIndexRef = getColumnIndexes();

  let row = $("table#inventory tbody tr").filter(function () {
    return $(this).children().eq(columnIndexRef["name"]).text() == name;
  });

  let span = row.find(".sellingPriceSpan");
  let form = row.find(".sellingPriceForm");
  let input = row.find(".sellingPriceInput");
  let button = row.find(".sellingPriceButton");

  if (form.hasClass("hidden")) {
    span.addClass("hidden");
    form.removeClass("hidden");
    input.focus();

    form.on("submit", function (e) {
      e.preventDefault();

      let putative_price = input.val();

      if (!putative_price || !parseInt(putative_price)) {
        input.val("");
        form.addClass("hidden");
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
            console.log("An error occurred immediately in $.ajax request.");
            console.log(result.responseText);
            console.log(result);
          },
          success: function (result) {
            if (result["status"]) {
              input.val("");
              form.addClass("hidden");
              span.removeClass("hidden");

              span.text(result["update_data"]["selling_price"]);
            } else {
              console.log(result["message"]);
              console.log(result["error"]);
            }
          },
        });
      }
    });
  }

  //Change this to input box?
  // row.children().eq(columnIndexRef['selling price']).text("oh yeah")
}

function calculateSales() {
  let incipient_sales = {};
  let num_rows = $("table#inventory tbody tr").filter(function () {
    return this;
  }).length;

  for (let i = 0; i < num_rows; i++) {
    let row = $("table#inventory tbody tr:eq(" + i + ")");
    let name = row.children().eq(columnIndexRef.name).text();
    let quantity = parseInt(row.children().eq(columnIndexRef.quantity).text());
    let selling_price = parseInt(
      row.children().eq(columnIndexRef["selling price"]).text()
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

    console.log(
      "calculateSales fxn: POP and MXB of " +
        name +
        " are " +
        popularity +
        " and " +
        max_buying_price
    );

    // console.log("calculateSales fxn: Popularity of "+name+" is "+popularity)
    let sales_percentage = (popularity + price_disparity * 4) / 5 / 100;

    let sales_quantity = Math.ceil(sales_percentage * quantity);

    if (sales_quantity < 0) {
      sales_quantity = 0;
    } else if (sales_quantity > quantity) {
      sales_quantity = quantity;
    }

    // console.log("calculateSales fxn: Sell "+ sales_quantity + " from " + quantity + " of " + name)

    let profit = sales_quantity * selling_price;

    incipient_sales[name] = { sales_quantity, profit };
  }
  return incipient_sales;
}

function printSingle(name) {
  name = name.replace(/%20/g, " ");
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
      console.log("An error occurred immediately in $.ajax request.", result);
      console.log(result.responseText);
    },
    success: function (result) {
      if (result["status"]) {
        console.log(result);
      } else {
        console.log(result["message"]);
        console.log(result["error"]);
      }
    },
  });
}

function checkSession() {
  console.log(">>>Old session is:");
  console.log(`<?php print_r($_SESSION); ?>`);
}

function checkTCs() {
  console.log(">>>TC proxy:");
  console.log(trend_calculates);
  console.log(">>>Seed data proxy:");
  console.log(seed_data);
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

  let labels = ["quantity", "selling price", "name", "qy", "restock price"];

  labels.forEach((label) => {
    columnIndexRef[label] = getIndex(label);
  });

  return columnIndexRef;
}

function updateGameStats(new_money_stat, new_days_stat, new_trend_calculates) {
  let el = $("*").filter(function () {
    return $(this).is("#moneyStat");
  });
  el.text(new_money_stat);

  if (new_days_stat) {
    el = $("*").filter(function () {
      return $(this).is("#daysStat");
    });
    el.text(new_days_stat);
  }

  if (new_trend_calculates) {
    console.log("old TCs in updateGameStats fxn", trend_calculates);
    console.log("new TCs in updateGameStats fxn", new_trend_calculates);
    trend_calculates = new_trend_calculates;
    updateSalesSubstratesInDisplayedTable();
  }
}

function updateSalesSubstratesInDisplayedTable() {
  let num_rows = $("table#inventory tbody tr").filter(function () {
    return this;
  }).length;
  let columnIndexRef = getColumnIndexes();

  for (let i = 0; i < num_rows; i++) {
    let name = $("table#inventory tbody tr")
      .eq(i)
      .children()
      .eq(columnIndexRef.name)
      .text();
    let formattedName = name;
    // .replace(/\s/g, "%20")
    // console.log("*", formattedName)
    let max_prices = seed_data.filter((item) => item.name == formattedName)[0]
      .max_prices;
    let popularity_factors = seed_data.filter(
      (item) => item.name == formattedName
    )[0].popularity_factors;
    let { popularity, max_buying_price, restock_price } = getSalesSubstrates(
      popularity_factors,
      max_prices,
      trend_calculates
    );

    console.log("NEW", popularity, max_buying_price);
    $("table#inventory tbody tr")
      .eq(i)
      .find(".devdata1")
      .text("P" + popularity + "  M" + max_buying_price);
    $("table#inventory tbody tr")
      .eq(i)
      .children()
      .eq(columnIndexRef["restock price"])
      .text(restock_price);
  }
}

function getPopularityFactor(pop_factor_names, i, trend_calculates) {
  let pop_keys = Object.keys(pop_factor_names);
  return pop_factor_names[pop_keys[i]]
    ? trend_calculates[pop_keys[i]]
    : 101 - trend_calculates[pop_keys[i]];
}
</script>