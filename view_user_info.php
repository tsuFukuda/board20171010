<?php
require("DB_connection.php");
require("security.php");
session_start();
$result = DB_get_user_info($pdo, $_SESSION['username']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新規登録画面</title>
    <link rel="stylesheet" type="text/css" href="view_user_info.css">
</head>
<body>
    <!-- <a href="view_main.php">home</a> -->
    <!-- <div class="header"> -->
        <div class="title">IESO掲示板</div>
    <!-- </div> -->
    <!-- <div class="main"> -->
        <table class="menu" border="3">
            <tr class="test">
                <td align="center"><a href="view_main.php">ホーム</a></td>
                <td align="center"><a href="view_my_tweets.php">編集</a></td>
                <td align="center"><a href="view_access.php">検索</a></td>
                <td align="center"><a href="view_user_info.php">ユーザー情報変更</a></td>
                <td align="center"><a href="view_login.php">ログアウト</a></td>
                <!-- rootでログインしている時だけ、登録内容確認画面を表示するメニューが出る -->
                <?php if($_SESSION['username']=="root") { ?>
                <td align="center"><a href="root_view_register.php">登録内容確認</a></td>
                <?php } ?>
            </tr>
        </table>
        <div class="text_register">ユーザー情報変更</div>
        <form class="form" action="init.php" method="post">

            <div class="form_1">
                <div class="form_2">
                    <div class="text">ユーザー名</div>
                    <input type="hidden" name="input_old_name" value="<?=h($result->name)?>">
                    <input type="text" name="input_new_name" value="<?=h($result->name)?>" autofocus required>
                </div>
                <div class="form_2">
                    <div class="text">メールアドレス</div>
                    <input type="email" name="input_mail" value="<?=h($result->email)?>" required>
                </div>
                <div class="form_2">
                    <div class="text">今までのパスワード</div>
                    <input type="text" name="input_old_password" required>
                </div>
                <div class="form_2">
                    <div class="text">新しいパスワード</div>
                    <input type="text" name="input_new_password" required>
                </div>
                <div class="form_2">
                    <div class="text">新しいパスワード確認</div>
                    <input type="text" name="input_confirm_password" required>
                </div>
            </div>
            <button class="decide_login_button" type="submit" name="action" value="decide_editing_user_info">登録情報変更</button>
        </form>
    </body>
    </html>
