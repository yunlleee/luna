<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "sewa_luna";

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$conn) die("DB error");

if ($_SERVER["REQUEST_METHOD"] != "POST") die("Invalid request");

$table_no = isset($_POST["table_no"]) ? (int)$_POST["table_no"] : 0;
$names  = isset($_POST["dish_name"]) ? $_POST["dish_name"] : [];
$qtys   = isset($_POST["qty"]) ? $_POST["qty"] : [];
$prices = isset($_POST["unit_price"]) ? $_POST["unit_price"] : [];

if ($table_no <= 0) die("Table error");
if (count($names) == 0) die("Cart empty");

/*total  */
$total = 0;
for ($i = 0; $i < count($names); $i++) {
  $total += $qtys[$i] * $prices[$i];
}

/*orders */
mysqli_query($conn, "INSERT INTO orders (table_no, total_price) VALUES ($table_no, $total)");
$order_id = mysqli_insert_id($conn);

/*order_items */
for ($i = 0; $i < count($names); $i++) {
  $dish = mysqli_real_escape_string($conn, $names[$i]);
  $q = (int)$qtys[$i];
  $p = (float)$prices[$i];
  $line = $q * $p;

  mysqli_query($conn, "INSERT INTO order_items (order_id, dish_name, amount, unit_price, line_total)
                       VALUES ($order_id, '$dish', $q, $p, $line)");
}

echo "OK: Your order has been received";
mysqli_close($conn);
?>