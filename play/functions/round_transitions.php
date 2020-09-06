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
        "<label class='createFruitLabel'>What affects its popularity?</label>" +
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
let rubicons = {1: Math.pow(10, 3), 2: Math.pow(10, 6)}
let prefix = rubicons[2]==1000000 ? "m" : "b"

const rubiconMessageRef = {
  1: "Well done! You unlocked a new fruit.",
  0: "You're a "+prefix+"illionaire! As a reward for all your hard work, you buy an island to relax on.",
  4: "You won the whole game! You own four lovely islands and are now king.",
};

function advance() {
  if (
    level_record["round"] >= level_record["final_round"] + 1 ||
    level_record["sublevel"] == 4
  ) {
    $(".dialogHolder").addClass("hidden");
    $(".island").removeClass("islandFaded");
    $(document).ready(function () {
      verifyBuyButtons();
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
  $(".island").removeClass("islandFaded");
  allButtonsDisabled(false);
  verifyBuyButtons();
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
      $(".island").addClass("islandFaded");
      $(".dialogHolder")
        .find(".dialogBoxText")
        .text(rubiconMessageRef[level_record[key]["sublevel"]]);

      if (level_record["round"] == level_record["final_round"] + 1) {
        showIsland();
        $(".newDayButton").addClass("hidden");
        $("#clock").attr("src", '.././images/crown2.png');
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
  $(".island").addClass("islandFaded");
  $(".dialogHolder").find(".dialogBoxText").text(rubiconMessageRef[sublevel]);

  if (sublevel == 4) {
    level_record["round"]++;
    showEndScreen();
  }

  updateGamesTable(null, null, level_record);
}

function showEndScreen(sublevel) {
  $(".dialogHolder").removeClass("hidden");
  $(".island").addClass("islandFaded");
  $(".dialogHolder").find(".dialogBoxText").text(rubiconMessageRef[sublevel]);
  showIsland();
  $(".newDayButton").addClass("hidden");
  $("#clock").attr("src", '.././images/crown2.png');
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

function resetToNewRound(level_record) {
  updateGamesTable(null, 100);
  updateGamesTable(null, null, level_record);

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
      console.log(row.find(".nameData").text())
      row.prependTo("table#inventory tbody")
      row.removeClass("hidden");
      makeSparkly(row);
    }
  });
}

function makeSparkly(row) {
  row.find("td").addClass("sparkly");
  setTimeout(() => {
    row.find("td").removeClass("sparkly");
  }, 5000);
}

function hideSpecificRows() {
  $("table#inventory tbody tr").each(function () {
    if (parseInt($(this).find(".rubiconData").text()) > current_rubicon) {
      $(this).addClass("hidden");
    }
  });
}

</script>