<?php

echo "<link rel='stylesheet' type='text/css' href='.././css/invTable.css' />";

$invTable =
  "
<div class='invTable'>

  <div class='invTableOverlay hidden'>

    <img class='invTableOverlayOval' src='.././images/blurryBeige1.png'/>
    <div class='invTableOverlayTextHolder'>
      <p class='invTableOverlayText'>Waiting for database...</p>
      <p class='invTableOverlayImageTag'>Art by Jonas Mosesson (CC BY-NC-ND 4.0)</p>
    </div>
    <img class='invTableOverlayOval2' src='.././images/blurryBeige2.png'/>
    <a href='https://jonasmosesson.se/' target='_blank'>
    <img class='invTableOverlayImage' src='.././images/avocado.gif'/>
    </a>
  </div>

<div class='row'>
<div class='col'>
<div class='box'>
  <div class='boxBody'>
  <img id='tooltipToggle' src='../images/crossed_out_info.png' onclick='toggleTooltips()'/>
    <table id='inventory' class='table'>
      <thead>
      <tr>" .
  "<th>Name" .
  "</th>" .
  "<th class='thHover'>Quantity ⓘ" .
  "<span class='tooltip THtooltip'>How many of each fruit you have.</span>" .
  "</th>" .
  "<th class='thHover'>Selling price ⓘ" .
  "<span class='tooltip THtooltip'>How much you're selling each fruit for. Click to change it.</span>" .
  "</th>" .
  "<th class='thHover'>Restock price ⓘ" .
  "<span class='tooltip THtooltip'>The price it costs you to buy more. The pin shows each fruit's current popularity. A higher popularity means people will buy your fruit for a higher price, but also increases the restock price.</span>" .
  "</th>" .
  "<th class='thHover' id='factorsTH'>Factors ⓘ" .
  "<span class='tooltip THtooltip'>The factors controlling each fruit's popularity. Red text with a flipped arrow means an inverse affect. So a fruit dependent on «↻weather» will be more popular in bad (lower scoring) weather. </span>" .
  "</th>" .
  "<th class='thHover' id='restockTH'>Restock ⓘ" .
  "<span class='tooltip THtooltip'>Buy more fruit at its restock price to increase your inventory. Click the buttons or type numbers for the quantity you want, then click BUY.</span>" .
  "</th>" .
  "</tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>
</div>
</div>
</div>
";
?>
