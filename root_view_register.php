<?php
require("DB_connection.php");
require("security.php");
// require("root_DB_connection");
session_start();
$result_users = DB_view_users_table($pdo);
$result_tweets = DB_view_tweets_table($pdo);
list($def_users, $def_tweets) = DB_table_definition($pdo);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>管理人画面</title>
    </head>
    <body>
        <a href="view_main.php">home</a>

        <p>アカウント情報</p>

        <table border="1">
            <tr>
                <th>id</th>
                <th>ユーザー名</th>
                <th>メールアドレス</th>
                <th>パスワード</th>
                <th>登録状況（仮=0, 本=1）</th>
                <th>アカウント作成時間</th>
            </tr>

            <?php while($row = $result_users->fetchObject()){ ?>
                <tr>
                    <td><?=$row->id?></td>
                    <td><?=h($row->name)?></td>
                    <td><?=h($row->email)?></td>
                    <td><?=h($row->password)?></td>
                    <td><?=$row->formal?></td>
                    <td><?=$row->created_time?></td>
                </tr>
            <?php } ?>
        </table>

        <p>DB内ツイート情報</p>

        <table border="1">
            <tr>
                <th>id</th>
                <th>ユーザー名</th>
                <th>コメント</th>
                <th>ファイル名</th>
                <th>拡張子</th>
                <th>投稿時間</th>
            </tr>
            <?php while($row = $result_tweets->fetchObject()){ ?>
                <tr>
                    <td><?=$row->id?></td>
                    <td><?=$row->name?></td>
                    <td><?=h($row->comment)?></td>
                    <td><?=h($row->filename)?></td>
                    <td><?=h($row->ext)?></td>
                    <td><?=$row->created_time?></td>
                </tr>
            <?php } ?>
        </table>


        <p>usersテーブル構造情報</p>

        <table border="1">
            <tr>
                <th>Field</th>
                <th>Type</th>
                <th>Collation</th>
                <th>Null</th>
                <th>Key</th>
                <th>Default</th>
                <th>Extra</th>
                <th>Privileges</th>
                <th>Comment</th>
            </tr>

            <?php while($row = $def_users->fetchObject()){ ?>
                <tr>
                    <td><?=$row->Field?></td>
                    <td><?=$row->Type?></td>
                    <td><?=$row->Collation?></td>
                    <td><?=$row->Null?></td>
                    <td><?=$row->Key?></td>
                    <td><?=$row->Default?></td>
                    <td><?=$row->Extra?></td>
                    <td><?=$row->Privileges?></td>
                    <td><?=$row->Comment?></td>
                </tr>
            <?php } ?>
        </table>


        <p>tweetsテーブル構造情報</p>

        <table border="1">
            <tr>
                <th>Field</th>
                <th>Type</th>
                <th>Collation</th>
                <th>Null</th>
                <th>Key</th>
                <th>Default</th>
                <th>Extra</th>
                <th>Privileges</th>
                <th>Comment</th>
            </tr>

            <?php while($row = $def_tweets->fetchObject()){ ?>
                <tr>
                    <td><?=$row->Field?></td>
                    <td><?=$row->Type?></td>
                    <td><?=$row->Collation?></td>
                    <td><?=$row->Null?></td>
                    <td><?=$row->Key?></td>
                    <td><?=$row->Default?></td>
                    <td><?=$row->Extra?></td>
                    <td><?=$row->Privileges?></td>
                    <td><?=$row->Comment?></td>
                </tr>
            <?php } ?>
        </table>
    </body>
</html>
