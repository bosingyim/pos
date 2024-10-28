<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// เชื่อมต่อฐานข้อมูล
include 'db.php';

// ดึงข้อมูลสินค้าจากฐานข้อมูล
$sql = "SELECT * FROM Products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>หน้าหลัก - สินค้า</title>
</head>
<body>
    <h1>ยินดีต้อนรับ <?= htmlspecialchars($username) ?></h1>
    <h2>แสดงรายการสินค้า</h2>
    <ul>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<li>" . htmlspecialchars($row['name']) . " - " . htmlspecialchars($row['price']) . " บาท</li>";
            }
        } else {
            echo "ไม่มีสินค้า";
        }
        ?>
    </ul>

    <a href="logout.php">ออกจากระบบ</a>
</body>
</html>

<?php $conn->close(); ?>
