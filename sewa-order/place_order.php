?php
//database connection details
$dbhost ="localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "sewa_luna";
//connect to mysql database
$conn = mysqli_connect($dbhost,$dbuser,$dbpass, $dbname);
//if connection fails, stop the script
if (!$conn){
  echo "Database connection error";
  exit();
}
//allows only post requests(form/fetch)
if ($_SERVER["REQUEST_METHOD"] != "POST"){
  echo "Invalid request";
  exit();
}
//get arrays sent from javascript (cart)
$table_no = isset($_POST["table_no"]) ? (int)$_POST["table_no"] :0;
$names  =isset($_POST["dish_name"]) ? $_POST["dish_name"] : [];
$qtys   = isset($_POST["qty"]) ? $_POST["qty"] : [];
$prices =isset($_POST["unit_price"]) ? $_POST["unit_price"] : [];
//validation checks
if ($table_no<= 0){
  echo "Table error";
  exit();
}
if (count($names)== 0){
  echo "Cart empty";
  exit();
}
/*calculate totall price  */
$total = 0;
//loop throuh all ordereds items and calculate total
for ($i = 0; $i < count($names);$i++) {
  $quantity = $qtys[$i];
  $price =$prices[$i];
  $total = $total + ($quantity*$price);
}
//insert into orders table
//save main order info(table number and total price)
mysqli_query(
  $conn, 
  "INSERT INTO orders (table_no, total_price) VALUES($table_no, $total)"
);
//get id of the new created order
$order_id = mysqli_insert_id($conn);
//save each ordered item seperately
for ($i= 0; $i<count($names); $i++) {
  //protect against sql injection
  $dish = mysqli_real_escape_string($conn, $names[$i]);
  //convert values to corrext types
  $q =(int)$qtys[$i];
  $p = (float)$prices[$i];
  //calculate the line total (price*quantity)
  $line= $q*$p;
  //insert the item into otder_item tablre
  mysqli_query(
    $conn, "INSERT INTO order_items (order_id, dish_name, amount, unit_price, line_total) 
    VALUES ($order_id, '$dish', $q, $p, $line)"
    );
}
//send succes message back to javascrilt
echo "OK: Your order has been received";
//close the database conection
mysqli_close($conn);
?>
