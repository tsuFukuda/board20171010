<?php
session_start();
if($_SESSION['username']!=NULL){
    unset($_SESSION['username']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ログイン画面</title>
    <link rel="stylesheet" type="text/css" href="view_login.css">
</head>
<body>
    <div class="header">
        <div class="title">IESO掲示板</div>
        <div class="menu">
            <div class="menu_login">
                <a href="view_login.php">ログイン</a>
            </div>
            <div class="menu_register">
                <a href="view_new_account.php">新規登録</a>
            </div>
        </div>
    </div>
    <div class="main">
        <div class="text_login">ログイン</div>
        <form class="form" action="init.php" method="post" name="form">

            <div>
                <div class="form_username">
                    ユーザー名：
                    <input type="text" name="input_name" autofocus>
                </div>
                <div class="form_password">
                    パスワード：
                    <input type="password" name="input_password">
                </div>
            </div>
            <button class="decide_login_button" name="action" value="decide_login">ログイン</button>
        </form>
    </div>
</body>
</html>
