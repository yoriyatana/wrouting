<!DOCTYPE html>
<html>
    <head>
        <title>Change Routing Policy Page</title>
    </head>
    <body>
        <?php include('top.php'); ?>
        <div id="content">
            <div class="wrap">
                <div id="SearchArea">
                    <table class="a" cellpadding="0" cellspacing="0" width="99%">
                        <tbody>
                            <tr>
                                <td><div align='center'><label for="txtPrefix">Prefix:&nbsp;&nbsp;</label>
                                <input type="text" name="txtPrefix" id="txtPrefix"></div></td>
                            </tr>
                            <tr>
                                <td><div align='center'><script type="text/javascript">DrawBotBoot()</script><br></div></td>
                            </tr>
                            <tr>
                                <td><div align='center'><input type="button" name="btnSearch" id="btnSearch" value="Search"></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                <div id="SearchResultArea">
                </div>
            </div>
        </div>
        <?php include('bottom.php'); ?>
    </body>
</html>


