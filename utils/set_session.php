<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$_SESSION['color'] = "deer";
$_SESSION['money_stat'] = $_GET['money_stat'];
$_SESSION['days_stat'] = $_GET['days_stat'];
$_SESSION['trend_calculates'] = $_GET['trend_calculates'];

echo 1;
?> 