<!DOCTYPE html>
<html>
    <head>
        <title>Update Routing Policy Page</title>
    </head>
    <body>
        <?php
        if (isset($_POST['btnUpdate'])) {
            ob_start();
            session_start();

            require("functions.php");
            updatedb();
            header("location:index.php");
        }

        include('top.php');
        ?>
        <div id="content">
            <div class="wrap">
                <form method="post" action="">
                    <table class="a" cellpadding="0" cellspacing="0" width="99%">
                        <tbody>
                            <tr>
                                <td><div align='center'>Thực hiện phép tính sau để thực hiện việc cập nhật</div></td>
                            </tr>
                            <tr>
                                <td><div align='center'><script type="text/javascript">DrawBotBoot()</script></div></td>
                            </tr>
                            <tr>
                                <td><div align='center'><input type="submit" name="btnUpdate" id="btnUpdate" value="Update" onclick="return ValidBotBoot();"></div></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
                <br/>
            </div>
        </div>
        <?php include('bottom.php'); ?>
    </body>
</html>
