<script>

function bindDevDataFunctions(value){
  if (value == 666){
    $("#factorsTH").bind("click", printDevData1)
    $("#factorsTH").css({"color": "purple"})
    $("#restockTH").bind("click", printDevData2)
    $("#restockTH").css({"color": "purple"})
    $(".factorsTD").bind("click", function(e){return printSingle(e)})
    $(".factorsTD").css({"border": "purple solid 1px"})
    rubicons[1] = 150
    rubicons[2] = 300
  }
}

function printDevData1() {
  console.log("OLD SESSION FROM PHP:", `<?php print_r($_SESSION); ?>`);
  console.log("TC PROXY:", trend_calculates);
}

function printDevData2() {
  console.log({ current_rubicon }, "PRINT: " + level_record)
}

function printSingle(e) {

  let name = $(e.target).parents("tr").find(".nameData").text()

  name = name.replace(/_/g, " ");
  $.ajax({
    type: "GET",
    url: "../api/fruit/read_single.php",
    dataType: "json",
    data: {
      table_name: "<?php echo $inv_table_name; ?>",
      identifying_column: "name",
      identifying_data: name,
      type_definition_string: "s",
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

</script>