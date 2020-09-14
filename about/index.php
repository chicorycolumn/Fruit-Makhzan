<?php

include './includes.php';

$content = "

<div class='homeHolder'>

<a class='homeCoinHolder' href='mailto:c.matus.contact@gmail.com?subject=Makhzan Feedback'>
<img class='homeCoin' src='.././images/coin_circle7a_pale.png' alt='A simplified coin background' />
<div class='homeCoinTextHolder'>
<p class='homeCoinText'>Contact the creator</p>
<img class='homeCoinFruit' src='../images/peach_sized_shadow2.png' alt='A peach'>
</div>
</a>

<div class='homeSunHolder'>
<img class='homeSun' src='.././images/sun_circle7a_pale.png' alt='A simplified sun background' />
<div class='homeSunTextHolder'>
<p class='homeSunText'>Fruit Makhzan is a database-based inventory game written in PHP</p>
</div>
</div>

<a class='homeCoinHolder' href='https://c-m-portfolio.netlify.app' target='_blank'>
<img class='homeCoin' src='.././images/coin_circle7a_pale.png' alt='A simplified coin background' />
<div class='homeCoinTextHolder'>
<p class='homeCoinText'>See other projects</p>
<img class='homeCoinFruit' src='../images/pomegranate_sized_shadow2.png' alt='A pomegranate'>
</div>
</a>

</div>

";

include '../master.php';
?>

<script>
    $(document).ready(function () {
  basicPageFunctions()
});
</script>
