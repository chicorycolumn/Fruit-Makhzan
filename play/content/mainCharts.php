<?php

include '.././chart/charts.php';

echo "<link rel='stylesheet' type='text/css' href='.././css/mainCharts.css' />";

$mainCharts =
  "

<div class='mainDiv' id='mainDivCharts1'>
" .
  '<div class="chartContainer" id="trendsChartContainer">
  
  <img class="ersatzChart hidden" src="../images/ersatz_chart2.png">
  <div class="ersatzChartUnderlay hidden">
  
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

<div class='mainDiv' id='mainDivCharts2'>
" .
  ' <div class="chartContainer" id="salesChartContainer"></div>' .
  "
</div>

";

?>
