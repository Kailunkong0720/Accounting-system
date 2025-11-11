# InfinityFree 部署指南

## 📦 您的網站信息
- **網址**: https://conankong.lovestoblog.com
- **項目**: PHP 記帳系統

## 🚀 部署步驟

### 1. 獲取 FTP 信息
在 InfinityFree Control Panel 中：
- 找到 "FTP Details"
- 記錄：
  - FTP Hostname
  - FTP Username
  - FTP Password
  - FTP Port (通常是 21)

### 2. 上傳文件

#### 方法 A：使用 InfinityFree 文件管理器（推薦）
1. 在 Control Panel 找到 "File Manager"
2. 點擊進入
3. 進入 `htdocs` 文件夾
4. 上傳所有項目文件（除了以下文件）：
   - ❌ connection.php（稍後手動創建）
   - ❌ .git 文件夾
   - ❌ 技術文件.md
   - ❌ AZURE_DEPLOY.md

#### 方法 B：使用 FTP 客戶端（FileZilla）
1. 下載 FileZilla: https://filezilla-project.org/
2. 使用 FTP 信息連接
3. 上傳所有文件到 `htdocs` 目錄

### 3. 創建數據庫

1. 在 Control Panel 找到 "MySQL Databases"
2. 點擊 "Create Database"
3. 記錄數據庫信息：
   - Database Name
   - Username
   - Password
   - Hostname

### 4. 配置數據庫連接

在文件管理器中：
1. 打開 `connection.php`
2. 修改為您的數據庫信息：
```php
<?php 
    $servername = "您的數據庫主機";
    $username = "您的數據庫用戶名";
    $password = "您的數據庫密碼";
    $db_name = "您的數據庫名稱";
    
    $conn = new mysqli($servername, $username, $password, $db_name);
    if($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
?>
```

### 5. 導入數據庫

1. 在 Control Panel 找到 "phpMyAdmin"
2. 點擊進入
3. 選擇您的數據庫
4. 點擊 "Import" 標籤
5. 上傳 `install.sql` 文件
6. 點擊 "Go" 執行

### 6. 測試網站

訪問：https://conankong.lovestoblog.com

測試功能：
- ✅ 註冊新帳號
- ✅ 登入系統
- ✅ 記帳功能
- ✅ 查看報表
- ✅ 導出功能

## 📝 重要注意事項

### InfinityFree 限制
- 每天有流量限制（但對個人使用足夠）
- 可能有短暫的服務中斷
- 某些 PHP 函數可能被禁用

### 安全建議
1. 修改默認密碼
2. 定期備份數據庫
3. 不要在生產環境使用明文密碼

## 🆘 常見問題

### Q: 網站顯示 404
A: 檢查文件是否上傳到 `htdocs` 目錄

### Q: 數據庫連接失敗
A: 檢查 connection.php 中的數據庫信息是否正確

### Q: 無法上傳文件
A: 使用文件管理器而不是 FTP，更穩定

## 📞 需要幫助？

如有問題，請檢查：
1. InfinityFree 的知識庫
2. 項目的 GitHub Issues
3. 聯繫技術支持

---

**部署成功後，您的記帳系統將 24/7 在線！** 🎉
