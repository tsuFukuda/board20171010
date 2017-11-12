<?php
ini_set( 'display_errors', 1 );

try {
    $pdo = new PDO('mysql:host=ホスト名; dbname=データベース名; charset=utf8','ユーザー名','パスワード', array(PDO::ATTR_EMULATE_PREPARES => true));
    // echo "成功.<br>";
} catch (PDOException $e) {
    echo "失敗.<br>";
    exit('データベース接続失敗。'.$e->getMessage());
}

?>
