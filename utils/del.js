let createFruitForm =
  '<form class="createFruitForm">' +
  '<div class="boxBody">' +
  '<div class="formGroup">' +
  "<label>Name</label>" +
  '<input type="text" value=' +
  temp_name +
  ' class="formControl" id="name" placeholder="Enter name"' +
  'onkeypress="return /[0-9a-zA-Z]/.test(event.key)" ' +
  ">" +
  "</div>" +
  '<div class="formGroup">' +
  "<label>Quantity</label>" +
  '<input type="quantity" value=' +
  temp_quantity +
  ' class="formControl" id="quantity" placeholder="Enter quantity">' +
  "</div>" +
  '<div class="formGroup">' +
  "<label>Selling price</label>" +
  '<input type="text" value=' +
  temp_selling_price +
  ' class="formControl" id="selling_price" placeholder="Enter selling price">' +
  "</div>" +
  "</div>" +
  '<div class="boxFooter">' +
  '<input type="button" class="btn" onClick="addFruit()" value="Submit"></input>' +
  "</div>" +
  "</form>";
