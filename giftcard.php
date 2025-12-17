<?php
// start the session and connect the database
session_start();
include 'db.php';

// process form if request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // get the input from giftcard payment section, only name, email and phonenumber to send the gift cards
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    if (empty($name) || empty($email) || empty($phone)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: giftcard.html");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format";
        header("Location: giftcard.html");
        exit();
    }

    // store email value in cookie for users
    setcookie("gift_email", $email, time() + (86400 * 30), "/");

    // send the data into database
    $query = "INSERT INTO giftcards (name, email, phone)
              VALUES ('$name', '$email', '$phone')";

    // set the messege if the data is saved in the database successfully or not
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Gift card request submitted";
    } else {
        $_SESSION['error'] = "Something went wrong";
    }

    // stay in gift card page
    header("Location: giftcard.html");
}

// block the direct access to php
else {
    echo "Invalid request";
}
?>
