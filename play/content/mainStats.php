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
      <button class='newDayButton'>NEW DAY</button>
    </div>
    
    <div class='mainStatBox'>
      <img class='coin' src='.././images/circle.png' />
      <span class='mainStatText mainStatTextNumber' id='daysStat'>--</span>
      <span class='mainStatText mainStatTextFaded'>days</span>
    </div>

    <div class='islandHolder'>
      <img class='island' src='.././images/island2_small.png'>
      <img class='island' src='.././images/island2_small.png'>
      <img class='island' src='.././images/island2_small.png'>
      <img class='island' src='.././images/island2_small.png'>
      <img class='island' src='.././images/island2_small.png'>
    </div>

  </div>
</div>
";

?>
