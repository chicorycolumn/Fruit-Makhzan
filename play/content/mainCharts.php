<?php

include '.././chart/charts.php';

echo "<link rel='stylesheet' type='text/css' href='.././css/mainCharts.css' />";
echo "<link rel='stylesheet' type='text/css' href='.././css/invTable.css' />";

$mainCharts =
  "

<div class='mainDiv' id='mainDivCharts1'>
<div class='trendsChartTooltipHolder'>

  <div class='trendsChartTooltipSymbol trendsChartTooltipSymbolHover noMarginPadding'>ⓘ
    <span class='tooltip trendsTooltip'>The love score is random every day.</span>
  </div>

  <div class='trendsChartTooltipSymbol trendsChartTooltipSymbolHover noMarginPadding'>ⓘ
    <span class='tooltip trendsTooltip' id='tooltipWeather'>The weather score goes in 90-day seasons.</span>
  </div>

  <div class='trendsChartTooltipSymbol trendsChartTooltipSymbolHover noMarginPadding'>ⓘ
    <span class='tooltip trendsTooltip'>The politics score changes weekly.</span>
  </div>

  <div class='trendsChartTooltipSymbol trendsChartTooltipSymbolHover noMarginPadding'>ⓘ
    <span class='tooltip trendsTooltip'>Whether the conformity score is rising, falling, or maintaining, it's more likely to keep doing that.</span>
  </div>

  <div class='trendsChartTooltipSymbol trendsChartTooltipSymbolHover noMarginPadding'>ⓘ
    <span class='tooltip trendsTooltip'>Decadence is high when you recently made a lot of profit. It's low when you recently had a lot of costs.</span>
  </div>

</div>
" .
  '<div class="chartContainer" id="trendsChartContainer">

  <img class="ersatzChart hidden" src="../images/ersatz_chart2.png" alt="A column chart showing popularity trends over time" >
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
