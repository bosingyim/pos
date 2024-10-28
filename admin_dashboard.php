<?php
session_start();
include 'db.php';

// ตรวจสอบว่าผู้ใช้ได้เข้าสู่ระบบแล้วและมีระดับเป็น admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // หากยังไม่เข้าสู่ระบบให้กลับไปที่หน้า login
    exit();
}

// ดึงข้อมูลสินค้าจากฐานข้อมูล
$sql = "SELECT * FROM Products";
$result = $conn->query($sql);

// ดึงข้อมูลยอดขายรวมจากฐานข้อมูล
$sales_sql = "SELECT SUM(total_amount) AS total_sales FROM receipts"; 
$sales_result = $conn->query($sales_sql);
$sales_row = $sales_result->fetch_assoc();
$total_sales = $sales_row['total_sales'] ? number_format($sales_row['total_sales'], 2) : '0.00'; 

// ดึงข้อมูลยอดขายรายวัน
$trend_sql = "SELECT DATE(created_at) AS sale_date, SUM(total_amount) AS daily_sales FROM receipts GROUP BY sale_date ORDER BY sale_date";
$trend_result = $conn->query($trend_sql);
$trend_data = [];
while ($row = $trend_result->fetch_assoc()) {
    $trend_data[] = $row;
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- เพิ่ม Chart.js -->
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow p-4">
            <h2 class="text-center">ยินดีต้อนรับ, <?php echo htmlspecialchars($_SESSION['username']); ?> (Admin)</h2>
            <hr>
            <h3 class="mt-4">รายงานสินค้า</h3>

            

            <h3 class="mt-4">ยอดขายรวม</h3>
            <div class="alert alert-info text-center">
                ยอดขายรวมทั้งหมด: <?php echo $total_sales; ?> บาท
            </div>

            <h3 class="mt-4">การวิเคราะห์แนวโน้มยอดขาย</h3>
            <canvas id="salesTrendChart"></canvas> <!-- ช่องสำหรับแสดงกราฟ -->
            <script>
                const trendData = <?php echo json_encode($trend_data); ?>;
                const labels = trendData.map(data => data.sale_date);
                const sales = trendData.map(data => parseFloat(data.daily_sales));

                const ctx = document.getElementById('salesTrendChart').getContext('2d');
                const salesTrendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'ยอดขายรายวัน',
                            data: sales,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>

            <h3 class="mt-4">ดาวน์โหลดรายงานยอดขาย</h3>
            <a href="download_report.php" class="btn btn-primary">ดาวน์โหลดรายงาน</a>
            <table class="table table-bordered table-hover mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>ชื่อสินค้า</th>
                        <th>รายละเอียด</th>
                        <th>ราคา</th>
                        <th>สต๊อก</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($product = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['description']); ?></td>
                        <td><?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($product['stock']); ?></td>
                        <td>
                            <a href="edit_product.php?id=<?php echo urlencode($product['id']); ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                            <a href="delete_product.php?id=<?php echo urlencode($product['id']); ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?');">ลบ</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <h3 class="mt-4">เพิ่มสินค้าใหม่</h3>
            <form method="POST" action="add_product.php" class="mt-3">
                <div class="mb-3">
                    <label for="name" class="form-label">ชื่อสินค้า</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">รายละเอียด</label>
                    <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">ราคา</label>
                    <input type="number" id="price" name="price" step="0.01" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="stock" class="form-label">สต๊อก</label>
                    <input type="number" id="stock" name="stock" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100">เพิ่มสินค้า</button>
            </form>
            <h3 class="mt-4">ค้นหาหมายเลขใบเสร็จ</h3>
            <a href="search_receipt.php" class="btn btn-primary">ไปยังหน้าค้นหาใบเสร็จ</a>
            <div class="mt-4 text-center">
                <a href="logout.php" class="btn btn-secondary">ออกจากระบบ</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
