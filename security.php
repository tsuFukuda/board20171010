<?php
// XSS攻撃対策
function h($txt){
    return htmlspecialchars($txt, ENT_QUOTES, 'UTF-8');
}

// SESSIONの確認をする関数
// ユーザー情報の変更の際に使用される
// ユーザー情報の変更を行った際に、ユーザー名 or パスワード が変更された場合には、
// 一旦ログアウトして、再度ログインする形式をとるため、おそらく不要になる。
function confirm_session(){
    session_start();
    if($_SESSION['username']=="" || $_SESSION['username']==NULL){
        header("location: view_login.php");
        break;
    }
}
?>
