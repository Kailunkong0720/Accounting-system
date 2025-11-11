
<?php 
    include("connection.php");
    include("login.php")
    ?>
    
<html>
    <head>
        <title>Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        
        <div id="form">
            <h1>用戶登入</h1>
            <form name="form" action="login.php" onsubmit="return isvalid()" method="POST">
                <label>用戶名：</label>
                <input type="text" id="user" name="user"></br></br>
                <label>密碼：</label>
                <input type="password" id="pass" name="pass"></br></br>
                <input type="submit" id="btn" value="登入" name="submit"/>
            </form>
            <p style="text-align: center; margin-top: 15px;">
                還沒有帳號？<a href="register.php" style="color: #0ecae7ff;">立即註冊</a>
            </p>
        </div>
        <script>
            function isvalid(){
                var user = document.form.user.value;
                var pass = document.form.pass.value;
                if(user.length=="" && pass.length==""){
                    alert(" Username and password field is empty!!!");
                    return false;
                }
                else if(user.length==""){
                    alert(" Username field is empty!!!");
                    return false;
                }
                else if(pass.length==""){
                    alert(" Password field is empty!!!");
                    return false;
                }
                
            }
        </script>
    </body>
</html>
