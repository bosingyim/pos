<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>ค้นหาใบเสร็จ</title>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">ค้นหาใบเสร็จ</h1>
    <form method="POST" class="mb-4">
        <div class="form-group">
            <label for="receipt_number">หมายเลขใบเสร็จ</label>
            <input type="text" name="receipt_number" class="form-control" id="receipt_number" required>
        </div>
        <button type="submit" class="btn btn-primary">ค้นหา</button>
    </form>

    <?php
    // เชื่อมต่อฐานข้อมูล
    $host = 'localhost';
    $username = 'root'; // เปลี่ยนเป็นชื่อผู้ใช้ของคุณ
    $password = ''; // เปลี่ยนเป็นรหัสผ่านของคุณ
    $database = 'pos'; // ชื่อฐานข้อมูล

    // สร้างการเชื่อมต่อ
    $conn = new mysqli($host, $username, $password, $database);

    // ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // ตรวจสอบว่ามีการส่งข้อมูลฟอร์มหรือไม่
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['receipt_number'])) {
        $receipt_number = htmlspecialchars($_POST['receipt_number']);
        
        // ค้นหาใบเสร็จ
        $sql = "SELECT * FROM receipts WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $receipt_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // แสดงข้อมูลใบเสร็จ
            while ($row = $result->fetch_assoc()) {
                echo "<div class='alert alert-success'>ค้นหาหมายเลขใบเสร็จ: " . $row['order_id'] . "<br>ราคารวม: " . $row['total_amount'] . " บาท</div>";

                // ค้นหาสินค้าที่เกี่ยวข้องจาก orderitems
                $sql_items = "SELECT oi.quantity, p.name, oi.price 
                              FROM orderitems oi 
                              JOIN products p ON oi.product_id = p.id 
                              WHERE oi.order_id = ?";
                $stmt_items = $conn->prepare($sql_items);
                $stmt_items->bind_param("i", $row['order_id']);
                $stmt_items->execute();
                $result_items = $stmt_items->get_result();

                if ($result_items->num_rows > 0) {
                    echo "<h4>รายการสินค้า:</h4><table class='table table-bordered'>";
                    echo "<thead><tr><th>ชื่อสินค้า</th><th>ราคา</th><th>จำนวน</th></tr></thead><tbody>";
                    while ($item = $result_items->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $item['name'] . "</td>
                                <td>" . $item['price'] . " บาท</td>
                                <td>" . $item['quantity'] . "</td>
                              </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<div class='alert alert-warning'>ไม่พบสินค้าที่เกี่ยวข้อง</div>";
                }
            }
        } else {
            echo "<div class='alert alert-danger'>ไม่พบหมายเลขใบเสร็จนี้</div>";
        }

        $stmt->close();
    }

    $conn->close();
    ?>

    <div class="text-center mt-4">
        
        <button onclick="goBack()" class="btn btn-secondary">ย้อนกลับ</button> <!-- ปุ่มย้อนกลับ -->
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<script>
function goBack() {
    window.history.back();
}
</script>