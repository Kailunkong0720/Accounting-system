# PHP 記帳系統

一個功能完整的個人記帳系統，支持收入和支出管理、月度報表、圖表分析和數據導出。

## ✨ 主要功能

- 👤 用戶註冊和登入系統
- 💰 收入和支出記錄
- 📊 月度報表統計
- 📈 圖表數據分析（Chart.js）
- 📥 Excel 導出功能
- 📱 響應式設計（支持手機、平板、電腦）
- 🔒 用戶數據隔離

## 🛠️ 技術棧

- **後端**: PHP 7.0+
- **數據庫**: MySQL 5.7+
- **前端**: HTML5, CSS3, JavaScript
- **圖表**: Chart.js
- **伺服器**: Apache

## 📋 系統需求

- XAMPP (或 LAMP/WAMP/MAMP)
- PHP 7.0 或更高版本
- MySQL 5.7 或更高版本
- 現代瀏覽器（Chrome, Firefox, Safari, Edge）

## 🚀 安裝步驟

### 1. 克隆專案

```bash
git clone https://github.com/你的用戶名/phplogin.git
cd phplogin
```

### 2. 移動到 Web 伺服器目錄

將項目文件夾移動到：
- **Windows (XAMPP)**: `C:\xampp\htdocs\phplogin\`
- **Mac (MAMP)**: `/Applications/MAMP/htdocs/phplogin/`
- **Linux (LAMP)**: `/var/www/html/phplogin/`

### 3. 啟動服務

打開 XAMPP Control Panel，啟動：
- ✅ Apache
- ✅ MySQL

### 4. 創建數據庫

訪問 `http://localhost/phpmyadmin`，執行以下 SQL：

```sql
-- 創建數據庫
CREATE DATABASE IF NOT EXISTS db2 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE db2;

-- 創建用戶表
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 創建記帳表
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('income', 'expense') NOT NULL DEFAULT 'expense',
    category VARCHAR(50) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    expense_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5. 配置數據庫連接

編輯 `connection.php`，根據您的環境修改：

```php
$servername = "localhost";
$username = "root";         // 您的 MySQL 用戶名
$password = "";            // 您的 MySQL 密碼
$db_name = "db2";         // 數據庫名稱
```

### 6. 訪問系統

在瀏覽器中打開：
```
http://localhost/phplogin/
```

## 📱 使用說明

### 註冊新用戶
1. 訪問首頁，點擊「立即註冊」
2. 填寫用戶名（至少 3 個字符）、密碼（至少 6 個字符）和電子郵件
3. 註冊成功後自動跳轉到登入頁面

### 記錄收支
1. 登入後點擊「💰 記帳系統」
2. 選擇類型（收入/支出）
3. 選擇類別、輸入金額和日期
4. 點擊「新增記錄」

### 查看報表
- **記帳記錄**: 查看所有收支記錄
- **月度報表**: 查看每月收入、支出、結餘統計
- **圖表分析**: 查看可視化圖表

### 導出數據
點擊右上角「📊 導出 Excel」按鈕，下載 CSV 文件

## 📂 項目結構

```
phplogin/
├── connection.php          # 數據庫連接配置
├── index.php              # 登入頁面
├── register.php           # 註冊頁面
├── login.php              # 登入處理
├── logout.php             # 登出處理
├── welcome.php            # 歡迎頁面
├── expense.php            # 記帳系統主頁面
├── export.php             # Excel 導出功能
├── style.css              # 全局樣式表
├── expense_table.sql      # 數據庫表結構（舊版）
├── update_expense_table.sql  # 數據庫更新腳本
└── README.md              # 本文件
```

## 🎨 功能截圖

### 登入頁面
- 現代化的漸層設計
- 響應式表單

### 記帳系統
- 清晰的統計卡片（總收入、總支出、結餘、記帳筆數）
- 直觀的記錄表單
- 類別統計

### 月度報表
- 每月收支對比
- 結餘計算

### 圖表分析
- 月度收支趨勢柱狀圖
- 支出類別圓餅圖

## 🔐 安全建議

⚠️ **重要**: 當前版本的密碼是明文存儲，不適合生產環境使用。

### 改進建議：

1. **密碼加密**
```php
// 註冊時
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 登入驗證時
if (password_verify($password, $hashed_password)) {
    // 密碼正確
}
```

2. **使用 HTTPS**（生產環境）

3. **定期備份數據庫**

4. **設置強密碼規則**

## 🐛 常見問題

### Q: 為什麼頁面顯示空白？
A: 檢查 Apache 服務是否啟動，文件是否在正確的目錄。

### Q: Table doesn't exist 錯誤
A: 執行安裝步驟中的 SQL 語句創建數據表。

### Q: 無法連接數據庫
A: 檢查 MySQL 服務是否啟動，`connection.php` 配置是否正確。

### Q: 導出的 CSV 中文亂碼
A: 使用 Excel 時選擇 UTF-8 編碼，或使用 Google Sheets 打開。

## 📝 待改進功能

- [ ] 密碼加密存儲
- [ ] 忘記密碼功能
- [ ] 記帳記錄編輯功能
- [ ] 預算設定和提醒
- [ ] 更多圖表類型
- [ ] 搜索和篩選功能
- [ ] 深色模式
- [ ] 多語言支持

## 📄 授權

本項目僅供學習和個人使用。

## 👨‍💻 貢獻

歡迎提交 Issue 和 Pull Request！

## 📧 聯絡

如有問題，請提交 Issue。

---

**版本**: v1.0.0  
**最後更新**: 2025-11-11
