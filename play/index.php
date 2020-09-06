<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['gid'])) {
  header("Location: ../home");
  exit();
}

include_once '../api/config/database.php';

setcookie("makhzan", $_SESSION['gid'], time() + 3600 * 24 * 30, "/");
$gid = $_SESSION['gid'];
$inv_table_name = $_SESSION['inv_table_name'];

include './includes.php';
include '../master.php';
?>

<script>
console.log("RESETTING in_progress")
let in_progress = {"round": {"value": false}, "restock": {"value": false}, "selling_price": {"value": false}};
let level_record = JSON.parse(`<?php echo $_SESSION['level_record']; ?>`);
let sessionMoney = "<?php echo $_SESSION['money_stat']; ?>"
let sessionDays = "<?php echo $_SESSION['days_stat']; ?>"

//screw: On heroku, this comes out as nothing. When clicking Continue.
let overall_sales_history = JSON.parse(`<?php print_r(
  $_SESSION['overall_sales_history']
); ?>`)

let trend_calculates = JSON.parse(`<?php print_r(
  $_SESSION['trend_calculates']
); ?>`)

let day_costs = 0;
let current_rubicon = 0;
updateCurrentRubicon();

fillInvTable()
updateGameStats(sessionMoney, sessionDays, null)
$(document).ready(function () {
  // $(".invTableOverlay").removeClass("hidden")
  // level_record["round"] = 3
  basicPageFunctions()
  loadRubiconIfAt()
  makeSalesChart(overall_sales_history)
  makeTrendsChart(true)
  setTimeout(() => {
    if (!$("#trendsChart").length){
        $(".ersatzChart").removeClass("hidden")
        $(".ersatzChartUnderlay").removeClass("hidden")
        updateTrendsChart(trend_calculates)
    }
  }, 2000);
});

</script>