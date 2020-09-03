<?php
include "../fusioncharts/fusioncharts.php"; ?>

<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>

<script>

let salesNumDisplay = 50
let trendsChart = null;

FusionCharts.ready(function() {
  trendsChart = new FusionCharts({
    id: "trendsChart",
    type: 'realtimecolumn',
    renderAt: 'trendsChartContainer',
    width: '500',
    height: '260',
    dataFormat: 'json',
    dataSource: {
      "chart": {
        "caption": "Factors Affecting Popularity",
        "palettecolors": "#FFC400",
        "xaxisname": "",
        "yaxisname": "",
        "numbersuffix": "",
        "theme": "fusion",
        "yAxisMinValue": 0,
        "yAxisMaxValue": 100,
        "bgColor": "#ebffe0",
        "numdisplaysets": "5",
        "showRealTimeValue": "0",
        "showToolTip": "0"
      },
      "categories": [{
        "category": [{
          "label": "Love"
        }, {
          "label": "Weather"
        },{
          "label": "Politics"
        },{
          "label": "Conformity"
        },{
          "label": "Decadence"
        }]
      }],
      "dataset": [{
        "data": [{
            "label": "Love",
          "value": <?php echo json_decode($_SESSION['trend_calculates'])
            ->love; ?>
        },{
            "label": "Weather",
          "value": <?php echo json_decode($_SESSION['trend_calculates'])
            ->weather; ?>
        },{
            "label": "Politics",
          "value": <?php echo json_decode($_SESSION['trend_calculates'])
            ->politics; ?>
        },{
            "label": "Conformity",
          "value": <?php echo json_decode($_SESSION['trend_calculates'])
            ->conformity; ?>
        },{
            "label": "Decadence",
          "value": <?php echo json_decode($_SESSION['trend_calculates'])
            ->decadence; ?>
        }]
      }]
    },
  });

  trendsChart.render();
});

function updateTrendsGraph(trendCalculates){

        let currentChartData = trendsChart.getChartData()
        
        let trendCalculatesArray = []

        let keys = ["love", "weather", "politics", "conformity", "decadence"]
        
        keys.forEach(key => {
            let capitalisedKey = key[0].toUpperCase() + key.slice(1).toLowerCase()
            trendCalculatesArray.push({"label": capitalisedKey, "value": trendCalculates[key]})
        })

        currentChartData["dataset"][0]["data"] = trendCalculatesArray

    trendsChart.setChartData(currentChartData)
}

let salesChart = null;

function makeSalesGraph(initial_data){

  let keys = Object.keys(initial_data)
  let newCategorySet = []
  let salesObject = {
            "seriesname": "Sales",
            "color": "#008000",
            "data": []
  }

  let spendingObject = {
            "seriesname": "Spending",
            "color": "#FFC400",
            "data": []
  }

  keys.forEach(key => {
    newCategorySet.push({"label": key.toString()})
    salesObject["data"].push({"value": initial_data[key]["profit"].toString()})
    spendingObject["data"].push({"value": initial_data[key]["costs"].toString()})
  })

  FusionCharts.ready(function() {
    salesChart = new FusionCharts({
      type: 'realtimeline',
      renderAt: 'salesChartContainer',
      width: '500',
      height: '260',
      dataFormat: 'json',
      dataSource: {
        "chart": {
          "theme": "fusion",
          "caption": "Daily Transactions",
          "xAxisName": "",
          "yAxisName": "Gold Dinars",
          "numDisplaySets": salesNumDisplay,
          "setadaptiveymin": "1",
          "setadaptivesymin": "1",
          "labeldisplay": "auto",
          "bgColor": "#ebffe0",
          "showRealTimeValue": "0",
          "yAxisMaxValue": "50",
          "showToolTip": "0"
        },
        "categories": [{
          "category": newCategorySet
        }],
        "dataset": [salesObject, spendingObject],
        "trendlines": [{
          "line": [{
            "startvalue": "0",
            "color": "#62B58F",
            "valueOnRight": "",
          }]
        }]
      }
    }).render();
  });
}

function updateSalesGraph(overall_sales_history){
  
  let day = Object.keys(overall_sales_history).sort((a, b) => b - a)[0].toString()
  let newSalesValue = overall_sales_history[day]['profit']
  let newSpendingValue = overall_sales_history[day]['costs']

  let currentChartData = salesChart.getChartData()
  let salesArray = currentChartData["dataset"][0]["data"]
  let spendingArray = currentChartData["dataset"][1]["data"]
  let categoryArray = currentChartData["categories"][0]["category"]

  let arrays = [salesArray, spendingArray, categoryArray]
  arrays.forEach(array => {if (array.length > salesNumDisplay){array.shift()}})

  salesArray.push({"value": newSalesValue})
  spendingArray.push({"value": newSpendingValue})
  categoryArray.push({"label": day.toString()})

  currentChartData["dataset"][0]["data"] = salesArray
  currentChartData["dataset"][1]["data"] = spendingArray
  currentChartData["categories"][0]["category"] = categoryArray

  salesChart.setChartData(currentChartData)
}

</script>