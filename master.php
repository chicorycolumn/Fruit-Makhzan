<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../css/master.css" type="text/css">
  <meta charset="utf-8">
  <title>Fruit-Makhzan</title>
  <link rel="icon" href="../images/favicon.ico" type="image/ico">
</head>
<body>
<div class="wrapper" onclick="false ? window.location.href = '../play' : ''">

  <header class="mainHeader">
 
  <a href="../home">Home</a>
  <a href="../play/create.php">Create</a>
  <a href="../play">Play</a>
          
          
  </header>

  <div class="contentWrapper">
    <section class="content">
      <?php echo $content; ?>
    </section>
  </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</body>

</html>