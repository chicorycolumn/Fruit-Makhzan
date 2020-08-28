<?php

echo "<link rel='stylesheet' type='text/css' href='.././css/invTable.css' />";

$invTable = "
<div class='invTable'>
<div class='row'>
<div class='col'>
<div class='box'>
  <div class='boxBody'>
    <table id='inventory' class='table'>
      <thead>
      <tr>
        <th class='th_inventory'>Name</th>
        <th class='th_inventory'>Quantity</th>
        <th class='th_inventory'>Selling price</th>
        <th class='th_inventory' style='cursor: help;' onClick=printDevDataHTML()>Restock price</th>
        <th class='th_inventory' style='cursor: help;' onClick=printDevData1()>Factors</th>
        <th class='th_inventory' style='cursor: help;' onClick=printDevData2()>Restock</th>
      </tr>
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
