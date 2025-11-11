<?php
// 完整診斷測試
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>診斷測試結果</h2>";

// 測試 1: PHP 基本功能
echo "✅ PHP 版本: " . phpversion() . "<br><br>";

// 測試 2: 數據庫連接
echo "<h3>測試數據庫連接:</h3>";
$servername = "sql100.infinityfree.com";
$username = "if0_40388267";
$password = "Kailun5065";
$database = "if0_40388267_accounting";

$conn = new mysqli($servername, $username, $password, $database);

if($conn->connect_error){
    echo "❌ 數據庫連接失敗: " . $conn->connect_error . "<br>";
} else {
    echo "✅ 數據庫連接成功！<br>";
    
    // 測試 3: 檢查表是否存在
    echo "<h3>檢查數據表:</h3>";
    
    $tables = ['users', 'expenses'];
    foreach($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if($result->num_rows > 0) {
            echo "✅ 表 '$table' 存在<br>";
            
            // 檢查表結構
            $count_result = $conn->query("SELECT COUNT(*) as count FROM $table");
            $count_row = $count_result->fetch_assoc();
            echo "   - 記錄數: " . $count_row['count'] . "<br>";
        } else {
            echo "❌ 表 '$table' 不存在<br>";
        }
    }
    
    $conn->close();
}

echo "<br><h3>結論:</h3>";
echo "如果以上都顯示 ✅,那麼數據庫配置正確。<br>";
echo "接下來我們需要修復 index.php<br>";
?>
