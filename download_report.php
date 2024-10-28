<?php
require('fpdf.php'); // ใช้เส้นทางที่ถูกต้อง

// เชื่อมต่อกับฐานข้อมูล
include 'db.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// สร้าง PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16); // ใช้ฟอนต์ Arial
$pdf->Cell(0, 10, 'Report', 0, 1, 'C');

// แสดงวันที่
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Date: ' . date('d/m/Y'), 0, 1, 'C'); // แสดงวันที่ปัจจุบันในรูปแบบ DD/MM/YYYY

// ดึงข้อมูลยอดขาย
$sql = "SELECT * FROM receipts";
$result = $conn->query($sql);

// สร้างหัวข้อ
$pdf->SetFont('Arial', 'B', 12); // ใช้ฟอนต์ Arial
$pdf->Cell(40, 10, 'ID', 1);
$pdf->Cell(40, 10, 'Order ID', 1);
$pdf->Cell(40, 10, 'Total Amount', 1);
$pdf->Cell(60, 10, 'Created At', 1);
$pdf->Cell(30, 10, 'Date', 1);
$pdf->Ln();

// ตัวแปรสำหรับยอดขายรวม
$total_sales = 0;

// เพิ่มข้อมูลลงใน PDF
$pdf->SetFont('Arial', '', 12); // ใช้ฟอนต์ Arial
while($row = $result->fetch_assoc()) {
    $pdf->Cell(40, 10, $row['id'], 1);
    $pdf->Cell(40, 10, $row['order_id'], 1);
    $pdf->Cell(40, 10, number_format($row['total_amount'], 2), 1);
    $pdf->Cell(60, 10, $row['created_at'], 1);
    $pdf->Cell(30, 10, $row['date'], 1);
    $pdf->Ln();

    // คำนวณยอดขายรวม
    $total_sales += $row['total_amount'];
}

// แสดงยอดขายรวม
$pdf->Cell(40, 10, '', 0); // ว่างเพื่อข้ามคอลัมน์แรก
$pdf->Cell(40, 10, 'Total:', 1);
$pdf->Cell(40, 10, number_format($total_sales, 2), 1);
$pdf->Cell(60, 10, '', 0); // ว่างเพื่อข้ามคอลัมน์นี้
$pdf->Cell(30, 10, '', 0); // ว่างเพื่อข้ามคอลัมน์นี้
$pdf->Ln();

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();

// ส่ง PDF ให้ดาวน์โหลด
$pdf->Output('D', 'Report_samgirlShop.pdf');
exit();
?>
