# Azure MySQL 部署指南

## 前置準備

1. Azure 帳戶設置：
   - 註冊 Azure 免費帳戶
   - 驗證身份並啟用免費額度
   - 設置付款方式（僅用於驗證，不會收費）

2. 創建 MySQL 數據庫：
   - 登入 Azure 門戶 (portal.azure.com)
   - 創建 Azure Database for MySQL 靈活伺服器
   - 選擇基本定價層（免費層級）
   - 配置服務器設置

## 數據庫設置步驟

1. 基本設置：
   ```
   服務器名稱：your-server-name
   位置：選擇最近的區域
   版本：MySQL 8.0
   工作負載類型：開發/測試
   ```

2. 網絡訪問：
   ```
   - 允許公共訪問
   - 添加您的 IP 地址到防火牆規則
   - 配置 SSL 連接
   ```

3. 創建數據庫：
   ```sql
   CREATE DATABASE db2;
   USE db2;
   
   -- 運行 deploy.sql 中的表創建語句
   ```

## 環境變量設置

設置以下環境變量：
```
AZURE_MYSQL_HOST=your-server.mysql.database.azure.com
AZURE_MYSQL_USER=your-username
AZURE_MYSQL_PASSWORD=your-password
AZURE_MYSQL_DATABASE=db2
AZURE_MYSQL_PORT=3306
ENV=production
```

## SSL 設置

1. 下載 SSL 證書：
   - 從 Azure 門戶下載 DigiCertGlobalRootCA.crt.pem
   - 放置在專案的 ssl 目錄中

2. 配置 SSL 連接：
   - 確保 config.php 中包含 SSL 證書路徑
   - 測試 SSL 連接

## 監控和維護

1. 使用 Azure 監控：
   - 監控數據庫性能
   - 設置告警
   - 查看日誌

2. 備份策略：
   - 啟用自動備份
   - 設置備份保留期
   - 測試還原過程

## 成本控制

1. 監控使用量：
   - 追蹤數據庫存儲使用量
   - 監控計算資源使用
   - 設置成本告警

2. 優化建議：
   - 使用連接池
   - 優化查詢
   - 適時縮放資源