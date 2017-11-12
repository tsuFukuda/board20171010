<?php
# エンコーディング指定
mb_internal_encoding("UTF-8");

// 外部ファイル読み込み
require("DB_connection.php");

// header関数
function headerTo($root){
    header("location: $root");
    exit;
}

// 認証用メール送信
function send_email($name, $email){
    mb_language("Japanese");
    mb_internal_encoding("UTF-8");

    $to      = 'メールアドレス';
    $subject = '本登録の確認';
    $message = "$name 様、この度はご登録ありがとうございます。\nまだ登録が完了していません。\n以下のURLより認証を行なってください。\n URL?name=$name ";
    $headers = 'From: メールアドレス' . "\r\n";

    $mail = mb_send_mail($to, $subject, $message, $headers);
    header('Content-Type: text/html; charset=UTF-8');
    // 確認用
    if($mail){
        print(mb_convert_encoding("メールを送信しました", "UTF-8"));
    } else {
        echo "メールの送信に失敗しました";
    };
}

// 今投稿された内容から、ファイルに関する情報を返す
function get_file_datas($fname, $tmp_name){
    # 相対パス
    $path = "images/".$fname;
    move_uploaded_file($tmp_name, $path);
    $file_contents = file_get_contents($path);
    $ext = pathinfo($path, PATHINFO_EXTENSION);     # pathinfo で様々な情報が得られる
    return array($file_contents, $fname, $ext);
}

//
function register_session($username){
    session_start();
    $_SESSION['username'] = $username;
}

function create_sql($number, $dname, $year_from, $month_from, $day_from, $year_to, $month_to, $day_to, $file){
    $sql_num = ($number != "") ? "AND id = $number " : "";
    $sql_dname = ($dname != "") ? "AND displayname = $dname " : "";

    $sql_time = "";
    if($year_from!="" && $month_from!="" && $day_from!="" && $year_to!="" && $month_to!="" && $day_to!=""){
        if(checkdate($month_from, $day_from, $year_from) === true && checkdate($month_to, $day_to, $year_to) == true){
            $time_from = $year_from."-".str_pad($month_from, 2, 0, STR_PAD_LEFT)."-".str_pad($day_from, 2, 0, STR_PAD_LEFT);
            $time_to = $year_to."-".str_pad($month_to, 2, 0, STR_PAD_LEFT)."-".str_pad($day_to, 2, 0, STR_PAD_LEFT);
            $sql_time = "AND created_time BETWEEN $time_from AND $time_to ";
        }
    }

    switch ($file) {
        case "0":
            $sql_file = "AND image IS NULL ";
            break;
        case "1":
            $sql_file = "AND image IS NOT NULL ";
            break;
        default:
            $sql_file = "";
            break;
    }

    $sql = $sql_num.$sql_dname.$sql_time.$sql_file;
    if($sql != ""){
        $sql = preg_replace('/AND/', '', $sql, 1);
    }
    return $sql;
}



$root_login = $_POST['action'];
switch($root_login) {

    case "decide_login":
        $username = "";
        $password = "";
        $name = $_POST['input_name'];
        $password = $_POST['input_password'];
        list($db_password, $db_formal) = DB_userinfo_from_name($pdo, $name);
        register_session($name);
        if($name!="" && $password!="" && $password == $db_password && $db_formal=="1"){
            headerTo("view_main.php");
        } else {
            session_start();
            unset($_SESSION['username']);
            headerTo("view_login.php");
        }
        break;

    case "move_new_account":
        headerTo("view_new_account.php");
        break;

    case "move_login":
        headerTo("view_login.php");
        break;

    case "move_home":
        headerTo("view_main.php");
        break;

    case "move_edit":
        headerTo("view_my_tweets.php");
        break;

    case "decide_new_account":
        $name = $_POST['input_name'];
        $email = $_POST['input_mail'];
        $password = $_POST['input_password'];
        $confirm_password = $_POST['input_confirm_password'];
        // 入力情報が不足・パスワードの差異が生じた場合は新規登録画面に戻る
        if($name=="" || $email=="" || $password=="" || $confirm_password=="" || $password!=$confirm_password){
            headerTo("view_new_account.php");
        }
        DB_register_new_account($pdo, $name, $email, $password);
        send_email($name, $email);
        break;

    case "contribution":
        # データを変数に格納
        session_start();
        list($file_contents, $filename, $ext) = get_file_datas($_FILES['input_file']['name'], $_FILES['input_file']['tmp_name']);
        DB_register_tweet($pdo, $_SESSION['username'], $_POST['input_comment'], $file_contents, $filename, $ext);
        session_write_close();
        headerTo("view_main.php");
        break;

    case "decide_editing":
        session_start();
        list($file_contents, $filename, $ext) = get_file_datas($_FILES['input_file']['name'], $_FILES['input_file']['tmp_name']);
        DB_tweet_updata($pdo, $_POST['tweet_id'], $_SESSION['username'], $_POST['input_comment'], $file_get_contents, $filename, $ext);
        headerTo("view_my_tweets.php");
        break;

    case "delete_tweet":
        DB_delete_tweet($pdo, $_POST['tweet_id']);
        headerTo("view_my_tweets.php");
        break;

    case "decide_editing_user_info":
        $old_name = $_POST['input_old_name'];
        $new_name = $_POST['input_new_name'];
        $email = $_POST['input_mail'];
        $old_pass = $_POST['input_old_password'];
        $new_pass = ($_POST['input_new_password'] == $_POST['input_confirm_password']) ? $_POST['input_new_password'] : headerTo("view_user_info.php");
        $result = DB_userinfo_from_name($pdo, $old_name);
        $registerd_id = $result->id;
        $registered_pass = $result->password;

        if($old_pass == $registered_pass){
            DB_userinfo_update($pdo,  $registerd_id, $new_name, $email, $new_pass);
        }
        session_start();
        $_SESSION['username'] = $new_name;
        if($old_name != $new_name || $old_pass != $new_pass){
            headerTo("view_login.php");
            break;
        }
        headerTo("view_main.php");
        break;

    default:
        print("ERROR!");
}


?>
