<?php

$trend_calculates = $_GET['trend_calculates'];

foreach ($trends as $key => $value) {
  if ($trends[$key] > 99) {
    $trends[$key] = 1;
  } else {
    $trends[$key]++;
  }
}

// print_r(json_encode($trend_calculates));
print_r($trend_calculates);

?>
