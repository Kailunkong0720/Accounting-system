# 記帳系統安裝說明

## 安裝步驟

### 1. 創建記帳數據表

1. 打開 phpMyAdmin (http://localhost/phpmyadmin)
2. 選擇您的數據庫 `db2`
3. 點擊「SQL」標籤
4. 複製以下 SQL 語句並執行：

```sql
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    expense_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

或者直接執行 `expense_table.sql` 文件。

### 2. 訪問記帳系統

1. 登入您的帳號
2. 在歡迎頁面點擊「💰 記帳系統」按鈕
3. 開始記錄您的支出！

## 功能說明

### 記帳功能
- **新增記帳**: 記錄支出的類別、金額、日期和說明
- **查看記錄**: 查看所有記帳記錄，按日期排序
- **刪除記錄**: 刪除錯誤的記帳記錄
- **統計分析**: 查看總支出和各類別支出統計

### 支出類別
- 🍔 飲食
- 🚗 交通
- 🎮 娛樂
- 🛍️ 購物
- 💡 帳單
- 📌 其他

## 注意事項

- 每個用戶只能看到和管理自己的記帳記錄
- 記帳金額支持小數點後兩位
- 刪除記錄前會有確認提示
- 數據會自動按日期排序顯示

## 未來可以添加的功能

- 收入記錄
- 月度/年度報表
- 圖表分析
- 預算設定
- 導出 Excel
- 標籤功能
