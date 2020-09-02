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
$_SESSION['show_dev_data'] = 0;

setcookie("makhzan", $_SESSION['gid'], time() + 3600 * 24 * 30, "/");
$gid = $_SESSION['gid'];
$inv_table_name = $_SESSION['inv_table_name'];

include './includes.php';
include '../master.php';
?>

<script>
$(document).ready(function () {
  setZoom()
  loadRubiconIfAt()
  makeSalesGraph()
});

let level_record = JSON.parse(`<?php echo $_SESSION['level_record']; ?>`);
let sessionMoney = "<?php echo $_SESSION['money_stat']; ?>"
let sessionDays = "<?php echo $_SESSION['days_stat']; ?>"
let trend_calculates = JSON.parse(`<?php print_r(
  $_SESSION['trend_calculates']
); ?>`)

let day_costs = 0;
let overall_sales_history = {}; ///*
let current_rubicon = 0;
updateCurrentRubicon();

fillInvTable()
updateGameStats(sessionMoney, sessionDays, null)

</script>