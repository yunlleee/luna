<?php
// start the session and connect the database
session_start();
include 'db.php';

// Process form if request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
     
    // Get the input from reservation form
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $guests = $_POST['guests'];

    if (empty($name) || empty($email) || empty($date) || empty($time) || empty($guests)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: reservations.html");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format";
        header("Location: reservations.html");
        exit();
    }

    // send the data into database
    $query = "INSERT INTO reservations (name, email, reservation_date, reservation_time, guests)
              VALUES ('$name', '$email', '$date', '$time', '$guests')";

    // set the messege if the data is saved in the database successfully or not
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Reservation successful";
    } else {
        $_SESSION['error'] = "Something went wrong";
    }

    // stay in reservations page
    header("Location: reservations.html");
}
// block the direct access to php
else {
    echo "Invalid request";
}
?>
