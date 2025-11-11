-- 更新記帳表，添加收入支出類型
ALTER TABLE expenses ADD COLUMN type ENUM('income', 'expense') NOT NULL DEFAULT 'expense' AFTER user_id;

-- 或者如果表不存在，創建完整的表
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('income', 'expense') NOT NULL DEFAULT 'expense',
    category VARCHAR(50) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    expense_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 請在 phpMyAdmin 中執行此 SQL 語句
