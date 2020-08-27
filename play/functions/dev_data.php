<script>

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

</script>