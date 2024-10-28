<?php
session_start();
include 'db.php';

// ตรวจสอบว่าผู้ใช้ได้เข้าสู่ระบบแล้วและมีระดับเป็น admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // หากยังไม่เข้าสู่ระบบให้กลับไปที่หน้า login
    exit();
}

// ประกาศตัวแปรสำหรับเก็บข้อความแจ้งเตือน
$message = '';
$message_type = '';

// ตรวจสอบว่ามีการส่งข้อมูลฟอร์มมา
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจากฟอร์ม
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    // ตรวจสอบข้อมูลที่ได้รับ
    if (empty($name) || empty($description) || $price <= 0 || $stock < 0) {
        $message = "กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง.";
        $message_type = "danger";
    } else {
        // สร้างคำสั่ง SQL เพื่อเพิ่มสินค้าลงในฐานข้อมูล
        $sql = "INSERT INTO Products (name, description, price, stock) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdi", $name, $description, $price, $stock);

        // ตรวจสอบการเพิ่มสินค้า
        if ($stmt->execute()) {
            $message = "เพิ่มสินค้าสำเร็จ!";
            $message_type = "success";
        } else {
            $message = "เกิดข้อผิดพลาด: " . $stmt->error;
            $message_type = "danger";
        }

        $stmt->close();
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มสินค้า - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow p-4">
            <h2 class="text-center">เพิ่มสินค้าใหม่</h2>
            <hr>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="mt-3">
                <div class="mb-3">
                    <label for="name" class="form-label">ชื่อสินค้า</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">รายละเอียด</label>
                    <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">ราคา</label>
                    <input type="number" id="price" name="price" step="0.01" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="stock" class="form-label">สต๊อก</label>
                    <input type="number" id="stock" name="stock" class="form-control" min="0" required>
                </div>
                <button type="submit" class="btn btn-success w-100">เพิ่มสินค้า</button>
            </form>

            <div class="mt-4 text-center">
                <a href="admin_dashboard.php" class="btn btn-secondary">กลับไปยัง Dashboard</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
