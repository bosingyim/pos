<?php
session_start();
session_destroy(); // ทำลาย session
header("Location: login.php"); // เปลี่ยนเส้นทางกลับไปยังหน้า login
exit();
?>
