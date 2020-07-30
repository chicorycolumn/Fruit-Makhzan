<?php 
  $content = '<div class="row">
          
                <div class="col">
            
                  <div class="box">
                    <div class="boxHeader">
                      <h3 class="boxTitle">Add Fruit</h3>
                    </div>

                    <form role="form">
                      <div class="boxBody">
                        <div class="formGroup">
                          <label>Name</label>
                          <input type="text" class="formControl" id="name" placeholder="Enter name">
                        </div>
                        <div class="formGroup">
                          <label>Quantity</label>
                          <input type="quantity" class="formControl" id="quantity" placeholder="Enter quantity">
                        </div>
                        <div class="formGroup">
                          <label>Selling price</label>
                          <input type="text" class="formControl" id="selling_price" placeholder="Enter selling price">
                        </div>
                      </div>
      
                      <div class="boxFooter">
                        <input type="button" class="btn" onClick="AddFruit()" value="Submit"></input>
                      </div>
                    </form>
                  </div>
                  
                </div>
              </div>';
  include('../master.php');
?>
<script>
  function AddFruit(){
    console.log("inside AddFruit fxn")

        $.ajax(
        {
            type: "POST",
            url: '../api/fruit/create.php',
            dataType: 'json',
            data: {
         
                name: $("#name").val(),
                quantity: $("#quantity").val(),        
                selling_price: $("#selling_price").val(),
       
            },
            error: function (result) {
              console.log("error!")
                alert(result.responseText);
            },
            success: function (result) {
                if (result['status'] == true) {
                    alert("Successfully Added New Fruit!");
                    window.location.href = '../play';
                }
                else {
                  console.log("else couldn't")
                    alert(result['message']);
                }
            }
        });
    }
</script>