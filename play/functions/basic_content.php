<?php

$content = "";

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
  $mainGraphs .
  '


  </div>
    
  <div class="mainDiv mainDivTable">
    ' .
  $invTable .
  '
  </div>
  ';

?>
