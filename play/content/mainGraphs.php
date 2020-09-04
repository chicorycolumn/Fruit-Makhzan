<?php

include '.././graph/graphs.php';

echo "<link rel='stylesheet' type='text/css' href='.././css/mainGraphs.css' />";

$mainGraphs =
  "

<div class='mainDiv mainDivGraphs1'>
" .
  '<div class="chartContainer" id="trendsChartContainer">
  
  <img class="ersatzGraph hidden" src="../images/ersatz_graph2.png">
  <div class="ersatzGraphUnderlay hidden">
  
    <div class="ersatzBarHolder">
      <div class="ersatzBar" id="ersatzBar_love"></div>
    </div>
      
    <div class="ersatzBarHolder">
      <div class="ersatzBar" id="ersatzBar_weather"></div>
    </div>
      
    <div class="ersatzBarHolder">
      <div class="ersatzBar" id="ersatzBar_politics"></div>
    </div>
      
    <div class="ersatzBarHolder">
      <div class="ersatzBar" id="ersatzBar_conformity"></div>
    </div>
      
    <div class="ersatzBarHolder">
      <div class="ersatzBar" id="ersatzBar_decadence"></div>
    </div>
  
  </div>
  </div>' .
  "
</div>

<div class='mainDiv mainDivGraphs2'>
" .
  ' <div class="chartContainer" id="salesChartContainer"></div>' .
  "
</div>

";

?>
