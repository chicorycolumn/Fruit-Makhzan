<?php
include "../fusioncharts/fusioncharts.php"; ?>

<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>

<script>

let trendsChart = null;

FusionCharts.ready(function() {
  trendsChart = new FusionCharts({
    id: "trendsChart",
    type: 'realtimecolumn',
    renderAt: 'trendsChartContainer',
    width: '440',
    height: '220',
    dataFormat: 'json',
    dataSource: {
      "chart": {
        "animationDuration": 0,
        "caption": "Factors Affecting Popularity",
        "xaxisname": "",
        "yaxisname": "",
        "numbersuffix": "",
        "theme": "fusion",
        "yAxisMinValue": 0,
        "yAxisMaxValue": 100,
        "bgColor": "#ebffe0",
        "numdisplaysets": "5",
        "showRealTimeValue": "0"
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

function updateTrendsGraph(XTC){

        let currentChartData = trendsChart.getChartData()
        
        let XTCArray = []

        let keys = ["love", "weather", "politics", "conformity", "decadence"]
        
        keys.forEach(key => {
            let capitalisedKey = key[0].toUpperCase() + key.slice(1).toLowerCase()
            XTCArray.push({"label": capitalisedKey, "value": XTC[key]})
        })

        currentChartData["dataset"][0]["data"] = XTCArray

    trendsChart.setChartData(currentChartData)
}


</script>