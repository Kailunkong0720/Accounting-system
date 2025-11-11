<?php 
    // InfinityFree 數據庫配置
    // 請在 InfinityFree Control Panel 查看您的數據庫信息並填入以下內容
    
    $servername = "sqlxxx.infinityfree.com";  // 替換為您的數據庫主機
    $username = "epiz_xxxxx";                  // 替換為您的數據庫用戶名
    $password = "your_password";               // 替換為您的數據庫密碼
    $db_name = "epiz_xxxxx_dbname";           // 替換為您的數據庫名稱
    
    // 創建連接
    $conn = new mysqli($servername, $username, $password, $db_name);
    
    // 檢查連接
    if($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }
    
    // 設定字符集為 UTF-8
    $conn->set_charset("utf8mb4");
    
    // 不輸出任何內容（避免干擾頁面輸出）
?>
