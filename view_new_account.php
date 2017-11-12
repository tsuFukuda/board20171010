<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新規登録画面</title>
    <link rel="stylesheet" type="text/css" href="view_new_account.css">
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
        <div class="text_register">新規登録</div>
        <form class="form" action="init.php" method="post">

            <div class="form_1">
                <div class="form_2">
                    <div class="text">ユーザー名</div>
                    <input type="text" name="input_name" autofocus>
                </div>
                <div class="form_2">
                    <div class="text">メールアドレス</div>
                    <input type="email" name="input_mail">
                </div>
                <div class="form_2">
                    <div class="text">パスワード</div>
                    <input type="password" name="input_password">
                </div>
                <div class="form_2">
                    <div class="text">パスワード確認</div>
                    <input type="password" name="input_confirm_password">
                </div>
            </div>
            <button class="decide_login_button" type="submit" name="action" value="decide_new_account">新規登録</button>
        </form>
    </body>
    </html>
