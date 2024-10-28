<?php
session_start();
include 'db.php';

// ตรวจสอบการเข้าสู่ระบบและสิทธิ์ Staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลสินค้าจากฐานข้อมูล
$sql = "SELECT * FROM Products";
$result = $conn->query($sql);

// สร้างตะกร้าหากยังไม่มี
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// เพิ่มสินค้าในตะกร้า
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];

    // ป้องกัน SQL Injection
    $product_query = $conn->prepare("SELECT stock, price FROM Products WHERE id = ?");
    $product_query->bind_param("i", $product_id);
    $product_query->execute();
    $product = $product_query->get_result()->fetch_assoc();

    if ($product && $product['stock'] >= $quantity) {
        $new_stock = $product['stock'] - $quantity;

        // ใช้ prepared statements เพื่ออัพเดทสต๊อก
        $update_stock = $conn->prepare("UPDATE Products SET stock = ? WHERE id = ?");
        $update_stock->bind_param("ii", $new_stock, $product_id);
        $update_stock->execute();

        // เพิ่มสินค้าลงในตะกร้า
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product_name,
                'quantity' => $quantity,
                'price' => $product['price']
            ];
        }
    } else {
        echo "<script>alert('สต๊อกสินค้าไม่เพียงพอ');</script>";
    }

    header("Location: staff_dashboard.php");
    exit();
}

// ฟังก์ชันชำระเงิน
if (isset($_POST['checkout'])) {
    $user_id = $_SESSION['user_id'];
    $total_amount = 0;

    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['quantity'] * $item['price'];
    }

    // ใช้ prepared statements เพื่อเพิ่มข้อมูลคำสั่งซื้อ
    $insert_order = $conn->prepare("INSERT INTO Orders (user_id, total_amount) VALUES (?, ?)");
    $insert_order->bind_param("id", $user_id, $total_amount);
    $insert_order->execute();
    $order_id = $conn->insert_id;

    foreach ($_SESSION['cart'] as $product_id => $item) {
        $quantity = $item['quantity'];
        $price = $item['price'];
        
        // ใช้ prepared statements เพื่อเพิ่มข้อมูลใน orderitems
        $insert_order_item = $conn->prepare("INSERT INTO orderitems (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $insert_order_item->bind_param("iiid", $order_id, $product_id, $quantity, $price);
        $insert_order_item->execute();
    }

    // บันทึกใบเสร็จในฐานข้อมูล
    $insert_receipt = $conn->prepare("INSERT INTO receipts (order_id, total_amount) VALUES (?, ?)");
    $insert_receipt->bind_param("id", $order_id, $total_amount);
    $insert_receipt->execute();

    $_SESSION['cart'] = [];
    header("Location: receipt.php?order_id=$order_id");
    exit();
}

// คำนวณราคารวมสินค้าในตะกร้า
$total_cart_amount = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_cart_amount += $item['quantity'] * $item['price'];
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-card {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-large {
            font-size: 1.25rem;
            padding: 10px 20px;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow p-4">
        <h2 class="text-center">ยินดีต้อนรับ, <?php echo htmlspecialchars($_SESSION['username']); ?> (Staff)</h2>
        <hr>
        <h3 class="mt-4">ค้นหาหมายเลขใบเสร็จ</h3>
        <a href="search_receipt.php" class="btn btn-primary">ไปยังหน้าค้นหาใบเสร็จ</a>

        <h3 class="mt-4">รายงานสินค้า</h3>
        <div class="row">
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="product-card">
                        <h5><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p>ราคา: <?php echo number_format($product['price'], 2); ?> THB</p>
                        <p>สต๊อก: <?php echo htmlspecialchars($product['stock']); ?></p>
                        <form method="POST" action="">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="mb-3">
                                <input type="number" name="quantity" value="1" min="1" max="<?php echo htmlspecialchars($product['stock']); ?>" class="form-control">
                            </div>
                            <button type="submit" name="add_to_cart" class="btn btn-primary btn-large">เพิ่มลงตะกร้า</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <h3 class="mt-4">ตะกร้าสินค้า</h3>
        <form method="POST" action="">
            <table class="table table-bordered mt-3">
                <thead class="table-secondary">
                <tr>
                    <th>ชื่อสินค้า</th>
                    <th>จำนวน</th>
                    <th>ราคา</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($_SESSION['cart'])): ?>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td><?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2" class="text-end"><strong>ราคารวม:</strong></td>
                        <td><strong><?php echo number_format($total_cart_amount, 2); ?> THB</strong></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">ไม่มีสินค้าในตะกร้า</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>

            <div class="text-end">
                <button type="submit" name="checkout" class="btn btn-warning">ชำระเงิน</button>
            </div>
        </form>
    </div>
    <div class="text-center mt-4">
        <a href="login.php" class="btn btn-secondary">ออกจากระบบ</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
