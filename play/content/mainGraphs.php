<?php

include '.././graph/graphs.php';

echo "<link rel='stylesheet' type='text/css' href='.././css/mainGraphs.css' />";

$mainGraphs =
  "

<div class='mainDiv mainDivGraphs1'>
" .
  '<div class="chartContainer" id="trendsChartContainer">Trends chart to render here.</div>' .
  "
</div>

<div class='mainDiv mainDivGraphs2'>
" .
  ' <div class="chartContainer" id="salesChartContainer">Sales chart to render here.</div>' .
  "
</div>

";

?>
