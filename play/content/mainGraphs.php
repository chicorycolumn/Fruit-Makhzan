<?php

include '.././graph/graphs.php';

echo "<link rel='stylesheet' type='text/css' href='.././css/mainGraphs.css' />";

$mainGraphs =
  "

<div class='mainDiv mainDivGraphs1'>
" .
  $trends_graph_content .
  "
</div>

<div class='mainDiv mainDivGraphs2'>
" .
  $sales_graph_content .
  "
</div>

";

?>
