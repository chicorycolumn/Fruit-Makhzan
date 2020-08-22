<?php

echo "<link rel='stylesheet' type='text/css' href='.././css/mainStats.css' />";

$mainStats = "
<div class='mainSubDivStats'>

  <div class='bottomHalf'>
  
    <div class='islandHolder'>
      <img id='island1' class='island hidden' src='.././images/island2_small.png'>
      <img id='island2' class='island hidden' src='.././images/island2_small.png'>
      <img id='island3' class='island hidden' src='.././images/island2_small.png'>
      <img id='island4' class='island hidden' src='.././images/island2_small.png'>
      <img id='island5' class='island hidden' src='.././images/island2_small.png'>
    </div>

  </div>

  <div class='topHalf'>
    <div class='mainStatBox'>
      <img class='coin' src='.././images/circle.png' />
      <span class='mainStatText mainStatTextNumber' id='moneyStat'>--</span>
      <span class='mainStatText mainStatTextFaded'>gold dinars</span>  
    </div>

    <div class='mainStatBox'>
      <img class='coin' src='.././images/circle.png' />
      <button class='newDayButton'>New day</button>
      <img class='crown hidden' src='.././images/crown1_medium.png'>
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
