<?php
include "../fusioncharts/fusioncharts.php"; ?>

<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>

<script>

function makeSalesGraph(XTC){

    if (!XTC){
        let XTC = {
            love: <?php echo json_decode($_SESSION['trend_calculates'])
              ->love; ?>,
            politics: <?php echo json_decode($_SESSION['trend_calculates'])
              ->politics; ?>,
            weather: <?php echo json_decode($_SESSION['trend_calculates'])
              ->weather; ?>,
            conformity: <?php echo json_decode($_SESSION['trend_calculates'])
              ->conformity; ?>,
            decadence: <?php echo json_decode($_SESSION['trend_calculates'])
              ->decadence; ?>,
        }
    }

    const dataSource = {
    chart: {
        caption: "Factors Affecting Popularity",
        xaxisname: "",
        yaxisname: "",
        numbersuffix: "",
        theme: "fusion",
        yAxisMinValue: 0,
        yAxisMaxValue: 100,
        bgColor: "#ebffe0",
    },
    data: [
        {
        label: "Love",
        value: XTC.love
        },
        {
        label: "Politics",
        value: XTC.politics
        },
        {
        label: "Weather",
        value: XTC.weather
        },
        {
        label: "Conformity",
        value: XTC.conformity
        },
        {
        label: "Decadence",
        value: XTC.decadence
        }
    ]
    };

    FusionCharts.ready(function() {
    var myChart = new FusionCharts({
        type: "column2d",
        renderAt: "trendsChartContainer",
        id: "trendsChart",
        width: "440",
        height: "220",
        dataFormat: "json",
        dataSource
    }).render();
    });

}

</script>