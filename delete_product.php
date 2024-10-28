<?php
session_start();
include 'db.php';

// ตรวจสอบว่าผู้ใช้ได้เข้าสู่ระบบแล้วและมีระดับเป็น admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$product_id = $_GET['id'];

// ลบสินค้าจากฐานข้อมูล
$sql = "DELETE FROM Products WHERE id = $product_id";

if ($conn->query($sql) === TRUE) {
    echo "ลบสินค้าสำเร็จ";
} else {
    echo "เกิดข้อผิดพลาด: " . $conn->error;
}

$conn->close();
header("Location: admin_dashboard.php"); // เปลี่ยนเส้นทางกลับไปที่หน้า dashboard
exit();
?>
