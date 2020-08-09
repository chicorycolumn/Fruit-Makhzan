<?php
include '../utils/table_utils.php';
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
delete_manipulated_cookie();

if (!isset($_SESSION['gid'])) {
  header("Location: ../home");
  exit();
}

$inv_table_name = $_SESSION['inv_table_name'];
?>

<?php
$temp_name = "Mango" . time();
$temp_quantity = 50;
$temp_selling_price = 100;

$content =
  '<div class="row">   
                <div class="col">
            
                  <div class="box">
                    <div class="boxHeader">
                      <h3 class="boxTitle">Add Fruit</h3>
                    </div>

                    <form role="form">
                      <div class="boxBody">
                        <div class="formGroup">
                          <label>Name</label>
                          <input type="text" value=' .
  $temp_name .
  ' class="formControl" id="name" placeholder="Enter name">
                        </div>
                        <div class="formGroup">
                          <label>Quantity</label>
                          <input type="quantity" value=' .
  $temp_quantity .
  ' class="formControl" id="quantity" placeholder="Enter quantity">
                        </div>
                        <div class="formGroup">
                          <label>Selling price</label>
                          <input type="text" value=' .
  $temp_selling_price .
  ' class="formControl" id="selling_price" placeholder="Enter selling price">
                        </div>
                      </div>
      
                      <div class="boxFooter">
                        <input type="button" class="btn" onClick="AddFruit()" value="Submit"></input>
                      </div>
                    </form>
                  </div>
                  
                </div>
              </div>';
include '../master.php';
?>
<script>
  function AddFruit(){

        $.ajax(
        {
            type: "GET",
            url: '../api/fruit/create.php',
            dataType: 'json',
            data: {
              table_name: "<?php echo $inv_table_name; ?>",
              name: $("#name").val(),
              quantity: $("#quantity").val(),        
              selling_price: $("#selling_price").val(),
            },
            error: function (result) {
              console.log("An error occurred immediately in $.ajax request.", result)
              console.log(result.responseText)
            },
            success: function (result) {
              console.log("success")
                if (result['status']) {
                  console.log(result)
                    window.location.href = '../play';
                } else {
                    console.log(result['message']);
                    console.log(result["error"]);
                }
            }
        });
    }
</script>