<?php 
    // InfinityFree 數據庫配置
    $servername = "sql100.infinityfree.com";
    $username = "if0_40388267";
    $password = "Kailun5065";  // vPanel 密碼
    $db_name = "if0_40388267_accounting";
    
    $conn = new mysqli($servername, $username, $password, $db_name);
    if($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }
    
    // 設定字符集
    $conn->set_charset("utf8mb4");
?>