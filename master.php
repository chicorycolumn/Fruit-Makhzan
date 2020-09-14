<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../css/master.css" >
<link rel="stylesheet" type="text/css" href="../css/navbar.css" >
<link rel='stylesheet' type='text/css' href='../css/buttons.css' />
<link rel='stylesheet' type='text/css' href='../css/global.css' />
<link rel='stylesheet' type='text/css' href='../css/scroll.css' />
<link href="https://fonts.googleapis.com/css2?family=Stylish&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Averia+Gruesa+Libre&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Averia+Libre:wght@700&display=swap" rel="stylesheet">

  <meta charset="utf-8">
  <title>Fruit Makhzan</title>

<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>

  <link rel="icon" href="../images/blueberry_favicon2.ico" type="image/ico">
</head>
<body>
<div class='background'></div>

  <header class="navbar">

    <div class=navbarSubholder>

      <a class='navbarLink' href="../home">
      <div class=navbarLinkSubholder>
      <img class='navbarImage' src='../images/blueberry_sized_shadow2.png' alt='A blueberry'>  
      <p class='navbarText' >Home</p>
      </div>
      </a>    

      <div class='navbarLink' id='navbarLinkPlay'>
      <div class=navbarLinkSubholder>
      <img class='navbarImage' id='navbarImagePlay' src='../images/banana_sized_shadow2.png' alt='A banana'>  
      <p class='navbarText' >Play</p>
      </div>
</div> 

      <a class='navbarLink' href="../about">
      <div class=navbarLinkSubholder>
      <img class='navbarImage' src='../images/pear_sized_shadow2.png' alt='A pear'>  
      <p class='navbarText' >About</p>
      </div>
      </a>   
      
    </div>
          
  </header>

  <div class="contentWrapper" id="contentWrapper">
    <section class="content">
      <?php echo $content; ?>
    </section>
  </div>




<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</body>

</html>