<?php
ini_set( 'display_errors', 1 );

try {
    $pdo = new PDO('mysql:host='ホスト名'; dbname='データベース名'; charset=utf8','ユーザー名','パスワード', array(PDO::ATTR_EMULATE_PREPARES => true));
    // echo "成功.<br>";
} catch (PDOException $e) {
    echo "失敗.<br>";
    exit('データベース接続失敗。'.$e->getMessage());
}

# ユーザーテーブルの内容を表示(確認用)
function DB_view_users_table($pdo){
    $sql = 'SELECT * FROM users';//クエリ
    $result = $pdo->query($sql);//実行・結果取得 //出力
    return $result;
}

# ツイートテーブルの内容を表示(確認用)
function DB_view_tweets_table($pdo){
    $sql = 'SELECT * FROM tweets';//クエリ
    $result = $pdo->query($sql);//実行・結果取得 //出力
    return $result;
}

function DB_table_definition($pdo){
    $sql_users = "SHOW FULL COLUMNS FROM users";
    $sql_tweets = "SHOW FULL COLUMNS FROM tweets";
    $result_users = $pdo->query($sql_users);
    $result_tweets = $pdo->query($sql_tweets);
    return array($result_users, $result_tweets);
}

# アカウント新規登録時、同じユーザー名、または、メールアドレスの件数を数える
function DB_search_account($pdo, $name, $email){
    $count_name = 0;
    $count_mail = 0;

    $sql = 'SELECT name FROM users';//クエリ
    $result = $pdo->query($sql);//実行・結果取得 //出力
    while($row_name = $result->fetchObject()){
        if($row_name->name == $name){
            $count_name += 1;
        }
    }

    $sql = 'SELECT email FROM users';//クエリ
    $result = $pdo->query($sql);//実行・結果取得 //出力
    while($row_mail = $result->fetchObject()){
        if($row_mail->email == $email){
            $count_mail += 1;
        }
    }
    return array($count_name, $count_mail);
}

# 仮登録 → 本登録 (formal の変更)
function DB_sertification($pdo, $name){
    $sql = "SELECT id, name FROM users";//クエリ
    $result = $pdo->query($sql);//実行・結果取得 //出力
    while($row = $result -> fetchObject()){
        if($row->name == $name){
            $id = $row->id;
        }
    }

    $sql6 = "UPDATE users SET formal = :formal WHERE id = :id";       // UPDATE文を変数に格納
    $stmt6 = $pdo->prepare($sql6);                                            // 更新する値と該当のIDは空のまま、SQL実行の準備をする
    $params6 = array(':formal' => 1, ':id' => $id);                         // 更新する値と該当のIDを配列に格納する
    $stmt6->execute($params6);
}

# 新規登録
function DB_register_new_account($pdo, $name, $email, $password){
    list($count_name, $count_mail) = DB_search_account($pdo, $name, $email);
    if($count_name==0 && $count_mail==0){
        $sql = "INSERT INTO users (id, name, email, password, formal, created_time) VALUES ('', :name, :email, :password, :formal, NOW())";
        $stmt = $pdo -> prepare($sql);     # 準備
        $params = array(':name'=>$name, ':email'=>$email, 'password'=>$password, ':formal'=>0);
        $flags = $stmt -> execute($params); # 実行
    }
    // else {
    //     header("location: view_new_account.php");
    //     exit;
    // }
}

function DB_register_tweet($pdo, $name, $comment, $file_contents, $filename, $ext){
    // DBに画像保存
    $sql = "INSERT INTO tweets (id, name, comment, image, filename, ext, created_time) VALUES ('', :name, :comment, :image, :filename, :ext, NOW())";
    $stmt = $pdo -> prepare($sql);
    $params = array(':name'=>$name, ':comment'=>$comment,':image'=>$file_contents, ':filename'=>$filename, ':ext'=>$ext);
    $flags = $stmt -> execute($params);
}

// ログイン後のメイン画面で、過去のツイートデータを表示
function DB_all_tweets($pdo){
    $sql = 'SELECT * FROM tweets ORDER BY created_time DESC';
    $result = $pdo->query($sql);
    return $result;
}

// 編集画面で、自分の投稿内容だけ取得する
function DB_my_tweets($pdo){
    session_start();
    $login_user = $_SESSION['username'];
    $sql = "SELECT * FROM tweets WHERE name = \"$login_user\" ORDER BY created_time DESC";
    $result = $pdo->query($sql);
    return $result;
}

// ユーザー名からそのユーザーの全情報を取り出す
function DB_userinfo_from_name($pdo, $name){
    $sql = "SELECT * FROM users WHERE name=\"$name\"";
    $result = $pdo -> query($sql);
    $result = $result -> fetchObject();
    return array($result->password, $result->formal);
}

# 編集画面で、投稿番号に該当する投稿内容を取り出す
function DB_get_selected_tweet($pdo, $tweet_id) {
    $sql = "SELECT * FROM tweets WHERE id = $tweet_id";
    $result = $pdo->query($sql);
    return $result->fetchObject();
}

// 編集画面で、投稿番号に一致する投稿内容を更新する
function DB_tweet_updata($pdo, $id, $name, $comment, $iamge, $filename, $ext){
    $sql = "UPDATE tweets SET name=:name, comment=:comment, image=:image, filename=:filename, ext=:ext WHERE id = :id";       // UPDATE文を変数に格納
    $stmt = $pdo->prepare($sql);                                            // 更新する値と該当のIDは空のまま、SQL実行の準備をする
    $params = array(':name'=>$name, ':comment'=>$comment, ':image'=>$image, ':filename'=>$filename, ':ext'=>$ext, ':id'=>$id);                         // 更新する値と該当のIDを配列に格納する
    $stmt->execute($params);                                                // 更新する値と該当のIDが入った変数をexecuteにセットしてSQLを実行
}

// 編集画面で、投稿番号に一致する投稿内容を削除する
function DB_delete_tweet($pdo, $id) {
    $sql = "DELETE FROM tweets WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $params = array(':id'=>$id);
    $stmt->execute($params);
}

// 検索画面で、sql_where(sqlの条件文)に合致する投稿内容を取り出す
function DB_access($pdo, $sql_where){
    $sql = "SELECT * FROM tweets WHERE $sql_where ORDER BY created_time DESC";
    $result = $pdo -> query($sql);
    return $result;
}

// ユーザーの登録情報変更画面で、ログインしているユーザーの情報を取り出す
function DB_get_user_info($pdo, $uname){
    $sql = "SELECT * FROM users WHERE name = \"$uname\"";
    $result = $pdo -> query($sql);
    return $result->fetchObject();
}

function DB_userinfo_update($pdo, $id, $uname, $email, $new_pass){
    $sql = "UPDATE users SET name=\"$uname\", email=\"$email\", password=\"$new_pass\" WHERE id=\"$id\"";
    $result = $pdo -> query($sql);
}
?>
