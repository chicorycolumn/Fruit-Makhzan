<script>

function submitSellingPrice(formattedName) {
  event.preventDefault();

  name = formattedName.replace(/_/g, " ");
  let row = $("table#inventory tbody tr#" + formattedName);
  let sellingPriceData = row.find(".sellingPriceData");
  let sellingPriceData_text = digitGrouping(sellingPriceData.text(), true);
  let form = row.find(".sellingPriceForm");
  let input = row.find(".sellingPriceInput");
  let button = row.find(".sellingPriceButton");

  let putative_price = digitGrouping(input.val(), true).toString();

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
          sellingPriceData.text(
            digitGrouping(result["update_data"]["selling_price"])
          );
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

function setAmount(formattedName, operation, modifier, forced_amount) {
  name = formattedName.replace(/_/g, " ");

  let row = $("table#inventory tbody tr#" + formattedName);

  let class_name = ".amountInput" + "_" + operation;
  let quantity = digitGrouping(row.find(".quantityData").text(), true);
  let restock_amount = digitGrouping(
    row.find(".amountInput_restock").val(),
    true
  );
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
  if (highlightedText == e.target.value) {
    e.target.value = "";
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

  if ((k >= 48 && k <= 57) || (w >= 48 && w <= 57)) {
    if (e.target.value.length > 10) {
      return;
    }

    let keyValue;

    if (k >= 48 && k <= 57) {
      keyValue = String.fromCharCode(k);
    } else if (w >= 48 && w <= 57) {
      keyValue = string.fromCharCode(w);
    }

    e.target.value = digitGrouping(
      digitGrouping(e.target.value, true) + keyValue
    );
  }

  if (k == 8 || k == 46 || w == 8 || w == 46) {
    e.target.value = digitGrouping(
      digitGrouping(e.target.value, true).toString().slice(0, -1)
    );
  }

  return false;
}

</script>