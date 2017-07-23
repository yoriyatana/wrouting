<!DOCTYPE html>
<html>
    <head>
        <title>Logs</title>
    </head>
    <body>
        <?php include('top.php'); ?>
        <?php include('connect_db.php'); ?>
        <div id="content">
            <div class="wrap">
                    <table class="t" cellpadding="0" cellspacing="0" width="99%">
                        <tbody>
                            <tr>
                                <th align="center">Timestamp</th>
                                <th align="center">Message</th>
                                <th align="center">User</th>
                            </tr>
                            <?php
                            $sql = "SELECT * FROM `logs` ORDER BY `log_timestamp` DESC";
                            $query = mysql_query($sql);
                            while ($row = mysql_fetch_array($query)) {
                            ?>
                            <tr>
                                <td><?php echo $row['log_timestamp']; ?>1</td>
                                <td><?php echo $row['log_message']; ?></td>
                                <td><?php echo $row['use_id']; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php include('bottom.php'); ?>
    </body>
</html>


