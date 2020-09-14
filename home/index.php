<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
include './includes.php';

$res = check_gid();
?>

<?php
$content =
  '  <div class="dialogHolder">
<img class="scroll" src=".././images/scroll2a.png" alt="A simplified old-timey parchment scroll background">
<div class="dialogBox">

  <div class="dialogBoxInner dialogBoxInnerHome">
    <div class="dialogBoxInnerInner">
      <p class=dialogBoxTextSmall>You are Ibn al-Baitar, medieval Andalusian scientist.</p>
      <p class=dialogBoxTextSmall>With your botanical knowledge, on a whim you open a Fruit Makhzan (storehouse).</p>
      <p class=dialogBoxTextSmall>You aim to become the best fruit seller in all of Al-Andalus!</p>
    </div>
  </div>

  <div class="homeButtonHolder">

    <button class="homeButton" onClick=loadPrevious() ' .
  (check_gid() ? "" : "disabled") .
  '>
      
      <img class="homeButtonImage" src="../images/cherry_sized_shadow2.png" alt="A cherry" />
      <p class="noMarginPadding homeButtonText">CONTINUE</p>
    
    </button>

    <button class="homeButton" onClick=startNewGame()>
      <img class="homeButtonImage" src="../images/banana_sized_shadow2.png" alt="A banana" />
      <p class="noMarginPadding homeButtonText">NEW GAME</p>
    </button>

  </div>

</div>
</div>
';

include '../master.php';
?>

<script>

$(document).ready(function(){
  basicPageFunctions()
});

</script>

