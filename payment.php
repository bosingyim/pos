<?php
session_start();

// ตรวจสอบว่ามีข้อมูลในตะกร้าหรือไม่
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "ตะกร้าว่าง!";
    exit;
}

// คำนวณราคาทั้งหมด
$totalPrice = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงิน</title>
</head>
<body>
    <h2>ชำระเงินสินค้า</h2>
    <form action="receipt.php" method="POST">
        <label for="amount">จำนวนเงินที่ชำระ:</label>
        <input type="number" id="amount" name="amount_received" required>
        <input type="hidden" name="total_price" value="<?php echo $totalPrice; ?>">
        <button type="submit">ชำระเงิน</button>
    </form>
</body>
</html>
