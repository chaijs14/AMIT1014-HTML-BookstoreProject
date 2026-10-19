<?php
session_start();
require 'config.php';

$order_id = $_GET['order_id'] ?? 0;
$amount = $_GET['amount'] ?? 0;

if ($order_id == 0) {
    header("Location: member_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Secure Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://www.paypal.com/sdk/js?client-id=AepsmvSnjj7JV8KfqRwt5htP3E5chIdUuf0jBEYaTO06DyuNcxgimpt_SHKoScCr-emhoyrEtpqMtoCB&currency=MYR"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/payment.css">
</head>
<body>
    <div class="box">
        <h2>Complete Payment</h2>
        <p>Order #<?php echo htmlspecialchars($order_id); ?></p>
        <h3 style="color: #2c3e50;">Total: RM <?php echo number_format($amount, 2); ?></h3>
        <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
        
        <div id="paypal-button-container"></div>

        <a href="member_dashboard.php" class="exit-link" onclick="return confirm('Cancel payment? Your order will be saved as Pending.')">
            Exit and Return to Shop
        </a>
    </div>

    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{ amount: { value: '<?php echo $amount; ?>' } }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    window.location.href = "payment_process.php?order_id=<?php echo $order_id; ?>&status=Completed";
                });
            },
            onCancel: function (data) {
                alert("Payment cancelled.");
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>