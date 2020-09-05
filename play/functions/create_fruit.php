<script>

function showCreateFruitForm() {
  $(".dialogHolder").removeClass("hidden");
  $(".island").addClass("islandFaded");
  $(".dialogHolder").find(".dialogBoxText").html(createFruitForm);
  $(".factorSelect").each(function () {
    $(this).bind("contextmenu", function (e) {
      return false;
    });
  });
}

function submitNewFruit(majorPopJQ, minorPopJQ, nameInput) {
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

  addFruit(nameInput, newFruitPopFactors, maxPrices);
}

function addFruit(name, popularity_factors, max_prices) {
  let rubicon = Math.floor(current_rubicon) + 0.1;

  $.ajax({
    type: "POST",
    url: "../api/fruit/create.php",
    dataType: "json",
    data: {
      table_name: "<?php echo $inv_table_name; ?>",
      name,
      popularity_factors,
      max_prices,
      rubicon,
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

        fillInvTable(false, name);
        resetToNewRound(level_record);

        $(".dialogHolder").addClass("hidden");
        $(".island").removeClass("islandFaded");
        allButtonsDisabled(false);
        verifyBuyButtons()

      } else {
        console.log(result["message"], result["error"], result);
      }
    },
  });
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

</script>
