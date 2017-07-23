<?php
ob_start();
session_start();
if (!isset($_SESSION['wr_id']))
    header("location:login.php");
?>
<!doctype html>
<html>
    <head>
        <title>Register Page</title>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
    </head>

    <body>
        <?php
        include("connect_db.php");

        if (isset($_POST['submit'])) {
            $sql = "INSERT INTO logs VALUES ('',now(),'User {$_SESSION['wr_uname']} thực hiện tạo user trên hệ thống.','{$_SESSION['wr_id']}')";
            mysql_query($sql);
            if (!empty($_POST['user']) && !empty($_POST['pass']) && $_POST['re-pass'] == $_POST['pass']) {
                $sql_str = "SELECT * FROM users WHERE use_user = '{$_POST['user']}'";
                $query = mysql_query($sql_str) or die(mysql_error());
                if (mysql_num_rows($query) == 0) {
                    $password = md5($_POST['pass']);
                    $sql_str = "INSERT INTO users VALUES ('','{$_POST['user']}','{$password}','{$_POST['full']}','0','1','','1')";
                    mysql_query($sql_str) or die(mysql_error());
                    $sql = "INSERT INTO logs VALUES ('',now(),'User {$_POST['user']} đã được tạo trên hệ thống.','{$_SESSION['wr_id']}')";
                    mysql_query($sql);
                    echo "YOUR REGISTRATION IS COMPLETED...";
                } else {
                    $sql = "INSERT INTO logs VALUES ('',now(),'User {$_POST['user']} đã tồn tại trên hệ thống.','{$_SESSION['wr_id']}')";
                    mysql_query($sql);
                    echo "SORRY...There is an existing account associated with this username.";
                }
            } else {
                $sql = "INSERT INTO logs VALUES ('',now(),'Có lỗi xảy ra trong quá trình tạo user.','{$_SESSION['wr_id']}')";
                mysql_query($sql);
                echo "SORRY...You have somethings wrong!";
            }
        }
        ?>
        <form id="signup" class="signup" method="post" action="">
            <h1>Registration form</h1>
            <input type="text" name="user" maxlength="16" placeholder="Choose your username" required="">
            <input type="password" name="pass" placeholder="Choose your password" required="">
            <input type="password" name="re-pass" placeholder="Confirm password" required="">
            <input type="text" name="full" maxlength="50" placeholder="Fill your fullname" required="">
            <button type="submit" name="submit">Submit</button>
            <p id="fcpy" style="margin-left:50px;">&copy; NetNam Corp - Branch in Ho Chi Minh City, 2014. All Rights Reserved.</p>
        </form>


    </body>

</html>