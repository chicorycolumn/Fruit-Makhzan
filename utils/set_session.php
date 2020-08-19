<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$labels = ['money_stat', 'days_stat', 'trend_calculates'];
$response = ["status" => false, "message" => ""];

foreach ($labels as $label) {
  if (array_key_exists($label, $_GET)) {
    $_SESSION[$label] = $_GET[$label];
    $response['status'] = true;
    $response['message'] .= "Successfully set " . $label . "   ";
  }
}

if ($response['message'] == "") {
  $response['status'] = true;
  $response['message'] = "Nothing to set into session.";
}

echo json_encode($response);
?> 