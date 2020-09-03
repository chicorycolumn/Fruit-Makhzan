<?php

echo "<link rel='stylesheet' type='text/css' href='.././css/invTable.css' />";

$invTable =
  "
<div class='invTable'>
<div class='row'>
<div class='col'>
<div class='box'>
  <div class='boxBody'>
    <table id='inventory' class='table'>
      <thead>
      <tr>" .
  "<th>Name" .
  "</th>" .
  "<th>Quantity ⓘ" .
  "<span class='tooltip'>How many of each fruit you currently have.</span>" .
  "</th>" .
  "<th>Selling price ⓘ" .
  "<span class='tooltip'>How much you're selling each fruit for. Click to change it.</span>" .
  "</th>" .
  "<th>Restock price ⓘ" .
  "<span class='tooltip'>The price it will cost to restock each fruit, along with a pin showing its current popularity. A higher popularity means people will buy your fruit it for a higher price, but also raises the price it will cost you to restock.</span>" .
  "</th>" .
  "<th id='factorsTH'>Factors ⓘ" .
  "<span class='tooltip'>What affects each fruit's popularity. The larger text is the major factor, while the smaller is the minor. Red text with a flipped arrow means that this fruit's popularity is affected negatively by this factor, so `↻weather` in red means this fruit is actually more popular in worse (lower scoring) weather.</span>" .
  "</th>" .
  "<th id='restockTH'>Restock ⓘ" .
  "<span class='tooltip'>Buy each fruit at its restock price to increase your inventory. Click buttons or type numbers to specify the quantity you want, then click BUY.</span>" .
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
