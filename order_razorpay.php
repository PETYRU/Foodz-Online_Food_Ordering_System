<html>
    <head>
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    </head>
    <body>
    <?php
        include("razorpay-php-2.9.0/Razorpay.php");
        session_start();
        error_reporting(0);

        // Use statement should be at the top level of the PHP code
        use Razorpay\Api\Api;

        $razorpayApiKey = "rzp_test_lc6zS4ZKAyFRcF";
        $razorpayApiSecret = "s14v82Qv0apucqWxb7UK4iYQ";

        // Add Razorpay integration here
        if ($_POST['mod'] == 'paypal') {  // Check if Razorpay is selected

            // Total amount for Razorpay (in paise, i.e., 100 = 1 INR)
            $item_total = 0;
            foreach ($_SESSION["cart_item"] as $item) {
                $item_total += ($item["price"]*$item["quantity"]);
            }

            $amount = $item_total * 100; // Convert to paise

            // Initialize Razorpay API Client
            $api = new Api($razorpayApiKey, $razorpayApiSecret);

            // Create a new order
            $orderData = [
                'receipt' => 'order_rcptid_11',
                'amount' => $amount,
                'currency' => 'INR',
                'payment_capture' => 1
            ];

            $order = $api->order->create($orderData);
            $orderId = $order['id']; // Store the order ID

            // Redirect to Razorpay payment gateway
            echo "<script>
                    var options = {
                        key: '$razorpayApiKey',
                        amount: $amount,
                        currency: 'INR',
                        name: 'Food Ordering System',
                        description: 'Order Payment',
                        order_id: '$orderId',
                        handler: function (response) {
                            // Collect data for the POST request
                            var payment_id = response.razorpay_payment_id;
                            var order_id = '$orderId';
                            var signature = response.razorpay_signature;

                            // Create a form and submit it with the POST request
                            var form = document.createElement('form');
                            form.method = 'POST';
                            form.action = 'verify_payment.php';

                            // Add hidden inputs to the form
                            var inputPaymentId = document.createElement('input');
                            inputPaymentId.type = 'hidden';
                            inputPaymentId.name = 'payment_id';
                            inputPaymentId.value = payment_id;
                            form.appendChild(inputPaymentId);

                            var inputOrderId = document.createElement('input');
                            inputOrderId.type = 'hidden';
                            inputOrderId.name = 'order_id';
                            inputOrderId.value = order_id;
                            form.appendChild(inputOrderId);

                            var inputSignature = document.createElement('input');
                            inputSignature.type = 'hidden';
                            inputSignature.name = 'signature';
                            inputSignature.value = signature;
                            form.appendChild(inputSignature);

                            // Append the form to the body
                            document.body.appendChild(form);

                            // Submit the form to send the POST request
                            form.submit();
                        },
                        prefill: {
                            name: '$_SESSION[user_name]',
                            email: '$_SESSION[user_email]',
                            contact: '$_SESSION[user_contact]'
                        }
                    };
                    var rzp = new Razorpay(options);
                    rzp.open();
                </script>";
        }
    ?>
    </body>
</html>
