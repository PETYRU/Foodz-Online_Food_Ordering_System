<?php
include("connection/connect.php");

error_reporting(0); 
session_start();
$message = "";
$success = "";

// Brevo API key
$apiKey = getenv('BREVO_APIKEY');

// Function to generate OTP
function generateOTP($length = 6) {
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= mt_rand(0, 9); // Generate a random digit
    }
    return $otp;
}

// Send OTP Email via Brevo API
function sendOTPEmail($toEmail, $otp) {
    global $apiKey;

    $subject = 'Your OTP Code';
    $htmlContent = "<p>Your OTP code is: <strong>$otp</strong></p>";

    $emailData = [
        'sender' => [
            'email' => getenv('BREVO_EMAIL'), // Your sender email
            'name' => 'Foods'
        ],
        'to' => [
            [
                'email' => $toEmail
            ]
        ],
        'subject' => $subject,
        'htmlContent' => $htmlContent
    ];

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.brevo.com/v3/smtp/email');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: application/json',
        'api-key: ' . $apiKey,
        'content-type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));

    // Execute cURL request
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    } else {
        $responseData = json_decode($response, true);
        if (isset($responseData['messageId'])) {
            echo 'OTP sent successfully to ' . $toEmail;
        } else {
            echo 'Failed to send OTP. Response: ' . $response;
        }
    }
    curl_close($ch);
}

// Handle OTP submission and user authentication
if (isset($_POST['submit'])) {
    if (isset($_POST['otp'])) {
        // Verify OTP
        if ($_POST['otp'] == $_SESSION['otp']) {
            // OTP is correct, log the user in
            $username = $_SESSION['username'];
            $loginquery = "SELECT * FROM users WHERE username='$username'"; // Matching username
            $result = mysqli_query($db, $loginquery); // Executing
            $row = mysqli_fetch_array($result);

            if (is_array($row)) {
                $_SESSION["user_id"] = $row['u_id'];
                header("Location: index.php");
                exit();
            } else {
                $message = "Invalid Username or Password!";
            }
        } else {
            $message = "Invalid OTP. Please try again.";
        }
    } else {
        // Handle login attempt before sending OTP
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (!empty($_POST["submit"])) {
            $loginquery = "SELECT * FROM users WHERE username='$username' && password='" . md5($password) . "'"; // Selecting matching records
            $result = mysqli_query($db, $loginquery); // Executing
            $row = mysqli_fetch_array($result);

            if (is_array($row)) {
                $_SESSION['username'] = $username;  // Store username in session
                $otp = generateOTP(); // Generate OTP
                $_SESSION['otp'] = $otp; // Store OTP in session
                sendOTPEmail($row['email'], $otp); // Send OTP email
                $success = "OTP sent to your email. Please enter it below to complete login.";
            } else {
                $message = "Invalid Username or Password!";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login || Code Camp BD</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900|RobotoDraft:400,100,300,500,700,900'>
    <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>
    <link rel="stylesheet" href="css/login.css">
    <style type="text/css">
    #buttn {
        color: #fff;
        background-color: #5c4ac7;
    }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <header id="header" class="header-scroll top-header headrom">
        <nav class="navbar navbar-dark">
            <div class="container">
                <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#mainNavbarCollapse">&#9776;</button>
                <a class="navbar-brand" href="index.php"> <img class="img-rounded" src="images/logo.png" alt="" width="18%"> </a>
                <div class="collapse navbar-toggleable-md float-lg-right" id="mainNavbarCollapse">
                    <ul class="nav navbar-nav">
                        <li class="nav-item"> <a class="nav-link active" href="index.php">Home</a> </li>
                        <li class="nav-item"> <a class="nav-link active" href="restaurants.php">Restaurants</a> </li>
                        <?php
                        if (empty($_SESSION["user_id"])) {
                            echo '<li class="nav-item"><a href="login.php" class="nav-link active">Login</a> </li>
                                  <li class="nav-item"><a href="registration.php" class="nav-link active">Register</a> </li>';
                        } else {
                            echo '<li class="nav-item"><a href="your_orders.php" class="nav-link active">My Orders</a> </li>';
                            echo '<li class="nav-item"><a href="logout.php" class="nav-link active">Logout</a> </li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div style="background-image: url('images/img/pimg.jpg');">
        <div class="pen-title"></div>
        <div class="module form-module">
            <div class="toggle"></div>
            <div class="form">
                <h2>Login to your account</h2>
                <span style="color:red;"><?php echo $message; ?></span>
                <span style="color:green;"><?php echo $success; ?></span>
                <!-- If OTP is not sent, show the login form -->
                <?php if (!isset($_SESSION['otp'])): ?>
                    <form action="" method="post">
                        <input type="text" placeholder="Username" name="username" />
                        <input type="password" placeholder="Password" name="password" />
                        <input type="submit" id="buttn" name="submit" value="Login" />
                    </form>
                <?php else: ?>
                    <!-- If OTP is sent, show OTP input form -->
                    <form action="" method="post">
                        <input type="text" placeholder="Enter OTP" name="otp" />
                        <input type="submit" id="buttn" name="submit" value="Verify OTP" />
                    </form>
                <?php endif; ?>
            </div>
            <div class="cta">Not registered?<a href="registration.php" style="color:#5c4ac7;"> Create an account</a></div>
        </div>
    </div>

    <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    <div class="container-fluid pt-3"><p></p></div>
    <?php include "include/footer.php" ?>
</body>
</html>
