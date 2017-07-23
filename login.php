<?php
session_start();
ob_start();
if (isset($_SESSION['wr_id']))
    header("location:index.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Log in</title>
        <link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
    </head>
    <body>

        <form id="signin" class="signup" method="post" action="process_login.php">
            <h1>Log in</h1><br>
            <input type="text" name="user" placeholder="Username">
            <input type="password" name="pass" placeholder="Password">
            <button type="submit" name="submit">Submit</button>
            <p id="fcpy" style="margin-left:50px;">&copy; NetNam Corp - Branch in Ho Chi Minh City, 2014. All Rights Reserved.</p>
        </form>
    </body>
</html>
