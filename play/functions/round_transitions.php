<script>

let createFruitForm =
  '<form class="createFruitForm">' +
    '<div class="boxBody">' +

      '<div class="formGroup createFruitFormGroup">' +
        '<label class="createFruitLabel noMarginPadding">Name of brand new fruit:</label>' +
        '<span><input type="text" class="formControl createFruitInputName" id="name" maxlength=20' +
        'onkeypress="return /[0-9a-zA-Z ]/.test(event.key)" ></span>' +
      "</div>" +

      '<div class="formGroup createFruitFormGroup">' +
        "<label class='createFruitLabel'>Which two will affect its popularity?</label>" +
        '<div class="formControl factorsHolder">' +
      
          '<span id="love" class="factorSelect factorDimensions" ' +
          'onMouseUp="return selectFactor(event,`Love`)" ' +
          ">Love</span>" +
          
          '<span id="politics" class="factorSelect factorDimensions" ' +
          'onMouseUp="return selectFactor(event,`Politics`)" ' +
          ">Politics</span>" +
          
          '<span id="weather" class="factorSelect factorDimensions" ' +
          'onMouseUp="return selectFactor(event,`Weather`)" ' +
          ">Weather</span>" +
          
          '<span id="conformity" class="factorSelect factorDimensions" ' +
          'onMouseUp="return selectFactor(event,`Conformity`)" ' +
          ">Conformity</span>" +
          
          '<span id="decadence" class="factorSelect factorDimensions" ' +
          'onMouseUp="return selectFactor(event,`Decadence`)" ' +
          ">Decadence</span>" +
      
        "</div>" +
      "</div>" +
    "</div>" +
  "</form>";

const rubiconMessageRef = {
  1: "Wahad! You reached sublevel 1!",
  0: "You're a billionaire! As a reward for all your hard work, you buy an island to relax on.",
  4: "You won the whole game! You own five islands and are now king.",
};

const rubicons = { 1: 150, 2: 300 };
// const rubicons = {1: 10000, 2: 1000000000}

function advance() {
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
  $(".dialogHolder").addClass("hidden");
  allButtonsDisabled(false);
}

function loadRubiconIfAt() {
  if (level_record["round"] > level_record["final_round"]) {
    showEndScreen(4);
  }

  for (let i = 1; i <= level_record["round"]; i++) {
    showIsland(i);
  }
  for (let key in level_record) {
    let days = dayGrouping($("#daysStat").text(), $("#yearsStat").text(), true);
    if (parseInt(key) == days && parseFloat(level_record["sublevel"]) < 0.9) {
      allButtonsDisabled(true);
      $(".dialogHolder").removeClass("hidden");
      $(".dialogHolder")
        .find(".dialogBoxText")
        .text(rubiconMessageRef[level_record[key]["sublevel"]]);

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

function incrementSublevel(rubiconMessageRef, sublevel) {
  if (sublevel < 0.9) {
    level_record["round"]++;
  }

  level_record["sublevel"] = sublevel;
  updateCurrentRubicon();
  allButtonsDisabled(true);

  let days = dayGrouping($("#daysStat").text(), $("#yearsStat").text(), true);

  level_record[days + 1] = {
    round: level_record["round"],
    sublevel: level_record["sublevel"],
  };

  $(".dialogHolder").removeClass("hidden");
  $(".dialogHolder").find(".dialogBoxText").text(rubiconMessageRef[sublevel]);

  if (sublevel == 4) {
    level_record["round"]++;
    showEndScreen();
  }

  updateGamesTable(null, null, level_record);
}

function showEndScreen(sublevel) {
  $(".dialogHolder").removeClass("hidden");
  $(".dialogHolder").find(".dialogBoxText").text(rubiconMessageRef[sublevel]);
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

function resetToNewRound() {
  updateGamesTable(null, 100);

  $.ajax({
    type: "POST",
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

function hideSpecificRows() {
  $("table#inventory tbody tr").each(function () {
    if (parseInt($(this).find(".rubiconData").text()) > current_rubicon) {
      $(this).addClass("hidden");
    }
  });
}

</script>