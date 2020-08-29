<?php
// Include the ../src/fusioncharts.php file that contains functions to embed the charts./
include "../fusioncharts/fusioncharts.php"; ?>

<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>

<?php
/////////////////////////

// Chart Configuration stored in Associative Array
$trendsArrChartConfig = [
  "chart" => [
    "caption" => "Factors Affecting Popularity",
    "subCaption" => "",
    "xAxisName" => "",
    "yAxisName" => "",
    "yAxisMinValue" => 0,
    "yAxisMaxValue" => 100,
    "numberSuffix" => "",
    "theme" => "fusion",
    "bgColor" => "#ebffe0",
  ],
];
// An array of hash objects which stores data
$trendsArrChartData = [
  ["Love", "50"],
  ["Politics", "50"],
  ["Weather", "50"],
  ["Conformity", "50"],
  ["Decadence", "50"],
];
$trendsArrLabelValueData = [];

// Pushing labels and values
for ($i = 0; $i < count($trendsArrChartData); $i++) {
  array_push($trendsArrLabelValueData, [
    "label" => $trendsArrChartData[$i][0],
    "value" => $trendsArrChartData[$i][1],
  ]);
}
$trendsArrChartConfig["data"] = $trendsArrLabelValueData;

// JSON Encode the data to retrieve the string containing the JSON representation of the data in the array.
$trendsJsonEncodedData = json_encode($trendsArrChartConfig);

$trendsChart = new FusionCharts(
  "column2d",
  "trendsChart",
  "440",
  "200",
  "trendsChartContainer",
  "json",
  $trendsJsonEncodedData
);

$trendsChart->render();

//////////////////////////////

// Chart Configuration stored in Associative Array
$salesArrChartConfig = [
  "chart" => [
    "caption" => "Profit and Spending",
    "subCaption" => "",
    "xAxisName" => "",
    "yAxisName" => "",
    "yAxisMinValue" => 0,
    "yAxisMaxValue" => 100,
    "numberSuffix" => "",
    "theme" => "fusion",
    "bgColor" => "#ebffe0",
  ],
];
// An array of hash objects which stores data
$salesArrChartData = [
  ["Love", "50"],
  ["Politics", "50"],
  ["Weather", "50"],
  ["Conformity", "50"],
  ["Decadence", "50"],
];
$salesArrLabelValueData = [];

// Pushing labels and values
for ($i = 0; $i < count($salesArrChartData); $i++) {
  array_push($salesArrLabelValueData, [
    "label" => $salesArrChartData[$i][0],
    "value" => $salesArrChartData[$i][1],
  ]);
}
$salesArrChartConfig["data"] = $salesArrLabelValueData;

// JSON Encode the data to retrieve the string containing the JSON representation of the data in the array.
$salesJsonEncodedData = json_encode($salesArrChartConfig);

$salesChart = new FusionCharts(
  "column2d",
  "salesChart",
  "440",
  "200",
  "salesChartContainer",
  "json",
  $salesJsonEncodedData
);

$salesChart->render();

////////////////////////////

$trends_graph_content =
  '<div class="chartContainer" id="trendsChartContainer">Chart will render here!</div>';

$sales_graph_content =
  ' <div class="chartContainer" id="salesChartContainer">Chart will render here!</div>';


?>
