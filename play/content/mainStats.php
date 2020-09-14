<?php

echo "<link rel='stylesheet' type='text/css' href='.././css/mainStats.css' />";

$mainStats = "
<div class='mainSubDivStats'>

  <div class='bottomHalf'>
  
    <div class='islandHolder islandHolder1'>
      <img id='island1' class='island hidden' src='.././images/island2_small.png' alt='A small tropical island'>
      <img id='island2' class='island hidden' src='.././images/island2_small.png' alt='A small tropical island'>
    </div>

  <div class='islandHolder islandHolder2'>
    <img id='island3' class='island hidden' src='.././images/island2_small.png' alt='A small tropical island'>
    <img id='island4' class='island hidden' src='.././images/island2_small.png' alt='A small tropical island'>
  </div>

  </div>

  <div class='topHalf'>
    <div class='mainStatBox'>
      <img class='coin' src='.././images/coin_circle7a_pale.png' id='coin' alt='A simplified coin background' />
      <span class='mainStatText mainStatTextNumber' id='moneyStat'>--</span>
      <span class='mainStatText mainStatTextFaded' id='moneyStatSubtitle'>gold dinars</span>  
    </div>

    <div class='mainStatBox'>
      <img class='coin' src='.././images/clock_circle7aa_pale.png' id='clock' alt='A simplified clock background' />
      <button class='newDayButton' id='newDayButton' onClick=newDay()>New day</button>
    </div>
    
    <div class='mainStatBox'>
      <img class='coin' src='.././images/sun_circle7a_pale.png' id='sun' alt='A simplified sun background' />
      <div class='daysYearsHolder'>

        <div class='daysYearsSubholder'>
          <span class='mainStatText mainStatTextNumber' id='yearsStat'>~~</span>
          <span class='mainStatText mainStatTextFaded'>year</span>
        </div>

        <div class='daysYearsSubholder'>
          <span class='mainStatText mainStatTextNumber mainStatTextNumberDays' id='daysStat'>--</span>
          <span class='mainStatText mainStatTextFaded'>days</span>
        </div>
        
      </div>
    </div>

  </div>
</div>
";

?>
