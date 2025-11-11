<?php
include('connection.php');

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    
    // 檢查用戶名是否已存在
    $check_sql = "SELECT * FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $error = "用戶名已經存在，請選擇其他用戶名";
    } else {
        // 插入新用戶
        $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $password, $email);
        
        if ($stmt->execute()) {
            echo "<script>
                    alert('註冊成功！請登入');
                    window.location.href='login.php';
                  </script>";
        } else {
            $error = "註冊失敗：" . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>用戶註冊</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div id="form">
        <h1>用戶註冊</h1>
        <?php if(isset($error)) { ?>
            <div style="color: red; margin-bottom: 15px;">
                <?php echo $error; ?>
            </div>
        <?php } ?>
        <form name="form" action="register.php" method="POST">
            <label>用戶名：</label>
            <input type="text" id="username" name="username" required></br></br>
            
            <label>密碼：</label>
            <input type="password" id="password" name="password" required></br></br>
            
            <label>電子郵件：</label>
            <input type="email" id="email" name="email" required></br></br>
            
            <input type="submit" id="btn" value="註冊" name="submit"/>
            
            <p style="text-align: center; margin-top: 15px;">
                已有帳號？<a href="login.php" style="color: #1877f2;">立即登入</a>
            </p>
        </form>
    </div>
    
    <script>
        // 表單驗證
        document.querySelector('form').onsubmit = function(e) {
            var username = document.getElementById('username').value;
            var password = document.getElementById('password').value;
            var email = document.getElementById('email').value;
            
            if (username.length < 3) {
                alert('用戶名至少需要3個字符');
                e.preventDefault();
                return false;
            }
            
            if (password.length < 6) {
                alert('密碼至少需要6個字符');
                e.preventDefault();
                return false;
            }
            
            if (!email.includes('@')) {
                alert('請輸入有效的電子郵件地址');
                e.preventDefault();
                return false;
            }
        };
    </script>
</body>
</html>