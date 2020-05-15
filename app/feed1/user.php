<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table align="center">
        <?php
            include "./redis.php";
            $sql = "select * from user";
            $re = $pdo->query($sql);
            $res = $re->fetchAll();
//            print_r($res);
            foreach($res as $v){
        ?>
        <tr>
            <td>
                <a href="index.php?uid=<?php echo $v['u_id']?>">
                    <?php echo $v['u_name']?>
                </a>
            </td>
        </tr>
        <?php }?>
    </table>
</body>
</html>


