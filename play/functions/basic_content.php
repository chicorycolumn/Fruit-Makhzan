<?php

$content = "";

if ($show_dev_data) {
  $_SESSION['show_dev_data'] = 1;
  $content =
    '<h1>' .
    $gid .
    '</h1><button onClick="printSingle(null)">Check Game Data</button>';
}

$content .=
  '
  <div class="dialogHolder hidden">
    <img class="scroll" src=".././images/scroll2a.png">
    <div class="dialogBox">
      <div class="dialogBoxInner">
        <div class="dialogBoxInnerInner">
          <p class=dialogBoxText></p>
        </div>
      </div>
      <p class="dialogBoxButton" onClick=advance()>☞Very well</p>
    </div>
  </div>

  <div class="mainDiv mainDivStats">
    ' .
  $mainStats .
  '
  </div>

  <div class="holderForHorizontalMainDivs">


    ' .
  // $mainGraphs .
  '


  </div>
    
  <div class="mainDiv mainDivTable">
    ' .
  $invTable .
  '
  </div>
  ';

?>
