<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>歡迎頁面</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .welcome-container {
            text-align: center;
            padding: 20px;
        }
        .user-info {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 600px;
        }
        .welcome-message {
            color: #7daadfff;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .logout-btn {
            background-color: #0056b3;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .logout-btn:hover {
            background-color: #166fe5;
        }
        .login-time {
            color: #666;
            margin-top: 10px;
        }
        .expense-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            margin-right: 10px;
        }
        .expense-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="user-info">
            <h1 class="welcome-message">歡迎回來，<?php echo htmlspecialchars($_SESSION['username']); ?>！</h1>
            <p>您已成功登入系統</p>
            <p class="login-time">登入時間：<?php echo date('Y-m-d H:i:s'); ?></p>
            <div>
                <a href="expense.php" class="expense-btn">💰 記帳系統</a>
                <a href="logout.php" class="logout-btn">登出</a>
            </div>
        </div>
    </div>
</body>
</html>