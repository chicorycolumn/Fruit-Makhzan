<?php
include "../fusioncharts/fusioncharts.php"; ?>

<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>

<script>


let salesChart = null;

FusionCharts.ready(function() {
  salesChart = new FusionCharts({
    type: 'realtimeline',
    renderAt: 'salesChartContainer',
    width: '700',
    height: '400',
    dataFormat: 'json',
    dataSource: {
      "chart": {
        "theme": "fusion",
        "caption": "Visitors",
        "xAxisName": "Day"
      },
      "categories": [{
        "category": [{
            "label": "1"
          },
          {
            "label": "2"
          },
          {
            "label": "3"
          },
          {
            "label": "4"
          },
          {
            "label": "5"
          },
          {
            "label": "6"
          },
          {
            "label": "7"
          }
        ]
      }],
      "dataset": [{
          "seriesname": "Sales",
          "data": [{
              "value": "5"
            },
            {
              "value": "10"
            },
            {
              "value": "5"
            },
            {
              "value": "200"
            },
            {
              "value": "0"
            },
            {
              "value": "0"
            },
            {
              "value": "5"
            }
          ]
        },
        {
          "seriesname": "Spending",
          "data": [{
              "value": "33"
            },
            {
              "value": "33"
            },
            {
              "value": "55"
            },
            {
              "value": "55"
            },
            {
              "value": "44"
            },
            {
              "value": "11"
            },
            {
              "value": "66"
            }
          ]
        }
      ],
      "trendlines": [{
        "line": [{
          "startvalue": "17022",
          "color": "#62B58F",
          "valueOnRight": "1",
          "displayvalue": "Average"
        }]
      }]
    }
  }).render();
});

function updateSalesGraph(){

  console.log("quack")
  console.log(salesChart)
  // return

// let currentChartData = trendsChart.getChartData()

// let XTCArray = []

// let keys = ["love", "weather", "politics", "conformity", "decadence"]

// keys.forEach(key => {
//     let capitalisedKey = key[0].toUpperCase() + key.slice(1).toLowerCase()
//     XTCArray.push({"label": capitalisedKey, "value": XTC[key]})
// })

// currentChartData["dataset"][0]["data"] = XTCArray
let label
let value
          label = "8"
          value = 100;
          strData = "$seriesname=Profits&label=" + label + "&value=" + value;
          salesChart.feedData(strData)

          label = "8"
          value = 1000;
          strData = "$seriesname=Spending&label=" + label + "&value=" + value;
          salesChart.feedData(strData)

}

</script>