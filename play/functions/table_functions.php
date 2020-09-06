<script>

function newDay() {

  if (in_progress["round"]["value"] || in_progress["restock"]["value"]){
    return
  }

  let incipient_sales = calculateSales();
  let day_profit = Object.values(incipient_sales).reduce(
    (sum, obj) => sum + obj.profit,
    0
  );

  let days = dayGrouping($("#daysStat").text(), $("#yearsStat").text(), true);
  let money = digitGrouping($("#moneyStat").text(), true);

  let new_money_stat = money + day_profit;
  let new_days_stat = days + 1;

  $("#tooltipWeather").text("The weather score goes in 90-day seasons. It's currently " + getSeasonDescription(new_days_stat) + ".")

  if (level_record["round"] < level_record["final_round"]) {
    if (new_money_stat >= rubicons[2]) {
      in_progress["round"]["value"] = true
      incrementSublevel(rubiconMessageRef, 0);
    } else if (
      parseFloat(level_record["sublevel"]) < 1 &&
      new_money_stat >= rubicons[1]
    ) {
      in_progress["round"]["value"] = true
      incrementSublevel(rubiconMessageRef, 1);
    }
  } else if (
    new_money_stat >= rubicons[2]
  ) {
    in_progress["round"]["value"] = true
    incrementSublevel(rubiconMessageRef, 4);
  }

  overall_sales_history[days + 1] = { profit: day_profit, costs: day_costs };

  updateGamesTableNewDay(new_money_stat, new_days_stat, day_profit, overall_sales_history);
  updateInventoryTable(incipient_sales);
  updateTimestamp()
  day_costs = 0;
}

function updateGamesTableNewDay(new_money_stat, new_days_stat, profit, overall_sales_history) {

  let newest_trend_calculates = evolve_trend_calculates_js(trend_calculates, new_days_stat, overall_sales_history)
  updateGameStats(new_money_stat, new_days_stat, newest_trend_calculates, overall_sales_history)

  $.ajax({
    type: "POST",
    url: "../api/fruit/new_day_supertable.php",
    dataType: "json",
    data: {
      table_name: "games",
      identifying_column: "game_id",
      identifying_data: `<?php echo $_SESSION['gid']; ?>`,
      new_money_stat,
      new_days_stat,
      json_data_object: overall_sales_history,
      json_data_object_name: "overall_sales_history",
      level_record
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

        let salesNumDisplay = 70

        if (Object.keys(overall_sales_history).length > salesNumDisplay) {
          delete overall_sales_history[Object.keys(overall_sales_history).sort((a, b) => parseInt(a) - parseInt(b))[0]]
        }
        
      } else {
        console.log(result["message"], result["error"], result);
      }
    },
  });
}

function updateInventoryTable(incipient_sales) {

  let names = Object.keys(incipient_sales);

  names.forEach((name) => {
    let formattedName = name.replace(/\s/g, "_");
    let row = $("table#inventory tbody tr#" + formattedName);
    let current_quantity = digitGrouping(row.find(".quantityData").text(), true);
    let new_quantity =
      current_quantity -
      parseInt(incipient_sales[name]["sales_quantity"]);

    let formattedNewQuantity = digitGrouping(new_quantity)

    if (parseInt(formattedNewQuantity) < 0 || (/-/).test(formattedNewQuantity)){
      formattedNewQuantity = "0"
    }

    row.find(".quantityData").text(formattedNewQuantity);
  });
  verifyBuyButtons();

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
      if (!result["status"]) {
        console.log(result["message"], result["error"], result);
      }
    },
  });
}

function updateGamesTable(money_crement, money_absolute, new_level_record) {
  let type_definition_string;
  let update_data;

  console.log("updateGamesTable 1", in_progress, in_progress["restock"]["value"])

  if (new_level_record) {
    type_definition_string = "ss";
    update_data = {
      level_record: new_level_record,
    };
  } else {
    let new_money_stat

    if (money_absolute) {
      new_money_stat = money_absolute;
    } else {
      new_money_stat = digitGrouping($("#moneyStat").text(), true) - money_crement;
    }

    type_definition_string = "is";
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
      type_definition_string,
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
        if (!new_level_record){ 
          updateGameStats(result["update_data"]['money_stat']);
        }
          if (in_progress){
          console.log("updateGamesTable 2", in_progress, in_progress["restock"]["value"])
          in_progress["round"]["value"] = false
          in_progress["restock"]["value"] = false
          allButtonsDisabled(false);
          if (!$(".invTableOverlay").hasClass("hidden")){

            let loadingGifs = ["avocado", "carrot", "lemon"]
            $(".invTableOverlayImage").attr("src", ".././images/"+loadingGifs[Date.now() % 3]+".gif")
            $(".invTableOverlay").addClass("hidden")
          }
          setTimeout(verifyBuyButtons, 10);
          console.log("updateGamesTable 3", in_progress, in_progress["restock"]["value"])
        }
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
        bindUsefulJqueriesAfterLoadingDataIntoTable();
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

  console.log(fruit['name'], shouldPrepend)

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
  max_prices = parseIntObjectValues(max_prices)
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

  let quantityFlaggedStyle = ""
  if (parseInt(quantity)<0){
    quantity = 0
    quantityFlaggedStyle = "style='color: darkgreen;'"
  }
  
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
        "<p class='invData quantityData noMarginPadding' "+quantityFlaggedStyle+">"+digitGrouping(quantity)+"</p>"+
      "</div>"+
    "</td>"+
    
    
    "<td class='regularTD sellingPriceTD clickable highlighted' "+
    
      "onClick=changeSellingPrice(true,'"+formattedName+"')>"+

        "<div class='invSubtd sellingPriceSubtd'>"+
        
          "<p class='invData sellingPriceData clickable shown'>"+digitGrouping(selling_price)+"</p>"+
          
          "<form class='sellingPriceForm noMarginPadding sellingPriceFormHidden' "+
            "onsubmit=submitSellingPrice(`"+formattedName+"`) "+
            "onfocusout=changeSellingPrice(false,'"+formattedName+"')>"+
              
            "<textarea class='sellingPriceInput' "+
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
          "<div class='popularityCircleSpan popularityCircleSpanHover'><p class='popularityCircleText'>X</p><span class='tooltip popularityCircleTooltip'>Tooltip text</span></div>"+
        "</div>"+
      "</div>"+
    "</td>"+
    

    "<td class='regularTD factorsTD' >"+
      
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
              "ondblclick='bindDevDataFunctions(value)' "+
              "onkeydown='return validateNumbersAndSubmit(event,`"+formattedName+"`,`restock`)' "+
              "onblur=setAmount('"+formattedName+"','restock') "+
              "maxlength=10>"+
          
          "</div>"+ 
          
          "<div class='buttonSubHolder'>"+
            "<button "+
            "class='mediumButtonKind buyButton'"+
            "onClick=restockFruit('"+formattedName+"')>BUY"+
            "<span class='tooltip insufficientFundsTooltip'>Insufficient funds</span>"+
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
  console.log("wong")
  name = formattedName.replace(/_/g, " ");
  let row = $("table#inventory tbody tr#" + formattedName);
  let requested_amount = digitGrouping(row.find(".amountInput_restock").val(), true);

  if (!requested_amount || in_progress["restock"]["value"]) {
    if (in_progress["restock"]["value"]){console.log("restock is in progress, hold on")}
    return;
  }

  console.log("setting in_progress.restock to TRUE...")
  in_progress["restock"]["value"] = true
  allButtonsDisabled(true);

  setTimeout(() => {
    if (in_progress["restock"]["value"]){
      $(".invTableOverlay").removeClass("hidden")
    }
  }, 1000);

  console.log("...so it should now be ", in_progress["restock"]["value"])
  console.log("restockFruit", in_progress, in_progress["restock"]["value"])

  let restock_price = digitGrouping(row.find(".restockPriceData").text(), true);
  let putative_cost = requested_amount * restock_price;
  let money = digitGrouping($("#moneyStat").text(), true);

  setAmount(formattedName, "restock", "", requested_amount);

    if (putative_cost > money) {
    alert("Insufficient funds!");
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
          console.log("restockFruit about to call updateGamesTable", in_progress, in_progress["restock"]["value"])
          updateGamesTable(putative_cost, null, null);
        } else {
          console.log(result["message"], result["error"], result);
        }
      },
    });
  }
}

function updateGameStats(new_money_stat, new_days_stat, new_trend_calculates, newest_overall_sales_history) {
  $("#moneyStat").text(digitGrouping(new_money_stat));

  if (new_days_stat) {

    let {days, years} = dayGrouping(new_days_stat)

    $("#daysStat").text(days);
    $("#yearsStat").text(years);
  }

  verifyBuyButtons();

  if (newest_overall_sales_history){
    updateSalesChart(newest_overall_sales_history);
  }

  if (new_trend_calculates) {
    trend_calculates = new_trend_calculates;
    updateSalesSubstratesInDisplayedTable();
    updateTrendsChart(new_trend_calculates);
  }
}

</script>