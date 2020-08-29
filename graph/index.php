<?php
// Include the ../src/fusioncharts.php file that contains functions to embed the charts./
include "../fusioncharts/fusioncharts.php"; ?>

<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>

<?php
// Chart Configuration stored in Associative Array
$arrChartConfig = [
  "chart" => [
    "caption" => "Factors affecting popularity",
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
$arrChartData = [
  ["Love", "50"],
  ["Politics", "50"],
  ["Weather", "50"],
  ["Conformity", "50"],
  ["Decadence", "50"],
];
$arrLabelValueData = [];

// Pushing labels and values
for ($i = 0; $i < count($arrChartData); $i++) {
  array_push($arrLabelValueData, [
    "label" => $arrChartData[$i][0],
    "value" => $arrChartData[$i][1],
  ]);
}
$arrChartConfig["data"] = $arrLabelValueData;

// JSON Encode the data to retrieve the string containing the JSON representation of the data in the array.
$jsonEncodedData = json_encode($arrChartConfig);

// chart object
$Chart = new FusionCharts(
  "column2d",
  "MyFirstChart",
  "450",
  "200",
  "chartContainer",
  "json",
  $jsonEncodedData
);

// Render the chart
$Chart->render();

$graph_content = '<html>
<body>
    <center>
        <div id="chartContainer">Chart will render here!</div>
    </center>
</body>
</html>';


?>
