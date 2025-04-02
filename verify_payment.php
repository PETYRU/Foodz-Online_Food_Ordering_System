<html>
<head>
    <style>
        /* Add styles for the success message and green tick */
        .success-message {
            text-align: center;
            padding: 50px;
        }

        .success-message h2 {
            color: #28a745;
            font-size: 36px;
        }

        .success-message .tick {
            font-size: 100px;
            color: #28a745;
            margin-bottom: 20px;
        }

        /* Optional: Add a fade-out effect */
        .redirect-message {
            font-size: 18px;
            margin-top: 20px;
            color: #555;
        }
    </style>
</head>
<body>
    <?php
        session_start();
        include("connection/connect.php");
        include("razorpay-php-2.9.0/Razorpay.php");
        error_reporting(0); 
        use Razorpay\Api\Api;

        $keyId = getenv('RZP_KEYID');
        $keySecret = getenv('RZP_SECRET');

        $api = new Api($keyId, $keySecret);

        $payment_id = $_POST['payment_id'];
        $order_id = $_POST['order_id'];
        $signature = $_POST['signature'];

        $data = $order_id . "|" . $payment_id;
        $generated_signature = hash_hmac('sha256', $data, $keySecret);

        if ($generated_signature == $signature) {
            $username = $_SESSION["username"];
            $loginquery = "SELECT * FROM users WHERE username='$username'";
            $result = mysqli_query($db, $loginquery); // Executing
            $row = mysqli_fetch_array($result);

            sendOrderConfirmationEmail($row['email'], $_SESSION["cart_item"]);
            foreach ($_SESSION["cart_item"] as $item) {                            
                $SQL = "insert into users_orders(u_id,title,quantity,price) values('".$_SESSION["user_id"]."','".$item["title"]."','".$item["quantity"]."','".$item["price"]."')";
                mysqli_query($db, $SQL);
                unset($_SESSION["cart_item"]);
                unset($item["title"]);
                unset($item["quantity"]);
                unset($item["price"]);
            }

            $success = "Thank you. Your order has been placed!";
            
            // Send order confirmation email
            

            // Clear the cart
            unset($_SESSION["cart_item"]);

            // Display success message
            echo "<div class='success-message'>";
            echo "<div class='tick'>✔</div>";
            echo "<h2>Payment Successful!</h2>";
            echo "<p>$success</p>";
            echo "<div class='redirect-message'>Redirecting to your orders...</div>";

            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'your_orders.php';
                    }, 3000);
                  </script>";
            echo "</div>";
        } else {
            echo "<div class='success-message'>";
            echo "<div class='tick'>❌</div>";
            echo "<h2>Payment Failed!</h2>";
            echo "<p>Sorry, there was an issue with your payment.</p>";
            echo "<div class='redirect-message'>Redirecting to try again...</div>";
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'retry_payment.php';
                    }, 3000);
                  </script>";
            echo "</div>";
        }

        // Function to send order confirmation email with order details
        function sendOrderConfirmationEmail($toEmail, $cartItems) {
            $apiKey = $apiKey = getenv('BREVO_APIKEY');

            $subject = 'Order Confirmation';

            // Construct the HTML content with order details
            $htmlContent = "<p>Thank you for your order! Your payment has been successfully processed. Here are your order details:</p>";
            $htmlContent .= "<table style='width:100%; border-collapse: collapse;'>";
            $htmlContent .= "<tr>
                                <th style='border: 1px solid #ddd; padding: 8px;'>Item</th>
                                <th style='border: 1px solid #ddd; padding: 8px;'>Quantity</th>
                                <th style='border: 1px solid #ddd; padding: 8px;'>Price</th>
                             </tr>";

            foreach ($cartItems as $item) {
                $htmlContent .= "<tr>
                                    <td style='border: 1px solid #ddd; padding: 8px;'>{$item["title"]}</td>
                                    <td style='border: 1px solid #ddd; padding: 8px;'>{$item["quantity"]}</td>
                                    <td style='border: 1px solid #ddd; padding: 8px;'>₹{$item["price"]}</td>
                                 </tr>";
            }

            $htmlContent .= "</table>";
            $htmlContent .= "<p>If you have any questions about your order, please feel free to contact us.</p>";

            $emailData = [
                'sender' => [
                    'email' => getenv('BREVO_EMAIL'),
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
                if (!isset($responseData['messageId'])) {
                    echo 'Failed to send confirmation email. Response: ' . $response;
                }
            }
            curl_close($ch);
        }
    ?>
</body>
</html>
