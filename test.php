<?php
// 簡單測試頁面
echo "PHP 正常工作！<br>";
echo "當前時間: " . date('Y-m-d H:i:s') . "<br>";

// 測試數據庫連接
$servername = "sql100.infinityfree.com";
$username = "if0_40388267";
$password = "Kailun15065";
$database = "if0_40388267_accounting";

$conn = mysqli_connect($servername, $username, $password, $database);

if ($conn) {
    echo "✅ 數據庫連接成功！<br>";
    mysqli_close($conn);
} else {
    echo "❌ 數據庫連接失敗: " . mysqli_connect_error() . "<br>";
}
?>
