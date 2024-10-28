<?php
session_start();
include 'db.php';

// ตรวจสอบการเข้าสู่ระบบและสิทธิ์ Staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

// ดึงหมายเลขใบเสร็จจาก URL
$order_id = $_GET['order_id'];

// ดึงข้อมูลคำสั่งซื้อจากฐานข้อมูล
$order_query = $conn->prepare("SELECT * FROM Orders WHERE id = ?");
$order_query->bind_param("i", $order_id);
$order_query->execute();
$order = $order_query->get_result()->fetch_assoc();

// ดึงข้อมูลรายการสินค้าในใบเสร็จ
$order_items_query = $conn->prepare("SELECT oi.*, p.name FROM orderitems oi JOIN Products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$order_items_query->bind_param("i", $order_id);
$order_items_query->execute();
$order_items = $order_items_query->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จ - หมายเลขใบเสร็จ <?php echo htmlspecialchars($order_id); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }
            .receipt {
                border: 1px solid #000;
                padding: 20px;
                width: 80%;
                margin: auto;
            }
            .receipt h2, .receipt h4 {
                margin: 0;
                text-align: center;
            }
            .receipt th, .receipt td {
                text-align: left;
                padding: 5px;
            }
        }
        .receipt {
            margin: 0 auto;
            padding: 20px;
            width: 300px; /* Adjust width to resemble a typical receipt */
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background: #fff;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="receipt shadow">
        <h2>ใบเสร็จ</h2>
        <h4>หมายเลขใบเสร็จ: <?php echo htmlspecialchars($order_id); ?></h4>
        <h5>รายละเอียดการสั่งซื้อ</h5>
        <p>ยอดรวม: <?php echo number_format($order['total_amount'], 2); ?> THB</p>
        
        <table class="table table-bordered mt-3">
            <thead class="table-secondary">
            <tr>
                <th>ชื่อสินค้า</th>
                <th>จำนวน</th>
                <th>ราคา</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($item = $order_items->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo number_format($item['price'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <div class="text-center mt-4 no-print">
            <button onclick="window.print();" class="btn btn-success">พิมพ์ใบเสร็จ</button>
            <a href="staff_dashboard.php" class="btn btn-primary">กลับไปที่แดชบอร์ด</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
