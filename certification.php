<!-- 本登録認証用画面(表示されずにすぐ画面遷移) -->
<?php
require("DB_connection.php");
DB_sertification($pdo, $_GET['name']);
header("location: view_login.php");
exit;
?>
