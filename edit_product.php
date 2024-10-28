<?php
session_start();
include 'db.php';

// ตรวจสอบว่าผู้ใช้ได้เข้าสู่ระบบแล้วและมีระดับเป็น admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลสินค้าที่ต้องการแก้ไข
$product_id = $_GET['id'];
$sql = "SELECT * FROM Products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // อัปเดตข้อมูลสินค้า
    $sql = "UPDATE Products SET name=?, description=?, price=?, stock=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdii", $name, $description, $price, $stock, $product_id);

    if ($stmt->execute()) {
        $message = "แก้ไขสินค้าสำเร็จ!";
        $message_type = "success";
    } else {
        $message = "เกิดข้อผิดพลาด: " . $stmt->error;
        $message_type = "danger";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow p-4">
            <h2 class="text-center">แก้ไขสินค้า</h2>
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
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo $product['name']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">รายละเอียด</label>
                    <textarea id="description" name="description" class="form-control" rows="3"><?php echo $product['description']; ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">ราคา</label>
                    <input type="number" id="price" name="price" class="form-control" value="<?php echo $product['price']; ?>" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label for="stock" class="form-label">สต๊อก</label>
                    <input type="number" id="stock" name="stock" class="form-control" value="<?php echo $product['stock']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">บันทึกการเปลี่ยนแปลง</button>
            </form>

            <div class="mt-4 text-center">
                <a href="admin_dashboard.php" class="btn btn-secondary">กลับไปยัง Dashboard</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
