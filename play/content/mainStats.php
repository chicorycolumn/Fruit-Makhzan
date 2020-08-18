<?php

echo "<link rel='stylesheet' type='text/css' href='.././css/mainStats.css' />";

$mainStats = "
<div class='mainSubDivStats'>
  <div class='topHalf'>
    <div class='mainStatBox'>
      <img class='coin' src='.././images/circle.png' />
      <span class='mainStatText mainStatTextNumber' id='moneyStat'>--</span>
      <span class='mainStatText mainStatTextFaded'>gold dinars</span>  
    </div>

    <div class='mainStatBox'>
      <img class='coin' src='.././images/circle.png' />
      <button class='newDayButton' onClick=newDay()>NEW DAY</button>
    </div>
    
    <div class='mainStatBox'>
      <img class='coin' src='.././images/circle.png' />
      <span class='mainStatText mainStatTextNumber' id='daysStat'>--</span>
      <span class='mainStatText mainStatTextFaded'>days</span>
    </div>

  </div>
</div>
";

?>
