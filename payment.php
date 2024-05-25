<?php
session_start();

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "register";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate a 6-digit OTP
function generate_otp() {
    return rand(100000, 999999);
}

// Function to send email
function send_otp_email($email, $message) {
    $subject = "Your Laundry Service OTP";
    $headers = "From: swatimirashi298@gmail.com";
    return mail($email, $subject, $message, $headers);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $customer_id = $_POST['customer_id'];

    // Fetch customer's email from the database
    $sql = "SELECT email FROM registration WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();

    if ($email) {
        $_SESSION['email'] = $email;
        $_SESSION['id'] = $id;
        $otp = generate_otp();
        $_SESSION['OTP'] = $otp;
        $message = "Your laundry is successfully done!!! Your OTP is: $otp";

        if (send_otp_email($email, $message)) {
            echo "OTP has been sent to your email: $email";
        } else {
            echo "Failed to send OTP to your email.";
        }
    } else {
        echo "Customer not found.";
    }
} elseif (!isset($_SESSION['OTP'])) {
    // Ensure OTP is set if it hasn't been already
    $_SESSION['OTP'] = generate_otp();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

<body>
    <div class="nav-bar">
        <div class="title">
            <h3>Welcome to my website</h3>
        </div>
    </div>

    <?php if (!isset($_SESSION['email'])): ?>
        <form method="POST" class="form-login">
            <div class="form-group">
                <input type="text" class="form-control" name="customer_id" placeholder="Enter your customer ID" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Send OTP</button>
        </form>
    <?php else: ?>
        <form class="form-login">
            <div class="form-group">
                <input type="text" class="form-control" name="otp" id="OTP" placeholder="Enter OTP" required>
            </div>
            <button type="button" class="btn btn-primary btn-lg" id="verify-otp">Verify OTP</button>
        </form>
    <?php endif; ?>

    <script>
        $(document).ready(function() {
            $("#verify-otp").click(function () {
                var otpInput = document.getElementById("OTP").value;
                var sessionOtp = "<?php echo $_SESSION['OTP']; ?>";
                if (otpInput === sessionOtp) {
                    window.location.replace("logged-in.php");
                } else {
                    alert("OTP does not match");
                }
            });
        });
    </script>
</body>
</html>