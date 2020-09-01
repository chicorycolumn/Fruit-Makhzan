<?php
include "../fusioncharts/fusioncharts.php"; ?>

<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>


<!-- ////////////////////// The little piece of text that it keeps appending to the bottom of the grpah
when I try feeddata, instead of actually feeding data, the thing it's adding is the value of the last column in the list
so here it's Decadence. Hmmm..... so it knows we're trying to do sth with the most recent value? -->


<?php
$trendsChart = new FusionCharts(
  "realtimecolumn",
  "trendsChart",
  "440",
  "220",
  "trendsChartContainer",
  "json",
  json_encode([
    "chart" => [
      "showrealtimevalue" => "1",
      "numdisplaysets" => 5,
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
    "categories" => [
      [
        "category" => [
          [
            "label" => "Love",
          ],
          [
            "label" => "Politics",
          ],
          [
            "label" => "Weather",
          ],
          [
            "label" => "Conformity",
          ],
          [
            "label" => "Decadence",
          ],
        ],
      ],
    ],
    "dataset" => [
      [
        "seriesname" => "",
        "data" => [
          [
            "label" => "Love",
            "value" => json_decode($_SESSION['trend_calculates'])->love,
          ],
          [
            "label" => "Politics",
            "value" => json_decode($_SESSION['trend_calculates'])->politics,
          ],
          [
            "label" => "Weather",
            "value" => json_decode($_SESSION['trend_calculates'])->weather,
          ],
          [
            "label" => "Conformity",
            "value" => json_decode($_SESSION['trend_calculates'])->conformity,
          ],
          [
            "label" => "Decadence",
            "value" => json_decode($_SESSION['trend_calculates'])->decadence,
          ],
        ],
      ],
    ],
  ])
);

$trendsChart->render();

//////////////////////////////

// // Chart Configuration stored in Associative Array
// $salesArrChartConfig = [
//   "chart" => [
//     "caption" => "Profit and Spending",
//     "subCaption" => "",
//     "xAxisName" => "",
//     "yAxisName" => "",
//     "yAxisMinValue" => 0,
//     "yAxisMaxValue" => 100,
//     "numberSuffix" => "",
//     "theme" => "fusion",
//     "bgColor" => "#ebffe0",
//   ],
// ];
// // An array of hash objects which stores data
// $salesArrChartData = [
//   ["Love", "50"],
//   ["Politics", "50"],
//   ["Weather", "50"],
//   ["Conformity", "50"],
//   ["Decadence", "50"],
// ];
// $salesArrLabelValueData = [];

// // Pushing labels and values
// for ($i = 0; $i < count($salesArrChartData); $i++) {
//   array_push($salesArrLabelValueData, [
//     "label" => $salesArrChartData[$i][0],
//     "value" => $salesArrChartData[$i][1],
//   ]);
// }
// $salesArrChartConfig["data"] = $salesArrLabelValueData;

// // JSON Encode the data to retrieve the string containing the JSON representation of the data in the array.
// $salesJsonEncodedData = json_encode($salesArrChartConfig);

// $salesChart = new FusionCharts(
//   "column2d",
//   "salesChart",
//   "440",
//   "200",
//   "salesChartContainer",
//   "json",
//   $salesJsonEncodedData
// );

// $salesChart->render();

////////////////////////////

$trends_graph_content =
  '<div class="chartContainer" id="trendsChartContainer">Chart will render here!</div>';

$sales_graph_content =
  ' <div class="chartContainer" id="salesChartContainer">Chart will render here!</div>';


?>
